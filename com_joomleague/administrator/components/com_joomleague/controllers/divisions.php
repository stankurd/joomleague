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
 * Divisions Controller
 */
class JoomleagueControllerDivisions extends JLGControllerAdmin
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
	public function getModel($name = 'Division',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * save division in cid and save/update also the events associated with the
	 * saved division
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
		$post['notes'] = $app->input->getVar('notes','none','post','STRING',JREQUEST_ALLOWHTML);

		$model = $this->getModel('division');

		if($model->store($post))
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DIVISION_CTRL_SAVED'),'notice');
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DIVISION_CTRL_ERROR_SAVE') . $model->getError(),'error');
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		$task = $this->getTask();

		if($task == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=divisions';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=division.edit&id=' . $post['id'];
		}

		$this->setRedirect($link);
	}


	/**
	 * remove the divisions in cid
	 */
	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		} else {
			$model = $this->getModel('division');

			if(!$model->delete($cid))
			{
				echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=divisions');
	}


	/**
	 *
	 */
	public function cancel()
	{
		// Checkin the project
		$model = $this->getModel('division');
		$model->checkin();

		$this->setRedirect('index.php?option=com_joomleague&view=divisions');
	}
}
