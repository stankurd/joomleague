<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		Marco Vaninetti <martizva@tiscali.it> + other JL Team members
 */
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Project Controller
 */
class JoomleagueControllerProject extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication('administrator');
		$jinput = $app->input;
		$jinput->set('layout','form');
		
		parent::__construct($config);
		
		if($jinput->get('return') == 'projects')
		{
			$this->view_list = 'projects';
			$this->view_item = 'project&return=projects';
		}
		if($jinput->get('return') == 'cpanel')
		{
			$this->view_list = 'joomleague&layout=panel';
			$this->view_item = 'project&return=cpanel';
		}
	}

	/**
	 * Function that allows child controller access to model data after the data
	 * has been saved.
	 *
	 * @param BaseDatabaseModel $model
	 *        	The data model object.
	 * @param array $validData
	 *        	The validated data.
	 *        	
	 * @return void
	 */
	protected function postSaveHook(BaseDatabaseModel $model,$validData = array())
	{
		return;
	}
}