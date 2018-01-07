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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;

defined('_JEXEC') or die;

jimport('joomla.application.component.modelform');

/**
 * Settings Model
 */
class JoomleagueModelSettings extends FormModel
{
	/**
	 * Method to update a placeholder string
	 *
	 * @author  And_One <andone@mfga.at>
	 * @access	public
	 * @return	boolean	True on success
	 */
	function updatePlaceholder($table, $field, $oldPlaceholder, $newPlaceholder)
	{
		$result = false;
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->update($table);
		$query->set($field.' = '.$db->Quote($newPlaceholder));
		$query->where($field.' = '.$db->Quote($oldPlaceholder));
		try
		{
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
				
		}
		return true;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 *
	 * @return	mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		if ($path = $this->getState('component.path')) {
			// Add the search path for the admin component config.xml file.
			Form::addFormPath($path);
		}
		else {
			// Add the search path for the admin component config.xml file.
			Form::addFormPath(JPATH_ADMINISTRATOR.'/components/'.$this->getState('component.option'));
		}

		// Get the form.
		$form = $this->loadForm(
			'com_joomleague.component',
			'config',
			array('control' => 'params', 'load_data' => $loadData),
			false,
			'/config'
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}
}
