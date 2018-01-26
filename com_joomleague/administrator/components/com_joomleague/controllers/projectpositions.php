<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Session\Session;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

/**
 * Projectpositions Controller
 */
class JoomleagueControllerProjectpositions extends JLGControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Proxy for getModel
	 *
	 * @param string $name Optional.
	 * @param string $prefix Optional.
	 *
	 * @return object model.
	 */
	public function getModel($name = 'Projectposition',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
	{
		$model = parent::getModel($name,$prefix,$config);
		return $model;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param BaseDatabaseModel $model The data model object.
	 * @param integer $ids The array of ids for items being deleted.
	 *
	 * @return void
	 */
	protected function postDeleteHook(BaseDatabaseModel $model,$ids = null)
	{
	}

	/**
	 *
	 */
	public function save_positionslist()
	{
		// Check for token
	    Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $input->post->getArray();
		$post['id'] = (int) $cid[0];
		
		$model = $this->getModel('projectpositions');
		if($model->store($post))
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_POSITION_LIST_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_ERROR_SAVING_POS') . $model->getError(),'error');
		}
		$link = 'index.php?option=com_joomleague&view=projectpositions';
		$this->setRedirect($link,$msg);
	}

	public function cancel()
	{
		// Checkin the project
		$model = $this->getModel('projectposition');
		$this->setRedirect('index.php?option=com_joomleague&view=projectpositions');
	}

	public function orderup()
	{
		$model = $this->getModel('projectposition');
		$model->move(- 1);
		$this->setRedirect('index.php?option=com_joomleague&view=projectpositions');
	}

	public function saveorder()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$order = $input->get('order',array(),'array');
		ArrayHelper::toInteger($order);
		
		$model = $this->getModel('team');
		$model->saveorder($cid,$order);
		$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_SAVED_NEW_ORDERING'),'notice');
		$this->setRedirect('index.php?option=com_joomleague&view=projectpositions');
	}

	public function assign()
	{
		$msg = Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_SELECT_POS_SAVE');
		$link = 'index.php?option=com_joomleague&view=projectpositions&layout=editlist';
		$this->setRedirect($link,$msg);
	}
}
