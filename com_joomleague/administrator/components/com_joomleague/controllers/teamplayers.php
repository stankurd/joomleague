<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Teamplayers Controller
 */
class JoomleagueControllerTeamplayers extends JLGControllerAdmin
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
	public function getModel($name = 'Teamplayer',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 *
	 */
	public function editlist()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		$model = $this->getModel('teamplayers');
		$viewType = $document->getType();
		$view = $this->getView('teamplayers',$viewType);
		$view->setModel($model,true); // true is for the default model;

		$mdlProject = $this->getModel('project');
		$mdlProject->setId($app->getUserState($option . 'project',0));
		$view->setModel($mdlProject);
		$mdlProjectteam = $this->getModel('projectteam');

		$mdlProjectteam->setId($app->getUserState($option . 'project_team_id',0));
		$view->setModel($mdlProjectteam);

		$input->set('hidemainmenu',$input->get('hidemainmenu',true));
		$input->set('layout','editlist');
		$input->set('view','teamplayers');
		$input->set('edit',true);

		parent::display($cachable,$urlparams);
	}


	/**
	 *
	 */
	public function save()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();
		$post['id'] = (int) $cid[0];
		// decription must be fetched without striping away html code
		$post['notes'] = $app->input->get('notes','none','post','STRING',JREQUEST_ALLOWHTML);
		$model = $this->getModel('TeamPlayer');
		if($model->store($post,'TeamPlayer'))
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYER_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_ERROR_PLAYER_SAVE') . $model->getError(),'error');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		$task = $this->getTask();

		if($task == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=teamplayers';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=teamplayer.edit&id=' . $post['id'];
		}
		$this->setRedirect($link,$msg);
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

		$model = $this->getModel('teamplayers');
		if($model->storeshort($cid,$post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UPDATED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_ERROR_PLAYERS_UPDATED').$model->getError(),'error');
		}
		$link = 'index.php?option=com_joomleague&view=teamplayers';
		$this->setRedirect($link,$msg);
	}


	/**
	 * save the checked rows inside the project teams list
	 */
	public function saveshortAjax()
	{
		header('Content-Type: application/json');

		$app 	= Factory::getApplication();
		$input = $app->input;
		$name   = $input->getString('name');
		$value  = $input->getString('value');
		$token   = $input->getString('token',false);
		$tokenValue = $input->getInt('tokenvalue',false);
		$pk		= $input->get('pk');
		if (empty($token) || empty ($tokenValue) || $tokenValue != '1') {
			jexit();
		}
		
		$token2 = Session::getFormToken();
		if (!($token == $token2)) {
			jexit();
		}
		if ($name) {
			$name = str_replace('_'.$pk, "", $name);
			$model = $this->getModel('teamplayers');
			$result = $model->storeshortAjax($name,$value,$pk);
			echo '{"success":true}';
		} else {
			echo '{"success":false}';
		}
		$app->close();
	}


	/**
	 *
	 */
	public function remove()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project',0);
		$user = Factory::getUser();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'notice');
		}
		else
		{
			// Access checks.
			foreach($cid as $i=>$id)
			{
				if(! $user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.admin','com_joomleague.project.' . (int) $project_id) ||
						 ! $user->authorise('core.delete','com_joomleague.team_player.' . (int) $id))
				{
					// Prune items that you can't delete.
					unset($cid[$i]);
					$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'warning');
				}
			}
			$model = $this->getModel('teamplayer');
			if(! $model->delete($cid))
			{
				echo "<script> alert('" . $model->getError() . "'); window.history.go(-1); </script>\n";
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers');
	}



	/**
	 *
	 */
	public function saveorder()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$order = $input->get('order',array(),'array');
		ArrayHelper::toInteger($order);

		$model = $this->getModel('teamplayer');
		$model->saveorder($cid,$order);
		$this->setMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_NEW_ORDERING_SAVED'),'notice');
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers');
	}


	/**
	 *
	 */
	public function select()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$pid = $input->getInt('pid');
		$ptid = $input->getInt('project_team_id');

		// set Userstate
		$app->setUserState($option . 'project',$pid);
		$app->setUserState($option . 'project_team_id',$ptid);

		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers');
	}


	/**
	 *
	 */
	public function assign()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('project_team_id',$input->getInt('project_team_id'));

		// redirect to players page,with a message
		$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_ASSIGN'),'notice');
		$this->setRedirect('index.php?option=com_joomleague&view=teamplayers&layout=assignplayers');
	}


	/**
	 *
	 */
	public function unassign()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('teamplayers');

		$nDeleted = $model->remove($cid);
		if($nDeleted != count($cid))
		{
			$msg = Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UNASSIGN',$nDeleted);
			$msg .= '<br/>' . $model->getError();
			$this->setRedirect('index.php?option=com_joomleague&view=teamplayers',$msg,'error');
		}
		else
		{
			$msg = Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_CTRL_PLAYERS_UNASSIGN',$nDeleted);
			$this->setRedirect('index.php?option=com_joomleague&view=teamplayers',$msg,'notice');
		}
	}


	/**
	 *
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

		$model = $this->getModel('teamplayers');

		if($model->storeassigned($cid,$project_team_id))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_PERSON_ASSIGNED_AS_PLAYER'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_ERROR_PERSON_ASSIGNED_AS_PLAYER') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=teamplayers';
		$this->setRedirect($link,$msg);
	}
}
