<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Joomleague Controller
 */
class JoomleagueControllerJoomleague extends JLGControllerAdmin
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
	public function getModel($name = 'Joomleague',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
	{
		$model = parent::getModel($name,$prefix,$config);
		return $model;
	}



	/**
	 * Redirect to cpanel
	 * we will have some GET variables that can be catched in this case
	 */
	public function panel()
	{

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		// Project_id
		$pid = $input->get('pid',array(),'array');
		ArrayHelper::toInteger($pid);

		if(empty($pid))
		{
			$pid = $app->getUserState($option . 'project',false);
		}
		else
		{
			$pid = $pid[0];
			$app->setUserState($option . 'project',$pid);
		}

		// Roundid
		$rid = $input->get('rid',array(),'array');
		ArrayHelper::toInteger($rid);
		if(empty($rid))
		{
			$rid = $app->getUserState($option . 'round',false);
		}
		else
		{
			$rid = $rid[0];
			$app->setUserState($option . 'round',$rid);
		}

		// Seasonid
		$sid = $input->get('seasonid',array(),'array');
		ArrayHelper::toInteger($sid);
		if(empty($sid))
		{
			$sid = $app->getUserState($option . 'seasonnav',false);
		}
		else
		{
			$sid = $sid[0];
			$app->setUserState($option . 'seasonnav',$sid);
		}

		// Sporttype_id
		$stid = $input->get('stid',array(),'array');
		ArrayHelper::toInteger($stid);
		if(empty($stid))
		{
			$stid = $app->getUserState($option.'sportstypes',false);
		}
		else
		{
			$stid = $stid[0];
			$app->setUserState($option.'sportstypes',$stid);
		}

		// Teamid
		$tid = $input->get('tid',array(),'array');
		ArrayHelper::toInteger($tid);
		if(empty($tid))
		{
			$tid = $app->getUserState($option . 'project_team_id',false);
		}
		else
		{
			$tid = $tid[0];
			$app->setUserState($option . 'project_team_id',$tid);
		}

		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=joomleague&layout=panel',false));
	}
	
	
	/**
	 * function is triggered by change of Quickmenu
	 */
	public function selectws()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
	
		// Sporttype_id
		$stid = $input->get('stid',array(),'array');
		ArrayHelper::toInteger($stid);
		if(empty($stid))
		{
			$stid = $app->getUserState($option . 'sportstypes',false);
		}
		else
		{
			$stid = $stid[0];
			$app->setUserState($option . 'sportstypes',$stid);
		}
		// Project_id
		$pid = $input->get('pid',array(),'array');
		ArrayHelper::toInteger($pid);
		if(empty($pid))
		{
			$pid = $app->getUserState($option . 'project',false);
		}
		else
		{
			$pid = $pid[0];
			$app->setUserState($option . 'project',$pid);
		}
		// Teamid
		$tid = $input->get('tid',array(),'array');
		ArrayHelper::toInteger($tid);
		if(empty($tid))
		{
			$tid = $app->getUserState($option . 'project_team_id',false);
		}
		else
		{
			$tid = $tid[0];
			$app->setUserState($option . 'project_team_id',$tid);
		}
		// Roundid
		$rid = $input->get('rid',array(),'array');
		ArrayHelper::toInteger($rid);
		if(empty($rid))
		{
			$rid = $app->getUserState($option . 'round',false);
		}
		else
		{
			$rid = $rid[0];
			$app->setUserState($option . 'round',$rid);
		}
		// Seasonid
		$sid = $input->get('seasonid',array(),'array');
		ArrayHelper::toInteger($sid);
		if(empty($sid))
		{
			$sid = $app->getUserState($option . 'seasonnav',false);
		}
		else
		{
			$sid = $sid[0];
			$app->setUserState($option . 'seasonnav',$sid);
		}
		
		$model = BaseDatabaseModel::getInstance('joomleague','JoomleagueModel');
		$model->setCurrentProjectData($pid,$rid,$sid,$stid,$tid);
	
	
		$act = $input->get('act');
	
		switch($act)
		{
			case 'projects':
				if($pid)
				{
					// $app->setUserState ( $option . 'project_team_id', '0' );
					$this->setRedirect('index.php?option=com_joomleague&task=joomleague.panel&layout=panel&pid[]=' . $pid,
							Text::_('COM_JOOMLEAGUE_ADMIN_CTRL_PROJECT_SELECTED'),'notice');
				}
				else
				{
					$this->setRedirect('index.php?option=com_joomleague&view=projects');
				}
				break;
	
			case 'teams':
				if($tid)
				{
					$this->setRedirect('index.php?option=com_joomleague&view=teamplayers',Text::_('COM_JOOMLEAGUE_ADMIN_CTRL_TEAM_SELECTED'),'notice');
				}
				else
				{
					$this->setRedirect('index.php?option=com_joomleague&task=joomleague.panel&layout=panel&pid[]=' . $pid);
				}
				break;
	
			case 'rounds':
				if($rid)
				{
					$this->setRedirect('index.php?option=com_joomleague&view=matches&rid[]=' . $rid,
							Text::_('COM_JOOMLEAGUE_ADMIN_CTRL_ROUND_SELECTED'),'notice');
				}
				break;
	
			case 'seasons':
				$this->setRedirect('index.php?option=com_joomleague&view=projects&sid[]=' . $sid,
				Text::_('COM_JOOMLEAGUE_ADMIN_CTRL_SEASON_SELECTED'),'notice');
				break;
	
			default:
				if($stid)
				{
					$this->setRedirect('index.php?option=com_joomleague&view=projects&stid[]=' . $stid,
							Text::_('COM_JOOMLEAGUE_ADMIN_CTRL_SPORTSTYPE_SELECTED'),'notice');
				}
				else
				{
					$this->setRedirect('index.php?option=com_joomleague&view=sportstypes');
				}
		}
	}
}
