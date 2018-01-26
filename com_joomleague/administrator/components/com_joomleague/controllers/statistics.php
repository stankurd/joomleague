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
 * Statistics Controller
 */
class JoomleagueControllerStatistics extends JLGControllerAdmin
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
	public function getModel($name = 'Statistic',$prefix = 'JoomleagueModel',$config = array('ignore_request' => true))
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
	 * redirect to import-view
	 */
	public function import()
	{
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=import&table=statistic',false));
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
			$this->setRedirect('index.php?option=com_joomleague&view=statistics');
			return;
		}
		$model = $this->getModel('statistic');
		ArrayHelper::toInteger($cid);
		$model->export($cid,'statistic','Statistic');
		$this->setRedirect('index.php?option=com_joomleague&view=statistics');
		jexit();
	}
	

	/**
	 * Fulldelete
	 */
	public function fulldelete()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$user 	= Factory::getUser();
		$app 	= Factory::getApplication();
		$input = $app->input;
		$cid    = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);

		if(!is_array($cid) || count($cid) < 1) {
			$msg = Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE');
			$app->enqueueMessage($msg, 'notice');
		} else {
			// Access checks.
			foreach($cid as $i=>$id)
			{
				if(!$user->authorise('core.admin','com_joomleague') || ! $user->authorise('core.delete','com_joomleague.statistic.'.$id))
				{
					// Prune items that you can't delete.
					unset($cid[$i]);
					$msg = Text::_('JERROR_CORE_DELETE_NOT_PERMITTED');
					$app->enqueueMessage($msg, 'notice');
				}
			}
			$model = $this->getModel('statistics');
			$result = $model->fulldelete($cid);

			if(!$result) {
				$this->setMessage('Statistics haven\'t been deleted');
				$this->setRedirect('index.php?option=com_joomleague&view=statistics');
				return;
			} else {
				// output notices
			}

			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_STAT_CTRL_DELETED'),'notice');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=statistics');
	}
}
