<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Marco Vaninetti <martizva@tiscali.it>
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Template Controller
 */
class JoomleagueControllerTemplate extends JoomleagueController
{
	protected $view_list = 'templates';
	
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('save','save');
		$this->registerTask('apply','apply');
		$this->registerTask('reset','remove');
	}

	public function display($cachable = false, $urlparams = false)
	{
	    $app = Factory::getApplication();
		$document = Factory::getDocument();
		$model = $this->getModel('templates');
		
		$viewType=$document->getType();
		$view=$this->getView('templates',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$projectws=$this->getModel('project');
		$projectws->setId($app->getUserState($this->option.'project',0));
		$view->setModel($projectws, false);

		$input = $app->input;
		switch ($this->getTask())
		{
			case 'add'	 :
				{
				} break;

			case 'edit'	:
				{
					$model=$this->getModel('template');
					$viewType=$document->getType();
					$view=$this->getView('template',$viewType);
					$view->setModel($model,true);	// true is for the default model;

					$projectws=$this->getModel('project');
					$projectws->setId($app->getUserState($this->option.'project',0));
					$view->setModel($projectws, false);
					$view->setLayout('form');
					$input->set('layout', 'form');
					$input->set('view','template');
					$input->set('edit',true);
					
					// Checkout the project
					$model->checkout();
				} break;

		}
		parent::display($cachable, $urlparams);
	}

	public function apply()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$cid=$input->post->get('cid',array(0),'array');
		$post=$input->post->getArray();
		$post['id']=(int) $cid[0];
		$model=$this->getModel('template');
		$index=0;
		if ($model->store($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_SAVED_TEMPLATE'),'notice');
		}
		else
		{
		    $this->setMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_SAVE_TEMPLATE',$index).' '.$model->getError(),'error');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save')
		{
			$link='index.php?option='.$this->option.'&view='.$this->view_list.'&task=template.display';
		}
		else
		{
			$link='index.php?option='.$this->option.'&task=template.edit&cid[]='.$post['select_id'];
		}
		$this->setRedirect($link,$msg);
	}

	public function save()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app 	= Factory::getApplication();
		$input = $app->input;
		$cid=$input->post->get('cid',array(0),'array');
		$post=$input->post->getArray();
		$index=0;
		$master_id=$input->post->getInt('master_id',0);
		if(count($cid) == 1)
		{
			$post['id']=(int) $cid[0];
			$model=$this->getModel('template');
			if ($model->store($post))
			{
			    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_SAVED_TEMPLATE'),'notice');
			}
			else
			{
			    $this->setMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_SAVE_TEMPLATE',$index).' '.$model->getError(),'error');
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();
		}
		else
		{
			for ($index=0; $index < count($cid); $index++)
			{
				$post['id']=(int) $cid[$index];
				$model=$this->getModel('template');
				$model->setId($post['id']);
				$template 		= $model->getData();
				$templatepath	= JPATH_COMPONENT_SITE.'/settings';
				$xmlfile 		= $templatepath.'/default/'.$template->template;
				$jlParams 		= new JLParameter($template->params,$xmlfile);
				$results		= array();
				$params 		= null;
				$name			= "params";
				foreach ($jlParams->getGroups() as $group => $groups)
				{
					foreach ($jlParams->_xml[$group]->children() as $param)
					{
						if(!in_array($param->attributes('name'),$template->params))
						{
							$post['params'][$param->attributes('name')]=$param->attributes('default');
						}
					}
				}
				if ($model->store($post))
				{
				    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_REBUILD_TEMPLATES'),'notice');
				}
				else
				{
				    $this->setMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_REBUILD_TEMPLATE',$index).' '.$model->getError(),'error');
					break;
				}
				// Check the table in so it can be edited.... we are done with it anyway
				$model->checkin();
			}
		}
		if ($this->getTask() == 'save')
		{
			$link='index.php?option='.$this->option.'&view='.$this->view_list.'&task=template.display';
		}
		else
		{
			$link='index.php?option='.$this->option.'&task=template.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	public function remove()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$cid=$input->post->get('cid',array(0),'array');
		ArrayHelper::toInteger($cid);
		$isMaster=$input->post->get('isMaster',array(),'array');
		ArrayHelper::toInteger($isMaster);
		if (count($cid) < 1){
		    throw new Exception(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'), 500);
		}
		foreach ($cid AS $id)
		{
			if ($isMaster[$id])
			{
				echo "<script> alert('" . Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_DELETE_WARNING') . "'); window.history.go(-1); </script>\n";
				return;
			}
		}
		$model=$this->getModel('template');
		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setMessage(Text::_("COM_JOOMLEAGUE_ADMIN_TEMPLATES_RESET_SUCCESS"),'notice');
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=template.display', $msg);
	}

	public function cancel()
	{
		// Checkin the template
		$model=$this->getModel('template');
		$model->checkin();
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=template.display');
	}

	public function masterimport()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$templateid=$input->post->getInt('templateid',0);
		$projectid=$input->post->getInt('project_id',0);
		$model=$this->getModel('template');
		if ($model->import($templateid,$projectid))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_IMPORTED_TEMPLATE'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CTRL_ERROR_IMPORT_TEMPLATE').$model->getError(),'error');
		}
		$this->setRedirect('index.php?option='.$this->option.'&view='.$this->view_list.'&task=template.display',$msg);
	}
}
