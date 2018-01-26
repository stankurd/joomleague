<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * ProjectPosition Controller
 */
class JoomleagueControllerProjectposition extends JoomleagueController
{
	protected $view_list = 'projectposition';
	
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	public function display($cachable = false, $urlparams = false)
	{
		$document = Factory::getDocument();
		$model=$this->getModel();
		$viewType=$document->getType();
		$view=$this->getView('projectposition',$viewType);
		$view->setModel($model,true);  // true is for the default model;

		$app = Factory::getApplication();
		$projectws=$this->getModel('project');
		$projectws->setId($app->getUserState($this->option.'project',0));
		$view->setModel($projectws);

		$input = $this->input;
		switch($this->getTask())
		{
			case 'add' :
			{
				$input->set('layout','form');
				$input->set('view','projectposition');
				$input->set('edit',false);

				// Checkout the project
				$model=$this->getModel();
				$model->checkout();
			} break;

			case 'edit' :
			{
				$input->set('layout','form');
				$input->set('view','projectposition');
				$input->set('edit',true);

				// Checkout the project
				$model=$this->getModel();
				$model->checkout();
			} break;
		}
		parent::display();
	}

	public function save_positionslist()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$input = $this->input;
		$cid = $input->post->get('cid', array(0), 'array');
		$post = $input->post->getArray();
		$post['id'] = (int) $cid[0];
		
		$model=$this->getModel();
		if ($model->store($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_POSITION_LIST_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_ERROR_SAVING_POS').$model->getError(),'error');
		}
		$link='index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display';
		$this->setRedirect($link,$msg);
	}

	public function save()
	{
		die('Save in projectposition controller');
		// TODO: check if the rest of this method can really be removed... 
		// Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$input = $this->input;
		$cid = $input->post->get('cid', array(0), 'array');
// 		$post['id']=(int) $cid[0];
		$post = $input->post->getArray();
		$model=$this->getModel();
		//if ($model->store($post))
		if (1==2)
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_TEAM_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_ERROR_SAVING_TEAM').$model->getError(),'error');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display';
		}
		else
		{
			$link='index.php?option='.$this->option.'&task=projectposition.edit&cid[]='.$post['id'];
		}
		//$this->setRedirect($link,$msg);
	}

	// save the checked rows inside the project positions list
	public function saveshort()
	{
		die('Saveshort in projectposition controller');
		// TODO: check if the rest of this method can really be removed... 
		$input = $this->input;
		$cid = $input->post->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		
		$model=$this->getModel();
		if ($model->storeshort($cid,$post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_POSITIONS_UPDATED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_ERROR_UPDATING_POS').$model->getError(),'error');
		}
		$link='index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display';
		$this->setRedirect($link,$msg);
	}

	public function remove()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->post->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		if (count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_SELECT_TO_DELETE'),'error');
		}
		// TODO: why do we delete something from the team model?
		$model=$this->getModel('team');
		if(!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError()."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option='.$this->option.'&view=positions&task=position.display');
	}

	// TODO: is this function used for projectpositions?
	public function publish()
	{
		$this->setRedirect('index.php?option='.$this->option.'&view=positions&task=position.display');
	}

	// TODO: is this function used for projectpositions?
	public function unpublish()
	{
		$this->setRedirect('index.php?option='.$this->option.'&view=positions&task=position.display');
	}

	public function cancel()
	{
		// Checkin the project
		$model=$this->getModel();
		//$model->checkin();
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display');
	}

	public function orderup()
	{
		$model=$this->getModel();
		$model->move(-1);
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display');
	}

	public function orderdown()
	{
		// TODO: why do we get the team model here?
		$model=$this->getModel('team');
		$model->move(1);
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=projectposition.display');
	}

	public function saveorder()
	{
		$input = $this->input;
		$cid = $input->post->get('cid', array(), 'array');
		$order=$input->post->get('order', array(), 'array');
		ArrayHelper::toInteger($cid);
		ArrayHelper::toInteger($order);
		// TODO: why do we get the team model here?
		$model=$this->getModel('team');
		$model->saveorder($cid,$order);
		$msg='COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_SAVED_NEW_ORDERING';
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list,$msg);
	}

	public function assign()
	{
		$msg=Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_CTRL_SELECT_POS_SAVE');
		$link='index.php?option='.$this->option.'&view='.$this->view_list.'&layout=editlist&task=projectposition.display';
		$this->setRedirect($link,$msg);
	}
	
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model
	 */
	public function getModel($name = 'Projectposition', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}	
}
