<?php
/**
 * Joomleague
 * 
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTreetomatchs extends JLGView
{

	public function display($tpl=null)
	{
		if ($this->getLayout() == 'editlist')
		{
			$this->_displayEditlist($tpl);
			return;
		}

		if ($this->getLayout()=='default')
		{
			$this->_displayDefault($tpl);
			return;
		}
		parent::display($tpl);
	}

	function _displayEditlist($tpl)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$baseurl = Uri::root();
		
		
		$document = Factory::getDocument();
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/multiselect.js');
		
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		$node_id = $app->getUserState($option . 'node_id');
		
		$uri = Uri::getInstance();

		$treetomatchs = $this->get('Data');
		$total = $this->get('Total');
		$model = $this->getModel();

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project_id = $app->getUserState($option.'project');
		$projectws 	= $mdlProject->getItem($project_id);
		
		// @todo check!
		// renamed "node" to "treetonode" as the output for 
		// nodews was returning a string and it seems the view was expecting an id 
		// so a object had to be returned
		
		$nodews = $this->get('Data','treetonode');
		// build the html select list for node assigned matches
		$ress = array();
		$res1 = array();
		$notusedmatches = array();

		if ($ress = $model->getNodeMatches($node_id))
		{			
			$matcheslist=array();
			foreach($ress as $res)
			{				
				if(empty($res->info))
				{
					$node_matcheslist[] = HTMLHelper::_ ( 'select.option',$res->value,$res->text);
				}
				else
				{
					$node_matcheslist[] = HTMLHelper::_ ( 'select.option',$res->value,$res->text.' ('.$res->info.')');
				}
			}

			$lists['node_matches'] = HTMLHelper::_ ( 'select.genericlist',$node_matcheslist, 'node_matcheslist[]',
	' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($ress)).'"',
				'value',
				'text',false,'multiselect_to');
		}
		else
		{
			$lists['node_matches']= '<select name="node_matcheslist[]" id="multiselect_to" style="width:250px; height:300px;" class="multiselect_to" multiple="true" size="10"></select>';
		}

		if ($ress1 = $model->getMatches())
		{
			if ($ress = $model->getNodeMatches($node_id))
			{
				foreach ($ress1 as $res1)
				{
					$used=0;
					foreach ($ress as $res)
					{
						if ($res1->value == $res->value){$used=1;}
					}

					if ($used == 0 && !empty($res1->info)){
						$notusedmatches[]=HTMLHelper::_ ( 'select.option',$res1->value,$res1->text.' ('.$res1->info.')');
					}
					elseif($used == 0 && empty($res1->info))
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text);
					}
				}
			}
			else
			{
				foreach ($ress1 as $res1)
				{
					if(empty($res1->info))
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text);
					}
					else
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text.' ('.$res1->info.')');
					}
				}
			}
		}
		else
		{
			$app->enqueueMessage('ERROR_CODE','<br />'.Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ADD_MATCH'),'warning');
		}
		
		//build the html select list for matches
		if (count($notusedmatches) > 0)
		{
			$lists['matches'] = HTMLHelper::_ ( 'select.genericlist', $notusedmatches,
				'matcheslist[]',
	' style="width:250px; height:300px;" class="inputbox" multiple="true" size="'.min(30,count($notusedmatches)).'"',
			'value',
			'text',false,'multiselect');
		}
		else
		{
			$lists['matches'] = '<select name="matcheslist[]" id="multiselect" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}

		unset($res);
		unset($res1);
		unset($notusedmatches);

		$this->user = Factory::getUser();
		$this->lists = $lists;
		$this->treetomatchs = $treetomatchs;
		$this->projectws = $projectws;
		$this->nodews = $nodews;
		
		// @todo fix!
		/* $this->pagination = $pagination; */
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}

	function _displayDefault($tpl)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$uri 	= Uri::getInstance();
		$match	= $this->get('Data');
		$total	= $this->get('Total');
		$pagination = $this->get('Pagination');

		$model = $this->getModel();
		
		$project_id = $app->getUserState($option.'project');
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$projectws 	= $mdlProject->getItem($project_id);
		
		// @todo: fix
		$nodews = $this->get('Data','treetonode');

		$this->match = $match;
		$this->projectws = $projectws;
		$this->nodews = $nodews;
		$this->total = $total;
		$this->pagination = $pagination;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}
}
