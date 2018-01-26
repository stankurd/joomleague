<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;


/**
 * Teamstaffs Controller
 */
class JoomleagueControllerTeamStaffs extends JLGControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}


	/**
	 * Proxy for getModel
	 *
	 * @param string $name		name. Optional.
	 * @param string $prefix	prefix. Optional.
	 *
	 * @return object model.
	 */
	public function getModel($name = 'Teamstaff',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
	{
		$model = parent::getModel($name,$prefix,$config);
		return $model;
	}


	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param BaseDatabaseModel $model	The data model object.
	 * @param integer $ids			The array of ids for items being deleted.
	 *
	 * @return void
	 */
	protected function postDeleteHook(BaseDatabaseModel $model,$ids = null)
	{
	}


	/**
	 * EditList
	 */
	public function editlist()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		$model = $this->getModel('teamstaffs');
		$viewType = $document->getType();
		$view = $this->getView('teamstaffs',$viewType);
		$view->setModel($model,true); // true is for the default model;

		$projectws = $this->getModel('project');
		$projectws->setId($app->getUserState($option . 'project',0));
		$view->setModel($projectws);
		$teamws = $this->getModel('projectteam');

		$teamws->setId($app->getUserState($option . 'project_team_id',0));
		$view->setModel($teamws);

		$input->set('hidemainmenu',true);
		$input->set('layout','editlist');
		$input->set('view','teamstaffs');
		$input->set('edit',true);

		parent::display();
	}


	/**
	 * save the checked rows inside the project teams list
	 */
	public function saveshort()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();

		$model = $this->getModel('teamstaffs');
		$model->storeshort($cid,$post);
		if($model->storeshort($cid,$post))
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_CTRL_UPDATED'),'notice');
		}
		else
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_CTRL_ERROR_UPDATED') . $model->getError(),'error');
		}
		$link = 'index.php?option=com_joomleague&view=teamstaffs';
		$this->setRedirect($link,$msg);
	}


	/**
	 * Remove
	 */
	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$user = Factory::getUser();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$project_id = $app->getUserState($option.'project',0);

		if(!is_array($cid) || count($cid) < 1)
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		} else {
			// Access checks.
			foreach($cid as $i=>$id)
			{
				if(!$user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.admin','com_joomleague.project.' . (int) $project_id) ||
						!$user->authorise('core.delete','com_joomleague.team_staff.' . (int) $id))
				{
					// Prune items that you can't delete.
					unset($cid[$i]);
					$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'notice');
				}
			}
			$model = $this->getModel('team');
			if(!$model->delete($cid))
			{
				$app->enqueueMessage($model->getError(true),'error');
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=teams');
	}


	/**
	 * Select
	 */
	public function select()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		/* $pid = $input->getInt('pid'); */

		// set UserState
		$app->setUserState($option.'project_team_id',$input->get('project_team_id'));
		$app->setUserState($option.'team_id',$input->get('team_id'));
		$app->setUserState($option.'team',$input->get('project_team_id'));
		/* $app->setUserState($option.'project',$pid); */

		// redirect
		$this->setRedirect('index.php?option=com_joomleague&view=teamstaffs');
	}


	/**
	 * redirect to layout assignplayers
	 */
	public function assign()
	{
		$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_CTRL_ASSIGN'),'notice');
		$this->setRedirect('index.php?option=com_joomleague&view=teamstaffs&layout=assignplayers');
	}


	/**
	 * UnAssign
	 */
	public function unassign()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('teamstaffs');
		$nDeleted = $model->remove($cid);
		if($nDeleted != count($cid))
		{
			$msg = Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_CTRL_UNASSIGN',$nDeleted);
			$msg .='<br/>' . $model->getError();
			$this->setRedirect('index.php?option=com_joomleague&view=teamstaffs',$msg,'error');
		}
		else
		{
			$msg = Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_CTRL_UNASSIGN',$nDeleted);
			$this->setRedirect('index.php?option=com_joomleague&view=teamstaffs',$msg,'notice');
		}
	}


	/**
	 * Save Assigned
	 */
	public function saveassigned()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();
		$type = $input->getInt('type',0);

		$project_team_id = $input->getInt('project_team_id',0);
		$project_id = $input->getInt('project_id',0);

		$model = $this->getModel('teamstaffs');

		if($model->storeassigned($cid,$project_team_id))
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_PERSON_ASSIGNED_AS_STAFF'),'notice');
		}
		else
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_ERROR_PERSON_ASSIGNED_AS_STAFF') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=teamstaffs';
		$this->setRedirect($link,$msg);
	}
}
