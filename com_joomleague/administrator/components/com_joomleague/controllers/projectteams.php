<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
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
 * Projectteams Controller
 */
class JoomleagueControllerProjectteams extends JLGControllerAdmin
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
	public function getModel($name = 'Projectteam',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	public function changeteams()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view='.$this->view_list.'&layout=changeteams',false));
	}


	/**
	 *
	 */
	public function cancel()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view='.$this->view_list,false));
	}


	/**
	 * Editlist
	 */
	function editlist()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view='.$this->view_list.'&layout=editlist',false));
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
			if(!$model->changeTeamId($oldteamids,$newteamids))
			{
				$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_SAVE').$model->getError(),'warning');
			}
		}
		$link = 'index.php?option=com_joomleague&view=projectteams';
		$this->setRedirect($link);
	}


	/**
	 *
	 */
	public function save_teamslist()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$post = $input->post->getArray();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('projectteams');
		if($model->store($post))
		{
			// clear ranking cache
			$cache = Factory::getCache('joomleague.project' . $post['id']);
			$cache->clean();
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_SAVE') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=projectteams';
		$this->setRedirect($link,$msg);
	}
	

	/**
	 * save the checked rows inside the project teams list
	 */
	public function saveshort()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();
		$model = $this->getModel('projectteams');

		if($model->storeshort($cid,$post))
		{
			// clear cache
			$cache = Factory::getCache('joomleague.project' . $project_id);
			$cache->clean();
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_UPDATED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_CTRL_ERROR_UPDATED') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=projectteams';
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
		$name   = $input->get('name');
		$value  = $input->get('value');
		$pk		= $input->get('pk');

		if ($name) {
			$name = str_replace('_'.$pk, "", $name);
			$model = $this->getModel('projectteams');
			$result = $model->storeshortinline($name,$value,$pk);

			$project_id = $app->getUserState('com_joomleagueproject');
			$cache = Factory::getCache('joomleague.project'.$project_id);
			$cache->clean();
		} else {
			//
		}

		echo '{"success":true}';
		$app->close();
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
			$msg = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_COPY_SUCCESS');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=projectteams',$msg,$type);
		$this->redirect();
	}
}
