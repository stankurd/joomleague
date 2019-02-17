<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Access\Rules;

defined('_JEXEC') or die;

/**
 * Settings Controller
 */
class JoomleagueControllerSettings extends JoomleagueController
{

	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'apply', 'save' );
	}

	public function edit()
	{
		$input = $this->input;
		$input->set('hidemainmenu', 0);
		$input->set('view', 'settings');
		parent::display();
	}

	public function save()
	{
		// Check for request forgeries
		Session::checkToken() or die( 'COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN' );

		// Sanitize
		$app = Factory::getapplication();
		$input = $app->input;
		$task	= $input->get('task');
		$data = $input->post->getArray();
		$data['option'] = $this->option;
		$params = $input->post->get('params', array(), 'array');

		$model=$this->getModel('settings');
		
		// Player
		$defPh = JoomleagueHelper::getDefaultPlaceholder('player');
		$newPh = $params['ph_player'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_person',
											'picture', 
											$defPh , 
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_team_player',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_team_staff',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_project_referee',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		} 
		// Clublogo - Big
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogobig');
		$newPh = $params['ph_logo_big'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_big', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_playground',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		// Clublogo - medium
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogomedium');
		$newPh = $params['ph_logo_medium'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_middle', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		// Clublogo - small
		$defPh = JoomleagueHelper::getDefaultPlaceholder('clublogosmall');
		$newPh = $params['ph_logo_small'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_club',
											'logo_small', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		// icon
		$defPh = JoomleagueHelper::getDefaultPlaceholder('icon');
		$newPh = $params['ph_icon'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_statistic',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_sports_type',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
			if(!$model->updatePlaceholder(	'#__joomleague_eventtype',
											'icon', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		// Team
		$defPh = JoomleagueHelper::getDefaultPlaceholder('team');
		$newPh = $params['ph_team'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder(	'#__joomleague_team',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}			
			if(!$model->updatePlaceholder(	'#__joomleague_project_team',
											'picture', 
											$defPh ,
											$newPh)) {
				$msg = $model->getError();
			}
		}
		// Playground
		$defPh = JoomleagueHelper::getDefaultPlaceholder('playground');
		$newPh = $params['ph_playground'];
		if($newPh != $defPh) {
			if(!$model->updatePlaceholder('#__joomleague_playground','picture',$defPh ,$newPh)) 
			{
				$msg = $model->getError();
			}
		}
		
		$xmlfile = JPATH_ADMINISTRATOR.'/components/'.$data['option'].'/config.xml';
		$form = Form::getInstance($data['option'], $xmlfile, array('control'=> 'params'), false, "/config");
		$data['params'] = $model->validate($form, $params);
		// Save the rules.
		if (isset($data['params']['rules'])) {
		    $rules	= new Rules($data['params']['rules']);
			$asset	= Table::getInstance('Asset');
		
			if (!$asset->loadByName($data['option'])) {
				$root	= Table::getInstance('Asset');
				$root->loadByName('root.1');
				$asset->name = $data['option'];
				$asset->title = $data['option'];
				$asset->setLocation($root->id, 'last-child');
			}
			$asset->rules = (string) $rules;
			if (!$asset->check() || !$asset->store()) {
				$this->setError($asset->getError());
				return false;
			}
		}
		
		unset($data['params']['rules']);
		
		$table = Table::getInstance('Extension');
		if (!$table->load(array("element" => "com_joomleague", "type" => "component")))
		{
			$app->enqueueMessage(Text::_( 'Not a valid component'), 'warning');
			return false;
		}
		$table->bind($data);
		
		// pre-save checks
		if (!$table->check())
		{
			$app->enqueueMessage(Text::_($table->getError()),'warning');
			return false;
		}
		
		// save the changes
		if ($table->store())
		{
		    $this->setMessage(Text::_( 'COM_JOOMLEAGUE_ADMIN_SETTINGS_CTRL_STAT_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_( 'COM_JOOMLEAGUE_ADMIN_SETTINGS_CTRL_ERROR_SAVE'),'error');
		}

		switch ($task)
		{
			case 'apply':
			case 'save':
				$link = 'index.php?option=com_joomleague&task=settings.edit';
				break;
			default:
				$link = 'index.php?option=com_joomleague&task=settings.display';
				break;
		}

		$this->setRedirect( $link, $msg );
	}

	public function cancel()
	{
		$this->setRedirect( 'index.php?option=com_joomleague&task=settings.display' );
	}
}
