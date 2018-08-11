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

	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		if($this->getLayout() == 'editlist')
		{
			$this->_displayEditlist($tpl);
			return;
		}

		if($this->getLayout() == 'default')
		{
			$this->_displayDefault($tpl);
			return;
		}
		parent::display($tpl);
	}


	function _displayEditlist($tpl)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$uri = Uri::getInstance();
		$baseurl = Uri::root();

		$document = Factory::getDocument();
		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/multiselect.js');

		$option = $jinput->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$node_id = $app->getUserState($option.'node_id');


		/*
		$treetomatchs = $this->get('Data');
		$total = $this->get('Total');
		*/

		$model = $this->getModel();

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		/*
		 $match = $this->get('Data');
		 $total = $this->get('Total');
		 $pagination = $this->get('Pagination');
		*/

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlTreenode = BaseDatabaseModel::getInstance('treetonode','JoomleagueModel');
		$node = $mdlTreenode->getItem($node_id);

		// build the html select list for node assigned matches
		$ress = array();
		$res1 = array();
		$notusedmatches = array();

		if($ress = $model->getNodeMatches($node_id))
		{
			$matcheslist = array();
			foreach($ress as $res)
			{
				if(empty($res->info))
				{
					$node_matcheslist[] = HTMLHelper::_ ( 'select.option',$res->value,$res->text);
				}
				else
				{
					$node_matcheslist[] = HTMLHelper::_ ( 'select.option',$res->value,$res->text . ' (' . $res->info . ')');
				}
			}

			$lists['node_matches'] = HTMLHelper::_ ( 'select.genericlist',$node_matcheslist,'node_matcheslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . min(30,count($ress)) . '"','value','text',false,
					'multiselect_to');
		}
		else
		{
			$lists['node_matches'] = '<select name="node_matcheslist[]" id="multiselect_to" style="width:250px; height:300px;" class="multiselect_to" multiple="true" size="10"></select>';
		}

		if($ress1 = $model->getMatches())
		{
			if($ress = $model->getNodeMatches($node_id))
			{
				foreach($ress1 as $res1)
				{
					$used = 0;
					foreach($ress as $res)
					{
						if($res1->value == $res->value)
						{
							$used = 1;
						}
					}

					if($used == 0 && ! empty($res1->info))
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text . ' (' . $res1->info . ')');
					}
					elseif($used == 0 && empty($res1->info))
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text);
					}
				}
			}
			else
			{
				foreach($ress1 as $res1)
				{
					if(empty($res1->info))
					{
						$notusedmatches[] = HTMLHelper::_ ( 'select.option',$res1->value,$res1->text);
					}
					else
					{
						$notusedmatches[] =HTMLHelper::_ ( 'select.option',$res1->value,$res1->text . ' (' . $res1->info . ')');
					}
				}
			}
		}
		else
		{
		    $app->enqueueMessage( Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ADD_MATCH'),'ERROR_CODE');
		}

		// build the html select list for matches
		if(count($notusedmatches) > 0)
		{
			$lists['matches'] = HTMLHelper::_ ( 'select.genericlist',$notusedmatches,'matcheslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . min(30,count($notusedmatches)) . '"','value',
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
		//$this->treetomatchs = $treetomatchs;
		$this->project = $project;
		$this->node = $node;

		// @todo fix!
		/* $this->pagination = $pagination; */
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}


	function _displayDefault($tpl)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$uri = Uri::getInstance();

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		/*
		$match = $this->get('Data');
		$total = $this->get('Total');
		$pagination = $this->get('Pagination');
		*/

		$model = $this->getModel();

		$project_id = $app->getUserState($option.'project');
		$node_id 	= $app->getUserState($option.'node_id');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlTreenode = BaseDatabaseModel::getInstance('treetonode','JoomleagueModel');
		$node = $mdlTreenode->getItem($node_id);

		//$this->match = $match;
		$this->project = $project;
		$this->node = $node;
		$this->total = $total;
		$this->request_url = $uri->toString();

		parent::display($tpl);
	}
}
