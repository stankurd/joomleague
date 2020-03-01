<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT.'/controllers/joomleague.php');

/**
 * Joomleague Prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
//class JoomleagueControllerPredictionGame extends JoomleagueController
class JoomleagueControllerPredictionGame extends JLGControllerForm
{

protected $view_list = 'predictiongames';

	public function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
		$this->registerTask('apply_project_settings','save_project_settings');
		$this->registerTask('copy','copysave');
	}

	function display($cachable = false, $urlparams = false)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		//$document = $this->app->getDocument();
		$app->enqueueMessage(Text::_('PredictionGame Task -> '.$this->getTask()),'');
	 	$model=$this->getModel('predictiongames');
		$viewType=$document->getType();
		$view=$this->getView('predictiongames',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1=$app->input->get('prediction_id','-1','','int');
		$prediction_id2=(int) $app->getUserState('com_joomleague'.'prediction_id');

		if ($prediction_id1 > (-1))
		{
			$app->setUserState('com_joomleague'.'prediction_id',(int) $prediction_id1);
		}
		else
		{
			$app->setUserState('com_joomleague'.'prediction_id',(int) $prediction_id2);
		}
		$prediction_id=(int) $app->getUserState('com_joomleague'.'prediction_id');

		switch($this->getTask())
		{
			case 'add'	 :
			{
				$input->set('hidemainmenu',0);
				$input->set('layout','form');
				$input->set('view','predictiongame');
				$input->set('edit',false);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

			case 'edit'	:
			{
				$input->set('hidemainmenu',0);
				$input->set('layout','form');
				$input->set('view','predictiongame');
				$input->set('edit',true);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

			case 'predsettings'	:
			{
				$cid	= $input->getVar('cid');
				$input->set('prediction_project',(int) $cid[0]);
				$input->set('hidemainmenu',0);
				$input->set('layout','predsettings');
				$input->set('view','predictiongame');
				$input->set('edit',true);

				// Checkout the project
				$model=$this->getModel('predictiongame');
				$model->checkout();
			} break;

		}		
		parent::display($cachable = false, $urlparams = false);
	}

	// save prediction_game in cid and save/update also the pred_admins and pred_projects associated with the saved predction_game
	public function save()
	{
		// Check for request forgeries
		Session::checkToken() or die('JL_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		$optiontext = strtoupper($input->getCmd('option').'_');
    	$post = $input->post->getArray();
		$cid = $input->post->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		//echo '<pre>'; print_r($post); echo '</pre>'; 

		$post['id']=(int)$cid[0];
		$msg='';
		$d=' - ';

		$model=$this->getModel('predictiongame');

		if ($model->store($post))
		{
			$msg .= Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_PGAME');

			if ($post['id'] == 0){$post['id']=$model->getDbo()->insertid();}

			if ($model->storePredictionAdmins($post))
			{
				$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_ADMINS');
			}
			else
			{
				$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_ADMINS').$model->getError();
			}

			if ($model->storePredictionProjects($post))
			{
				$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_SAVED_PROJECTS');
			}
			else
			{
				$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_PROJECTS').$model->getError();
			}
		}
		else
		{
			$msg .= Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_ERROR_SAVE_PGAME').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=predictiongames&task=predictiongame.display';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=predictiongame.edit&cid[]='.$post['id'];
		}
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	// remove the prediction_game(s) in cid and remove also the projects,members and tipps associated with the deleted prediction_game(s)
	public function remove()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$document = Factory::getDocument();
		$optiontext = strtoupper($input->getCmd('option').'_');
        $post = $app->input->post->getArray();
		$d=' - ';
		$msg='';
		$cid = $input->post->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		
		if (count($cid) < 1){$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ITEM'),'error');}

		$model=$this->getModel('predictiongame');

		if (!$model->delete($cid))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$msg .= Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PGAME');
		if (!$model->deletePredictionAdmins($cid))
		{
			$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ADMINS_MSG').$model->getError();
		}

		$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_ADMINS');
		if (!$model->deletePredictionProjects($cid))
		{
			$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PROJECTS_MSG').$model->getError();
		}

		$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PROJECTS');
		if (!$model->deletePredictionMembers($cid))
		{
			$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PMEMBERS_MSG').$model->getError();
		}

		$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PMEMBERS');
		if (!$model->deletePredictionResults($cid))
		{
			$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PRESULTS_MSG').$model->getError();
		}
		$msg .= $d.Text::_('COM_JOOMLEAGUE_ADMIN_PGAME_CTRL_DEL_PRESULTS');
		$link='index.php?option=com_joomleague&view=predictiongames&task=predictiongame.display';
		
		//$link='index.php?option=com_joomleague&view=predictiongames';
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	public function cancel()
	{
		// Checkin the project
		$model=$this->getModel('predictiongame');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}
  
  public function publish() {
		$this->view_list = 'predictiongames';
		parent::publish();
	}
	
	public function unpublish() {
		$this->view_list = 'predictiongames';
		parent::unpublish();
	}

/**
	function publish()
	{
		$cid=$input->getVar('cid',array(),'post','array');
		ArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,Text::_('JL_ADMIN_PGAME_CTRL_PUBLISH_ITEM'));}
		$model=$this->getModel('predictiongame');
		if(!$model->publish($cid,1))
		{
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}

	function unpublish()
	{
		$cid=$input->getVar('cid',array(),'post','array');
		ArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,Text::_('JL_ADMIN_PGAME_CTRL_UNPUBLISH_ITEM'));}
		$model=$this->getModel('predictiongame');
		if (!$model->publish($cid,0))
		{
			echo "<script> alert('".$model->getError(true)  ."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect('index.php?option=com_joomleague&view=predictiongames');
	}
*/

	// copy and save prediction_game in cid and save/update also the pred_admins and pred_projects associated with the saved predction_game
	public function copysave()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		ToolbarHelper::title(Text::_('JL_ADMIN_PGAME_CTRL_COPY_PGAME'),'generic.png');
		ToolBarHelper::back(Text::_('JL_ADMIN_PGAME_CTRL_BACK'),Route::_('index.php?option=com_joomleague&view=predictiongames'));
		$post = $input->post->getArray();
		$cid = $input->post->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		//echo '<pre>'; print_r($post); echo '</pre>';

		$post['id']=(int) $cid[0];
		$msg		= '';
		$d			= ' - ';

		$model=$this->getModel('predictiongame');

		/*
		if ($model->store($post))
		{
			$msg .= Text::_('Prediction Game Saved');

			if ($post['id'] == 0)
			{
				$post['id']=mysql_insert_id();
			}

			if ($model->storePredictionAdmins($post))
			{
				$msg .= $d.Text::_('Admins of Prediction Game Saved');
			}
			else
			{
				$msg .= $d.Text::_('Error while saving admins data of predictiongame').$model->getError();
			}

			if ($model->storePredictionProjects($post))
			{
				$msg .= $d.Text::_('Projects of Prediction Game Saved');
			}
			else
			{
				$msg .= $d.Text::_('Error while saving projects data of predictiongame').$model->getError();
			}
		}
		else
		{
			$msg .= Text::_('Error while saving general data of predictiongame').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save')
		{
			$link='index.php?option=com_joomleague&view=predictiongames';
		}
		else
		{
			$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
		}
		*/
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	public function save_project_settings()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		ToolBarHelper::title(Text::_('JL_ADMIN_PGAME_CTRL_SAVE'),'generic.png');
		ToolBarHelper::back(Text::_('JL_ADMIN_PGAME_CTRL_BACK'),Route::_('index.php?option=com_joomleague&view=predictiongames'));

		$msg='';
		$post = $input->post->getArray();
		$cid = $input->post->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);	
		//$psapply=$input->post->getInt('psapply',0);
		$post['id']=(int) $cid[0];
		//echo '<pre>'; print_r($this->getTask()); echo '</pre>';

		$model=$this->getModel('predictiongame');

		if ($model->savePredictionProjectSettings($post))
		{
			$msg .= Text::_('JL_ADMIN_PGAME_CTRL_SAVE_PPROJECT');
		}
		else
		{
			$msg .= Text::_('JL_ADMIN_PGAME_CTRL_ERROR_SAVE_PPROJECT').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask() == 'save_project_settings')
		{
			$link='index.php?option=com_joomleague&view=predictiongames';
		}
		else
		{
			$link='index.php?option=com_joomleague&view=predictiongame&controller=predictiongame&task=predsettings&cid[]='.$post['id'];
			/*
			if ($psapply==0)
			{
				$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
			}
			else
			{
				//$link='index.php?option=com_joomleague&controller=predictiongame&task=edit&cid[]='.$post['id'];
				$link='index.php?option=com_joomleague&view=predictiongame&controller=predictiongame&task=predsettings&cid[]='.$post['id'];
			}
			*/
		}
		//echo $psapply.'#<br />';
		//echo $msg.'<br />';
		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
	}

	public function rebuild()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		ToolBarHelper::title(Text::_('JL_ADMIN_PGAME_CTRL_REBUILD'),'generic.png');
		ToolBarHelper::back(Text::_('JL_ADMIN_PGAME_CTRL_BACK'),Route::_('index.php?option=com_joomleague&view=predictiongames'));

		$cid=$input->post->getVar('cid',array(0),'array');
		ArrayHelper::toInteger($cid);
		$msg='';
		$model=$this->getModel('predictiongame');

		if ($model->rebuildPredictionProjectSPoints($cid))
		{
			$msg .= Text::_('JL_ADMIN_PGAME_CTRL_REBUILT');
		}
		else
		{
			$msg .= Text::_('JL_ADMIN_PGAME_CTRL_ERROR_REBUILT').$model->getError();
		}

		// Check the table in so it can be edited.... we are done with it anyway
		//$model->checkin();
		$link='index.php?option=com_joomleague&view=predictiongames';
		//echo $msg.'<br />';
		//echo $link.'<br />';
		$this->setRedirect($link,$msg);
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
	function getModel($name = 'predictiongame', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
/**

	function copysave()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		JToolBarHelper::title(Text::_('JoomLeague - Copy project'),'generic.png');
		JToolBarHelper::back('Back to project list','index.php?option=com_joomleague&view=projects');
		$post = $input->post->getArray();
		#$cid	= $input->getVar('cid',array(0),'post','array');
		ArrayHelper::toInteger($cid);
		#$post['id']=(int) $cid[0];

		$newLeagueCheck=$input->getVar('newLeagueCheck',0,'post','int');
		$leagueNew=trim($input->getVar('leagueNew',Text::_('New league'),'post','string'));
		$newLeagueId=$input->getVar('oldleague',0,'post','int');
		$newSeasonCheck=$input->getVar('newSeasonCheck',0,'post','int');
		$seasonNew=trim($input->getVar('seasonNew',Text::_('New Season'),'post','string'));
		$newSeasonId=$input->getVar('oldseason',0,'post','int');

		//echo '<pre>'; print_r($post); echo '</pre>';

		if (($newLeagueCheck == 1) && ($leagueNew != '')) // add new league if needed
		{
			echo Text::_('Adding new league...').'&nbsp;&nbsp;';
			$model=$this->getModel('league');

			$newLeagueId=$model->addLeague($leagueNew);
			echo $newLeagueId.'<br />';
		}
		$input->set('league_id',$newLeagueId,'post',true);

		if (($newSeasonCheck == 1) && ($seasonNew != '')) // add new season if needed
		{
			echo Text::_('Adding new season...').'&nbsp;&nbsp;';
			$model=$this->getModel('season');

			$newSeasonId=$model->addSeason($seasonNew);

			echo $newSeasonId.'<br />';
		}
		$input->set('season_id',$newSeasonId,'post',true);

		$model=$this->getModel('projects');

		$post = $input->post->getArray();
		$cid	= $input->getVar('cid',array(0),'post','array');
		ArrayHelper::toInteger($cid);
		$post['id']=(int) $cid[0];
		#echo '<pre>'; print_r($post); echo '</pre>';

		if (!$model->cpCheckPExists($post)) //check project unicity if season and league are not both new
		{
			$link='index.php?option=com_joomleague&controller=project&view=projects';
			$msg='This project already exists! Please change name,league or season!';
			$this->setRedirect($link,$msg);
		}

		if ((isset($post['fav_team'])) && (count($post['fav_team']) > 0))
		{
			$temp=implode(",",$post['fav_team']);
		}
		else
		{
			$temp="";
		}
		$post['fav_team']=$temp;

		echo Text::_('Copying project settings...<br />');
		$model=$this->getModel('project');
		if ($model->store($post)) //copy project data and get a new project_id
		{
			//	save the templates params
			if ($post['id'] == 0)
			{
				$post['id']=mysql_insert_id();
			}

			$templatesModel = JLGModel::getInstance('Templates','JoomleagueModel');
			$templatesModel->setProjectId($post['id']);
			$templatesModel->checklist();

			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();

			echo Text::_('Project settings were saved...<br /><br />');
			echo Text::_('Copying project divisions...<br />');
			$source_to_copy_division=Array("0" => 0);
			$model=$this->getModel('division');
			if ($source_to_copy_division=$model->cpCopyDivisions($post)) //copy project divisions
			{
				echo Text::_('Project divisions copied...<br /><br />');
				echo Text::_('Copying project teams...<br />');
				$model=$this->getModel('projectteam');
				if ($model->cpCopyTeams($post,$source_to_copy_division)) //copy project teams
				{
					echo Text::_('Project teams copied...<br /><br />');
				}
				else
				{
					echo Text::_('Error copying project teams!<br /><br />').$model->getError().'<br />';
				}
				echo Text::_('Copying project positions...<br />');
				$model=$this->getModel('projectposition');
				if ($model->cpCopyPositions($post)) //copy project team-positions
				{
					echo Text::_('Project positions copied...').'<br /><br />';
					echo Text::_('Copying project rounds...').'<br />';
					$model=$this->getModel('round');
					if ($model->cpCopyRounds($post)) //copy project team-positions
					{
						echo Text::_('Project rounds copied...').'<br /><br />';
					}
					else
					{
						echo Text::_('Error copying project positions!').'<br />'.$model->getError().'<br />';
					}
				}
				else
				{
					echo Text::_('Error copying project positions!<br /><br />').$model->getError().'<br />';
				}

			}
			else
			{
				echo Text::_('Error copying project divisions!<br /><br />').$model->getError().'<br />';
			}
			$link='index.php?option=com_joomleague&controller=project&view=projects';
		}
		else
		{
			echo Text::_('Error saving project settings!<br /><br />').$model->getError().'<br />';
		}
		#$this->setRedirect($link,$msg);
	}
*/

}
?>