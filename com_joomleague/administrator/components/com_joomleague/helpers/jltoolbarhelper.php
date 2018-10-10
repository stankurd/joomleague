<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

class JLToolBarHelper extends ToolBarHelper {

	public static function addNew($task = 'add', $alt = 'JTOOLBAR_NEW', $check = false)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
		    //Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task, 'warning');
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::addNew($task, $alt, $check);
		}
	}

	public static function addNewX($task = 'add', $alt = 'JTOOLBAR_NEW')
	{
		JLToolBarHelper::addNew($task, $alt);
	}

	public static function publish($task = 'publish', $alt = 'JTOOLBAR_PUBLISH', $check = false)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::publish($task, $alt, $check);
		}
	}

	public static function publishList($task = 'publish', $alt = 'JTOOLBAR_PUBLISH')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::publishList($task, $alt);
		}
	}

	public static function unpublish($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH', $check = false)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::unpublish($task, $alt, $check);
		}
	}

	public static function unpublishList($task = 'unpublish', $alt = 'JTOOLBAR_UNPUBLISH')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::unpublishList($task,$alt);
		}
	}

	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true,  $formId = NULL)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::custom($task, $icon, $iconOver, $alt, $listSelect);
		}
	}

	public static function customX($task = '', $icon = '', $iconOver = '', $alt = '', $listSelect = true)
	{
		JLToolBarHelper::custom($task, $icon, $iconOver, $alt, $listSelect);
	}

	public static function editList($task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::editList($task, $alt);
		}
	}

	public static function editListX($task = 'edit', $alt = 'JTOOLBAR_EDIT')
	{
		JLToolBarHelper::editList($task, $alt);
	}

	public static function deleteList($msg = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::deleteList($msg, $task, $alt);
		}
	}

	public static function deleteListX($msg = '', $task = 'remove', $alt = 'JTOOLBAR_DELETE')
	{
		JLToolBarHelper::deleteList($msg, $task, $alt);
	}
	
	public static function apply($task = 'apply', $alt = 'JTOOLBAR_APPLY', $group = false)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::apply($task, $alt);
		}
	}
	
	public static function save($task = 'save', $alt = 'JTOOLBAR_SAVE',  $group = false)
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::save($task, $alt);
		}
	}
	
	public static function archiveList($task = 'archive', $alt = 'JTOOLBAR_ARCHIVE')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::archiveList($task, $alt);
		}	
	}
	
	public static function cancel($task = 'cancel', $alt = 'JTOOLBAR_CANCEL')
	{
		$allowed = true;
		if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
			//display the task which is not handled by the access.xml
			//return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR').' Task: '  .$task);
			$allowed = false;
		}
		if($allowed) {
			parent::cancel($task, $alt);
		}
	}
	
}