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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Projects Controller
 */
class JoomleagueControllerProjects extends JLGControllerAdmin
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
	public function getModel($name = 'Project',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * Copy Project
	 */
	public function copy()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_PROJECT_COPY_TITLE'),'generic.png');
		JLToolBarHelper::back('COM_JOOMLEAGUE_PROJECT_BACK','index.php?option=com_joomleague&view=projects');

		$app = Factory::getApplication();
		$jinput = $app->input;
		$post = $jinput->post->getArray();
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_COPY'),'error');
			$this->setRedirect('index.php?option=com_joomleague&view=projects');
			return;
		} else {
			$app->setUserState('filter.projectCopy', $cid);
			$this->setRedirect('index.php?option=com_joomleague&view=projects&layout=progressCopy');
			return;
		}

		$this->setRedirect('index.php?option=com_joomleague&view=projects');
	}


	/**
	 *
	 */
	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_PROJECT_DELETE_TITLE'),'generic.png');
		JLToolBarHelper::back('COM_JOOMLEAGUE_PROJECT_BACK','index.php?option=com_joomleague&view=projects');

		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$user = Factory::getUser();
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
			$this->setRedirect(Route::_('index.php?option=com_joomleague&view=' . $this->view_list,false));
			return;
		}
		else
		{
			// Access checks.
			foreach($cid as $i=>$id)
			{
				if(!$user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.admin','com_joomleague.project.'.(int) $id) ||
						 !$user->authorise('core.delete','com_joomleague.project.'.(int) $id))
				{
					// Prune items that you can't delete.
					unset($cid[$i]);
					$app->enqueueMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'warning');
				}
			}
			if ($cid) {
				$app->setUserState('filter.projectDelete', $cid);
				$this->setRedirect('index.php?option=com_joomleague&view=projects&layout=progressDelete');
				return;
			} else {
				$this->setRedirect(Route::_('index.php?option=com_joomleague&view=' . $this->view_list,false));
			}
		}

		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=' . $this->view_list,false));
	}


	/**
	 *
	 */
	public function save()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $jinput->post->getArray();
		$post['id'] = (int) $cid[0];
		$msg = '';
		// convert dates back to mysql date format
		if(isset($post['start_date']))
		{
			$post['start_date'] = strtotime($post['start_date']) ? strftime('%Y-%m-%d',strtotime($post['start_date'])) : null;
		}
		else
		{
			$post['start_date'] = null;
		}

		if(isset($post['fav_team']))
		{
			if(count($post['fav_team']) > 0)
			{
				$temp = implode(",",$post['fav_team']);
			}
			else
			{
				$temp = '';
			}
			$post['fav_team'] = $temp;
		}
		else
		{
			$post['fav_team'] = '';
		}
		if(isset($post['extension']))
		{
			if(count($post['extension']) > 0)
			{
				$temp = implode(",",$post['extension']);
			}
			else
			{
				$temp = '';
			}
			$post['extension'] = $temp;
		}
		else
		{
			$post['extension'] = '';
		}

		if(isset($post['leagueNew']))
		{
			$mdlLeague = $this->getModel('league');
			$post['league_id'] = $mdlLeague->addLeague($post['leagueNew']);
			$msg .= Text::_('COM_JOOMLEAGUE_LEAGUE_CREATED') . ',';
		}
		if(isset($post['seasonNew']))
		{
			$mdlSeason = $this->getModel('season');
			$post['season_id'] = $mdlSeason->addSeason($post['seasonNew']);
			$msg .= Text::_('COM_JOOMLEAGUE_SEASON_CREATED') . ',';
		}

		$model = $this->getModel('project');

		$form = $model->getForm($post,false);
		// Test whether the data is valid.
		$validData = $model->validate($form,$post);

		// Check for validation errors.
		if($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for($i = 0,$n = count($errors);$i < $n && $i < 3;$i ++)
			{
				if($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(),'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i],'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_joomleague.edit.project' . '.data',$post);

			// Redirect back to the edit screen.
			$this->setRedirect(Route::_('index.php?option=com_joomleague&task=project.edit&cid[]=' . $post['id'],false));

			return false;
		}

		if($id = $model->store($post))
		{

			// clear data in the session.
			$app->setUserState('com_joomleague.edit.project' . '.data',null);

			// save the templates params
			if($post['id'] == 0)
			{
				$post['id'] = $id;
			}
			$templatesModel = JLGModel::getInstance('Templates','JoomleagueModel');
			$templatesModel->setProjectId($post['id']);
			$templatesModel->checklist();
			$msg .= Text::_('COM_JOOMLEAGUE_PROJECT_SAVED');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ERROR_SAVING_PROJECT') . $model->getError(),'error');
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		$task = $this->getTask();

		if($task == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=projects';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=project.edit&cid[]=' . $post['id'];
		}
		$this->setRedirect($link,$msg);
	}


	/**
	 * Redirect to import
	 */
	public function import()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=import&table=project',false));
	}


	/**
	 *
	 */
	public function export()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$jinput = $app->input;
		$post = $jinput->post->getArray();
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));
		}
		$model = $this->getModel('project');
		$model->export($cid,'project','Project');
	}


	/**
	 *
	 */
	private function _success()
	{
		echo '<span style="color:green">' . Text::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS') . '</span>';
	}


	/**
	 *
	 */
	private function _error()
	{
		echo '<span style="color:red">' . Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR') . '</span>';
	}


	/**
	 * batch convert project dates to utc
	 *
	 * this is for converting former projects
	 */
	public function fixdates()
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('Please select a project'),'warning');
			$this->setRedirect('index.php?option=com_joomleague&view=projects');
			return;
		}

		$msg = array();
		$type = 'message';

		foreach($cid as $project_id)
		{
			$model = $this->getModel('project');
			if(! $res = $model->utc_fix_dates($project_id))
			{
				$msg[] = $model->getError();
				$type = 'error';
			}
			else
			{
				$msg[] = Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTS_SUCCESSULLY_CONVERTED_PROJECT_D',$project_id);
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=projects',implode($msg,"<br/>"),$type);
	}
}