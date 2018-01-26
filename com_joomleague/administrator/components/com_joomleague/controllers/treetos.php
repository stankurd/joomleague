<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
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
 * Treetos Controller
 */
class JoomleagueControllerTreetos extends JLGControllerAdmin
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
	public function getModel($name = 'Treeto',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	public function __constructObs()
	{
		parent::__construct();
		
		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	/**
	 *
	 */
	public function displayObs($cachable = false,$urlparams = false)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		$model = $this->getModel('treetos');
		$viewType = $document->getType();
		$view = $this->getView('treetos',$viewType);
		$view->setModel($model,true); // true is for the default model;
		
		$projectws = $this->getModel('project');
		$projectws->setId($app->getUserState($option . 'project',0));
		$view->setModel($projectws);
		
		$task = $this->getTask();
		
		switch($task)
		{
			case 'add':
				{
					$input->set('hidemainmenu',false);
					$input->set('layout','form');
					$input->set('view','treeto');
					$input->set('edit',false);
					
					$model = $this->getModel('treeto');
					break;
				}
			
			case 'edit':
				{
					$input->set('hidemainmenu',false);
					$input->set('layout','form');
					$input->set('view','treeto');
					$input->set('edit',true);
					
					$model = $this->getModel('treeto');
					break;
				}
		}
		parent::display();
	}

	/**
	 * save the checked rows inside the treetos list (save division assignment)
	 */
	public function saveshort()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		
		$post = $input->post->getArray();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		
		$model = $this->getModel('treetos');
		if($model->storeshort($cid,$post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_ERROR_SAVED') . $model->getError(),'error');
		}
		
		$link = 'index.php?option=com_joomleague&view=treetos';
		$this->setRedirect($link,$msg);
	}

	/**
	 *
	 */
	public function genNode()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$cid = $cid[0];
		
		$this->setRedirect('index.php?option=com_joomleague&view=treeto&layout=gennode&cid=' . $cid);
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
		$data['project_id'] = $post['project_id'];
		
		$table = Table::getInstance('Treeto','Table');
		if($table->save($data))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_ERROR_SAVED') . $model->getError(),'error');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		// $model->checkin();
		
		$task = $this->getTask();
		
		if($task == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=treetos';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=treeto.edit&id=' . $post['id'];
		}
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
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		
		if(count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		}
		$model = $this->getModel('treeto');
		
		if(! $model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&view=treetos');
	}

	/**
	 *
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_joomleague&view=treetos');
	}
}
