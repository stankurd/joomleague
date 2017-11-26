<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Person Controller
 */
class JoomleagueControllerPerson extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('layout','form');

		parent::__construct($config);

		if($input->get('return') == 'projectreferees')
		{
			$this->view_list = 'projectreferees';
			$this->view_item = 'person&return=projectreferees';
		}
		if($input->get('return') == 'teamplayers')
		{
			$this->view_list = 'teamplayers';
			$this->view_item = 'person&return=teamplayers';
		}
		if($input->get('return') == 'teamstaffs')
		{
			$this->view_list = 'teamstaffs';
			$this->view_item = 'person&return=teamstaffs';
		}
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
	
	
	/**
	 * Assign
	 */
	public function personassign()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$prjid = $input->get('prjid',array(0),'array');
		ArrayHelper::toInteger($prjid);
		$proj_id = (int) $prjid[0];
		
		$input->set('hidesidemenu',true);
		$input->set('layout','assignperson');
		$this->setRedirect('index.php?option=com_joomleague&view=person&layout=assignperson&prjid[]='.$proj_id);
	}
}
