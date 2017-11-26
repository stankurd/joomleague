<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Teamstaff Controller
 */
class JoomleagueControllerTeamStaff extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('layout','form');

		$projectteam_id = $input->get('projectteam');
		if($projectteam_id)
		{
			$app->setUserState('com_joomleagueprojectteam_id',$projectteam_id);
		}

		parent::__construct($config);
	}


	/**
	 * Function that allows child controller access to model data after the data
	 * has been saved.
	 *
	 * @param BaseDatabaseModel $model	The data model object.
	 * @param array $validData		The validated data.
	 *
	 * @return void
	 */
	protected function postSaveHook(BaseDatabaseModel $model,$validData = array())
	{
		return;
	}
}
