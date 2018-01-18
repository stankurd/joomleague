<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// needed for data in view.html.php
require_once JPATH_ADMINISTRATOR . '/components/com_joomleague/models/project.php';
require_once JPATH_ADMINISTRATOR . '/components/com_joomleague/models/divisions.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * HTML View class
 */
class JoomleagueViewProjectteamform extends BaseHtmlView
{
	protected $form;
	protected $item;
	protected $state;
	protected $return_page;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$user = Factory::getUser();

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->return_page = $this->get('ReturnPage');
		
		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_joomleague');
		}
		else
		{
			$authorised = $this->item->params->get('access-edit');
		}
		
		if ($authorised !== true)
		{
			$app->enqueueMessage( Text::_('JERROR_ALERTNOAUTHOR'),'error');
		
			return false;
		}
		
		$project_id 	= $input->getInt('pid',0);
		$projectteam_id = $input->getInt('ptid',0);
		$uri = Uri::getInstance();
		$user = Factory::getUser();

		$model = $this->getModel();
		$lists = array();

		$mdlProject = BaseDatabaseModel::getInstance('Project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		// build the html select list for days of week
		if($trainingData = $model->getTrainingData($projectteam_id,$project_id))
		{
			$daysOfWeek = array(
					0 => Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT'),
					1 => Text::_('COM_JOOMLEAGUE_GLOBAL_MONDAY'),
					2 => Text::_('COM_JOOMLEAGUE_GLOBAL_TUESDAY'),
					3 => Text::_('COM_JOOMLEAGUE_GLOBAL_WEDNESDAY'),
					4 => Text::_('COM_JOOMLEAGUE_GLOBAL_THURSDAY'),
					5 => Text::_('COM_JOOMLEAGUE_GLOBAL_FRIDAY'),
					6 => Text::_('COM_JOOMLEAGUE_GLOBAL_SATURDAY'),
					7 => Text::_('COM_JOOMLEAGUE_GLOBAL_SUNDAY')
			);
			$dwOptions = array();
			foreach($daysOfWeek as $key=>$value)
			{
				$dwOptions[] = HTMLHelper::_('select.option',$key,$value);
			}
			foreach($trainingData as $td)
			{
				$lists['dayOfWeek'][$td->id] = HTMLHelper::_('select.genericlist',$dwOptions,'dw_' . $td->id,'class="input-medium"','value','text',
						$td->dayofweek);
			}
			unset($daysOfWeek);
			unset($dwOptions);
		}
		
		if($project->project_type == 'DIVISIONS_LEAGUE') // No divisions
		{
			// build the html options for divisions
			$division[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
			$mdlDivisions = BaseDatabaseModel::getInstance('divisions','JoomLeagueModel');
			if($res = $mdlDivisions->getDivisions($project_id))
			{
				$division = array_merge($division,$res);
			}
			$lists['divisions'] = $division;

			unset($res);
			unset($divisions);
		}
		

		$extended = $this->getExtended($this->item->extended,'projectteam');
		$this->extended = $extended;

		// $this->imageselect = $imageselect;
		$this->project = $project; 
		$this->lists = $lists;
		$this->trainingData = $trainingData;

		parent::display($tpl);
	}

	function getExtended($data='', $file, $format='ini')
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		$xmlfile = JLG_PATH_ADMIN.'/assets/extended/'.$file.'.xml';
		// extension management
		$extensions = JoomleagueHelper::getExtensions($input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/admin';
			//General extension extended xml
			$file = $JLGPATH_EXTENSION.'/assets/extended/'.$file.'.xml';
			if(file_exists(JPath::clean($file))) {
				$xmlfile = $file;
				break; //first extension file will win
			}
		}
	
		if (is_array($data)) {
			$data = json_encode($data);
		}
			
		// Convert the extended field to an array.
		$registry = new Registry;
		$registry->loadString($data);
	
		/*
		 * extended data
		*/
		$extended = Form::getInstance('extended', $xmlfile,array('control'=> 'extended'),false);
		$extended->bind($registry);
	
		return $extended;
	}
}
