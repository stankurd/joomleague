<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
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
 * Treetomatches controller
 */
class JoomleagueControllerTreetomatches extends JLGControllerAdmin
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
	public function getModel($name = 'Treetomatch',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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


	public function editlist()
	{
		/*
		 * $app = Factory::getApplication();
		 * $input = $app->input;
		 * $option = $input->getCmd('option');
		 * $document = Factory::getDocument();
		 * $model = $this->getModel('treetomatchs');
		 * $viewType = $document->getType();
		 * $view = $this->getView('treetomatchs', $viewType);
		 * $view->setModel($model, true); // true is for the default model;
		 *
		 * $projectws = $this->getModel('project');
		 * $projectws->setId($app->getUserState($option . 'project', 0));
		 * $view->setModel($projectws);
		 *
		 * if ($nid = $app->input->getVar('nid', null, '', 'array'))
		 * {
		 * // Set node_id
		 * $app->setUserState($option . 'node_id', $nid[0]);
		 * }
		 * if ($tid = $app->input->getVar('tid', null, '', 'array'))
		 * {
		 * // Set Treeto_id
		 * $app->setUserState($option . 'treeto_id', $tid[0]);
		 * }
		 * $nodews = $this->getModel('treetonode');
		 * $nodews->setId($app->getUserState($option . 'node_id'));
		 * $view->setModel($nodews);
		 *
		 * $input->set('hidemainmenu', false);
		 * $input->set('layout', 'editlist');
		 * $input->set('view', 'treetomatchs');
		 * $input->set('edit', true);
		 *
		 * parent::display();
		 */
		$app = Factory::getApplication();
		$input = $app->input;

		$nid = $input->get('nid',array(),'array');
		ArrayHelper::toInteger($nid);
		if($nid)
		{
			$app->setUserState('com_joomleaguenode_id',$nid[0]);
		}

		$tid = $input->get('tid',array(),'array');
		ArrayHelper::toInteger($tid);
		if($tid)
		{
			$app->setUserState('com_joomleaguetreeto_id',$tid[0]);
		}

		$this->setRedirect('index.php?option=com_joomleague&view=treetomatches&layout=editlist');
	}


	/**
	 *
	 */
	public function save_matcheslist()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();
		$post['id'] = (int) $cid[0];

		$model = $this->getModel('treetomatchs');
		if($model->store($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_CTRL_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_CTRL_ERROR_SAVE') . $model->getError(),'error');
		}

		$link = 'index.php?option=com_joomleague&view=treetonodes';
		$this->setRedirect($link,$msg);
	}
}
