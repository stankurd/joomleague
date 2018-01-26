<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
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
 * Matches Controller
 */
class JoomleagueControllerMatches extends JLGControllerAdmin
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
	public function getModel($name = 'Match',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * Redirect to matches view + set massadd to true
	 */
	public function massadd()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=matches&massadd=1',false));
	}
	
	
	/**
	 * Redirect to matches view
	 */
	public function cancelmassadd()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=matches',false));
	}
	
	
	/**
	 * delete selected matches
	 */
	public function remove()
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$user 	= Factory::getUser();
		$option = $input->get('option');
		$project_id = $app->getUserState($option.'project',0);
		
		$cid = $input->get('cid',array(),'array');
		
		if(!is_array($cid) || count($cid) < 1) {
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
			$this->setRedirect('index.php?option=com_joomleague&view=matches');
			return;
		}
		
		ArrayHelper::toInteger($cid);
		
		// Access checks.
		foreach ($cid as $i => $id)
		{
			if (!$user->authorise('core.admin', 'com_joomleague') ||
					!$user->authorise('core.admin', 'com_joomleague.project.'.(int) $project_id) ||
					!$user->authorise('core.delete', 'com_joomleague.match.'.(int) $id))
			{
				// Prune items that you can't delete.
				unset($cid[$i]);
				$app->enqueueMessage( Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'),'notice');
			}
		}
		$model = $this->getModel('match');
		if (!$model->delete($cid)){
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$link = 'index.php?option=com_joomleague&view=matches';
		$this->setRedirect($link);
	}
	
	
	// save the checked rows inside the round matches list
	public function saveshort()
	{
		$app 		= Factory::getApplication();
		$input		= $app->input;
		$option 	= $input->get('option');
		$project_id	= $app->getUserState($option.'project',0);
		$post 		= $input->post->getArray();
		$cid 		= $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
	
		$model 		= $this->getModel('match');
	
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);
	
		$project_tz = new DateTimeZone($project->timezone);
			
		for ($x=0; $x < count($cid); $x++)
		{
			$uiDate = $post['match_date'.$cid[$x]];
			$uiTime = $post['match_time'.$cid[$x]];
			$post['match_date'.$cid[$x]] = $this->convertUiDateTimeToMatchDate($uiDate, $uiTime, $project_tz);
			unset($post['match_time'.$cid[$x]]);
			
			// clear ranking cache
			$cache = Factory::getCache('joomleague.project'.$project_id);
			$cache->clean();
			if (!$model->save_array($cid[$x],$post,true,$project_id))
			{
				$app->enqueueMessage($model->getError(),'warning');
			}
		}
	
		$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_MATCH'),'notice');
		$link	= 'index.php?option=com_joomleague&view=matches';
		$this->setRedirect($link,$msg);
	}
	
	
	private function convertUiDateTimeToMatchDate($uiDate, $uiTime, $timezone)
	{
		$format = Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT');
	
		if (((!strpos($uiDate,'-')!==false) && (!strpos($uiDate,'.')!==false)) && (strlen($uiDate) <= 8 ))
		{
			// to support short date inputs
			if (strlen($uiDate) == 8 )
			{
				if ($format == 'Y-m-d')
				{
					// for example 20111231 is used for 31 december 2011
					$dateStr = substr($uiDate,0,4) . '-' . substr($uiDate,4,2) . '-' . substr($uiDate,6,2);
				}
				elseif ($format == 'd-m-Y')
				{
					// for example 31122011 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr($uiDate,4,4);
				}
				elseif ($format == 'd.m.Y')
				{
					// for example 31122011 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '.' . substr($uiDate,2,2) . '.' . substr($uiDate,4,4);
				}
			}
				
			elseif (strlen($uiDate) == 6 )
			{
				if ($format == 'Y-m-d')
				{
					// for example 111231 is used for 31 december 2011
					$dateStr = substr(date('Y'),0,2) . substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr($uiDate,4,2);
				}
				elseif ($format == 'd-m-Y')
				{
					// for example 311211 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr(date('Y'),0,2) . substr($uiDate,4,2);
				}
				elseif ($format == 'd.m.Y')
				{
					// for example 311211 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '.' . substr($uiDate,2,2) . '.' . substr(date('Y'),0,2) . substr($uiDate,4,2);
				}
			}
		}
		else
		{
			$dateStr = $uiDate;
		}
	
		if (!empty($uiTime))
		{
			$format  .= ' H:i';
	
			if(strpos($uiTime,":")!==false)
			{
				$dateStr .= ' '.$uiTime;
			}
			// to support short time inputs
			// for example 2158 is used instead of 21:58
			elseif (strlen($uiTime) == 4 )
			{
				$dateStr .= ' '.substr($uiTime, 0, -2) . ':' . substr($uiTime, -2);
			}
			// for example 21 is used instead of 2100
			elseif (strlen($uiTime) == 2 )
			{
				$dateStr .= ' '.$uiTime. ':00';
			}
		} else {
			// $dateStr .= ' 00:00:00';
		}
	
		// Todo: fix
		$timestamp = DateTime::createFromFormat($format, $dateStr, $timezone);
		if(is_object($timestamp))  {
			$timestamp->setTimezone(new DateTimeZone('UTC'));
			return $timestamp->format('Y-m-d H:i:s');
		} else {
			return '0000-00-00 00:00:00';
		}
	}
	
}
