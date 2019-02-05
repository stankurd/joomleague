<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Joomleague Common Controller
 */
class JoomleagueController extends JLGControllerAdmin
//class JoomleagueController extends AdminController

{

	public function __construct($config = array())
	{
		parent::__construct($config);

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		// Project_id
		$pid = $input->get('pid',array(),'array');
		ArrayHelper::toInteger($pid);

		if(empty($pid))
		{
			$pid = $app->getUserState($option . 'project',false);
		}
		else
		{
			$pid = $pid[0];
			$app->setUserState($option . 'project',$pid);
		}

		// Roundid
		$rid = $input->get('rid',array(),'array');
		ArrayHelper::toInteger($rid);
		if(empty($rid))
		{
			$rid = $app->getUserState($option . 'round',false);
			if (empty($rid)) {
				$rid = $app->getUserState($option . 'round_id',false);
			}
		}
		else
		{
			$rid = $rid[0];
			$app->setUserState($option . 'round',$rid);
			$app->setUserState($option . 'round_id',$rid);
		}

		// Seasonid
		$sid = $input->get('seasonid',array(),'array');
		ArrayHelper::toInteger($sid);
		if(empty($sid))
		{
			$sid = $app->getUserState($option . 'seasonnav',false);
		}
		else
		{
			$sid = $sid[0];
			$app->setUserState($option . 'seasonnav',$sid);
		}

		// Sporttype_id
		$stid = $input->get('stid',array(),'array');
		ArrayHelper::toInteger($stid);
		if(empty($stid))
		{
			$stid = $app->getUserState($option.'sportstypes',false);
		}
		else
		{
			$stid = $stid[0];
			$app->setUserState($option.'sportstypes',$stid);
		}

		// Teamid
		$tid = $input->get('tid',array(),'array');
		ArrayHelper::toInteger($tid);
		if(empty($tid))
		{
			$tid = $app->getUserState($option . 'project_team_id',false);
		}
		else
		{
			$tid = $tid[0];
			$app->setUserState($option . 'project_team_id',$tid);
		}

		/*
		$model = JModelLegacy::getInstance('joomleague','JoomleagueModel');
		$model->setCurrentProjectData($pid,$rid,$sid,$stid,$tid);
		*/
	}


	public function display($cachable = false,$urlparams = false)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		// display the left menu only if hidemainmenu is not true
		$hidemainmenu = $input->get('hidemainmenu',false);

		// display left menu
		$viewName = $this->input->getCmd('view','');
		$layoutName = $this->input->getCmd('layout','default');


		if($hidemainmenu)
		{
			$show_menu = false;
		}
		else
		{
			$show_menu = true;
		}


		// define variables
		if ($viewName == 'person' && $layoutName == 'assignperson') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}

		// match views
		if ($viewName == 'match' && $layoutName == 'editevents') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}
		if ($viewName == 'match' && $layoutName == 'editeventsbb') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}
		if ($viewName == 'match' && $layoutName == 'editlineup') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}
		if ($viewName == 'match' && $layoutName == 'editreferees') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}
		if ($viewName == 'match' && $layoutName == 'editstats') {
			$show_menu = false;
			$input->set('hidemainmenu',true);
		}

		if($viewName == '' && $layoutName == 'default')
		{
			$app->input->getCmd('view','projects');
			$viewName = "projects";
		}
		if($show_menu)
		{
			$this->ShowMenu();
		}
		else
		{
			$pid = $app->input->getVar('pid',array(
					0
			),'','array');
			if($pid[0] > 0)
			{
				$option = $app->input->getCmd('option');
				$app->setUserState($option . 'project',$pid[0]);
			}
		}
		$document = Factory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView($viewName,$viewType);
		$view->setLayout($layoutName);
		$model = $this->getModel($viewName);

		$view->setModel($model,true);
		$view->display();
		parent::display($cachable,$urlparams);
	}

	private function ShowMenu()
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView('joomleague',$viewType);
		if($model = $this->getModel('project'))
		{
			$model->setId($app->getUserState($option.'project',0));
			$app->getUserState($option . 'project',0);
			$view->setModel($model,true);
		}
		$view->display();
	}

	private function ShowMenuExtension()
	{
		$document = Factory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView('joomleague',$viewType);
		$view->setLayout('extension');
		$view->display();
	}

	private function ShowMenuFooter()
	{
		$document = Factory::getDocument();
		$viewType = $document->getType();
		$view = $this->getView('joomleague',$viewType);
		$view->setLayout('footer');
		$view->display();
	}
}
