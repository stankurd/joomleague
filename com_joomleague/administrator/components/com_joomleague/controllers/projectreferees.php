<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author		Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Projectreferees Controller
 */
class JoomleagueControllerProjectReferees extends JLGControllerAdmin
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
	public function getModel($name = 'Projectreferee',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * save the checked rows inside the project teams list
	 */
	public function saveshort()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		$post = $input->post->getArray();
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('projectreferees');

		if($model->saveshort($cid,$post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UPDATED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ERROR_UPDATED') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=projectreferees';
		$this->setRedirect($link,$msg);
	}

	
	/**
	 *
	 */
	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project',0);
		$user = Factory::getUser();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		}
		// Access checks.
		foreach($cid as $i=>$id)
		{
			if(! $user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.admin','com_joomleague.project.' . (int) $project_id) || ! $user->authorise(
					'core.delete','com_joomleague.project_referee.' . (int) $id))
			{
				// Prune items that you can't delete.
				unset($cid[$i]);
				$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'notice');
			}
		}
		$model = $this->getModel('team');

		if(! $model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_joomleague&view=projectreferees');
	}


	/**
	 *
	 */
	public function cancel()
	{
		// Checkin the project
		$model = $this->getModel('projectreferee');
		$model->checkin();

		$this->setRedirect('index.php?option=com_joomleague&view=projectreferees');
	}

	
	/**
	 *
	 */
	public function select()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$app->setUserState($option . 'team',$input->get('team'));
		$this->setRedirect('index.php?option=com_joomleague&view=projectreferees');
	}

	
	/**
	 * Assign referees to the project
	 */
	public function assign()
	{
		// redirect to ProjectReferees page, with a message
		$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_ASSIGN'),'notice');
		$this->setRedirect('index.php?option=com_joomleague&view=projectreferees&layout=assignplayers');
	}

	
	/**
	 * unassing referees
	 */
	public function unassign()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('projectreferees');

		$nDeleted = $model->unassign($cid);
		if(! $nDeleted)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UNASSIGN'),'warning');
		}
		else
		{
			$app->enqueueMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_REFEREE_CTRL_UNASSIGNED',$nDeleted));
		}
		// redirect to projectreferee page, with a message
		$this->setRedirect('index.php?option=com_joomleague&view=projectreferees');
	}
}
