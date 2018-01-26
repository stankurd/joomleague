<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;


/**
 * Sportstypes Controller
 */
class JoomleagueControllerSportsTypes extends JLGControllerAdmin
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
	public function getModel($name = 'SportsType',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
	{
		$model = parent::getModel($name,$prefix,$config);
		return $model;
	}

	
	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param JModel $model	The data model object.
	 * @param integer $ids			The array of ids for items being deleted.
	 *
	 * @return void
	 */
	protected function postDeleteHook(BaseDatabaseModel $model,$ids = null)
	{
	}
	

	/**
	 * function to remove selected SportTypes
	 */
	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$user = Factory::getUser();
		$app = Factory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1)
		{
		   $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		}
		else
		{
			$model = $this->getModel('sportsType');
			
			jimport('joomla.utilities.arrayhelper');
			ArrayHelper::toInteger($cid);
			
			$result = $model->delete($cid);
			if($result['removed'])
			{
				$app->enqueueMessage(Text::plural($this->text_prefix.'_N_ITEMS_DELETED',$result['removedCount']));
			}
			if($result['error'])
			{
				$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_CLUBS_UNABLETODELETE'),'warning');
				
				foreach ($result['error'] AS $error) 
				{	
					$html = array();
					$html[] = '<span class="label label-info">'.$error[0].'</span>';
					$html[] = '<br>';
					unset($error[0]);
					$html[] = implode('<br>', $error);
					$app->enqueueMessage(implode("\n",$html),'warning');
				}
			}
			$this->postDeleteHook($model,$cid);
		}
		$this->setRedirect('index.php?option=com_joomleague&view=sportstypes');
	}

	
	/**
	 * Redirect to import view
	 */
	public function import()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=import&table=sports_type',false));
	}

	
	/**
	 * Export selected rows
	 */
	public function export()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if(!is_array($cid) || count($cid) < 1)
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'),'error');
			$this->setRedirect('index.php?option=com_joomleague&view=sportstypes');
			return;
		}
		$model = $this->getModel('sportsType');
		$model->export($cid,'sports_type','SportsType');
		$this->setRedirect('index.php?option=com_joomleague&view=sportstypes');
		jexit();
	}
}
