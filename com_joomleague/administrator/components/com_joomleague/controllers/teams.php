<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
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
 * Teams Controller
 */
class JoomleagueControllerTeams extends JLGControllerAdmin
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
	public function getModel($name = 'Team',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * Copy Team
	 */
	public function copysave()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		
		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('Select Teams to be copied'),'notice');
		}
		else
		{
			$model = $this->getModel('teams');
			ArrayHelper::toInteger($cid);
			$result = $model->copyTeams($cid);
			if($result)
			{
				$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAM_CTRL_COPY_TEAM'),'notice');
			}
			else
			{
				$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TEAM_CTRL_ERROR_COPY_TEAM').$model->getError(),'error');
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=teams');
	}

	
	/**
	 * Redirect to import view
	 */
	public function import()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=import&table=team',false));
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
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'),'error');
			$this->setRedirect('index.php?option=com_joomleague&view=teams');
			return;
		}
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('team');
		$model->export($cid,'team','Team');
		$this->setRedirect('index.php?option=com_joomleague&view=teams');
		jexit();
	}
}
