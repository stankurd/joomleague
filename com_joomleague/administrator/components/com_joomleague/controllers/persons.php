<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Person Controller
 */
class JoomleagueControllerPersons extends JLGControllerAdmin
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
	public function getModel($name = 'Person',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * save the checked rows inside the persons list
	 */
	public function saveshort()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$post = $input->post->getArray();
		$cid = $input->get('cid',array(),'array');
		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('Please select row to update'),'error');
		}
		else
		{
			$model = $this->getModel('person');
			ArrayHelper::toInteger($cid);
			if($model->storeshort($cid,$post))
			{
			    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_PERSON_UPDATE'),'notice');
			}
			else
			{
			    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_ERROR_PERSON_UPDATE').$model->getError(),'error');
			}
		}
		$link = 'index.php?option=com_joomleague&view=persons';
		$this->setRedirect($link);
	}


	/**
	 * save the checked rows inside the persons list
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
			$model = $this->getModel('person');
			$result = $model->storeshortAjax($name,$value,$pk);
			echo '{"success":true}';
		} else {
			echo '{"success":false}';
		}
		
		$app->close();
	}


	/**
	 * Remove selected persons
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
		}
		else
		{
			$model = $this->getModel('person');
			if(! $model->delete($cid))
			{
				$this->setRedirect('index.php?option=com_joomleague&view=persons',$model->getError(),'error');
				return;
			}
		}

		$this->setRedirect('index.php?option=com_joomleague&view=persons');
	}


	/**
	 * Redirect to Import
	 */
	public function import()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=import&table=person',false));
	}


	/**
	 * Export to xml
	 */
	public function export()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');

		if(!is_array($cid) || count($cid) < 1)
		{
		    $app->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'),'error');
			$this->setRedirect('index.php?option=com_joomleague&view=persons');
			return;
		}
		$model = $this->getModel('person');
		ArrayHelper::toInteger($cid);
		$model->export($cid,'person','Person');
		$this->setRedirect('index.php?option=com_joomleague&view=persons');
		jexit();
	}
	
	
	/**
	 * Assign
	 */
	public function personassign()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		
		$input->set('layout','assignperson');
		$this->setRedirect('index.php?option=com_joomleague&view=person&layout=assignperson');
	}
}
