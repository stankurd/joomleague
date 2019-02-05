<?php
/**
* @copyright	Copyright (C) 2005-2013 JoomLeague.net. All rights reserved.
* @license		GNU/GPL,see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License,and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerpredictiongroup extends JLGControllerForm
{
	protected $view_list = 'predictiongroups';
	
	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	function display($cachable = false, $urlparams = false)
	{
		$app = Factory::getApplication();
		switch ($this->getTask())
		{
			case 'add'	 :
			{
				$app->input->set('hidemainmenu',0);
				$app->input->set('layout','form');
				$app->input->set('view','predictiongroup');
				$app->input->set('edit',false);
				// Checkout the predictiongroup
				$model=$this->getModel('predictiongroup');
				$model->checkout();
			} break;
			case 'edit'	:
			{
				$app->input->set('hidemainmenu',0);
				$app->input->set('layout','form');
				$app->input->set('view','predictiongroup');
				$app->input->set('edit',true);
				// Checkout the predictiongroup
				$model=$this->getModel('predictiongroup');
				$model->checkout();
			} break;
		}
		parent::display();
	}

	function save()
	{
		//Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$post = $app->input->post->getArray();
		$cid=$app->input->post->getVar('cid',array(0),'array');
		ArrayHelper::toInteger($cid);
		$post['id']=(int) $cid[0];
		$model=$this->getModel('predictiongroup');
		if ($model->store($post))
		{
			$msg=Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUP_CTRL_SAVED');
		}
		else
		{
			$msg=Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUP_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=predictiongroups';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=predictiongroup.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	function remove()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$cid=$app->input->post->getVar('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if (count($cid) < 1){$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');}
		$model=$this->getModel('predictiongroup');
		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
			return;
		}
		else
		{
			$msg='COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUP_CTRL_DELETED';
		}
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongroups&task=predictiongroup.display');
	}

	function cancel()
	{
		// Checkin the project
		$model=$this->getModel('predictiongroup');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongroups&task=predictiongroup.display');
	}

	function import()
	{
		$app = Factory::getApplication();
		$app->input->set('view','import');
		$app->input->set('table','predictiongroup');
		parent::display();
	}
	
	function export()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$post = $app->input->post->getArray();
		$cid=$app->input->post->getVar('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if (count($cid) < 1){$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'),'error');}
		$model = $this->getModel("predictiongroup");
		$model->export($cid, "predictiongroup", "predictiongroup");
	}

	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'predictiongroup', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
?>