<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

/**
 * Projectteam Controller
 */
class JoomleagueControllerProjectteam extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('layout','form');

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


	/**
	 *
	 */
	public function storechangeteams()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$model = $this->getModel('projectteams');
		$post = $input->post->getArray();

		$oldteamids = $input->get('oldptid',array(),'array');
		ArrayHelper::toInteger($oldteamids);
		$newteamids = $input->get('newptid',array(),'array');
		ArrayHelper::toInteger($newteamids);

		if($oldteamids)
		{
			if(! $model->changeTeamId($oldteamids,$newteamids))
			{
				$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_SAVE') . $model->getError(),'warning');
			}
		}
		$link = 'index.php?option=com_joomleague&view=projectteams';
		$this->setRedirect($link);
	}

	
	/**
	 *
	 */
	public function changeteams()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=' . $this->view_list . '&layout=changeteams',false));
	}

	
	/**
	 * Editlist (assign/unassign)
	 */
	function editlist()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=' . $this->view_list . '&layout=editlist',false));
	}

	
	/**
	 *
	 * @todo : check!
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
		$project_id = $app->getUserState($option . 'project',0);

		if(count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'notice');
		}
		// Access checks.
		foreach($cid as $i=>$id)
		{
			if(! $user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.admin','com_joomleague.project.' . (int) $project_id) ||
					 ! $user->authorise('core.delete','com_joomleague.project_team.' . (int) $id))
			{
				// Prune items that you can't delete.
				unset($cid[$i]);
				$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'noptice');
			}
		}
		$model = $this->getModel('team');

		if(! $model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect('index.php?option=com_joomleague&view=teams');
	}

	/**
	 * copy team to another project
	 */
	public function copy()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$dest = $input->getInt('dest');

		$ptids = $input->get('ptids',array(),'array');
		ArrayHelper::toInteger($ptids);

		// check if this is the final step
		if(! $dest)
		{
			$input->set('view','projectteams');
			$input->set('layout','copy');

			return parent::display();
		}

		$msg = '';
		$type = 'message';

		$model = $this->getModel('projectteams');

		if(! $model->copy($dest,$ptids))
		{
			$msg = $model->getError();
			$type = 'error';
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_SUCCESS'),'notice');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=projectteams',$msg,$type);
		$this->redirect();
	}
}
