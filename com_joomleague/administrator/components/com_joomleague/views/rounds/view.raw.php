<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * AJAX View class for the Joomleague component
 */
class JoomleagueViewRounds extends JLGView
{

	/**
	 * view AJAX display method
	 *
	 * @return void
	 *
	 */
	function display($tpl = null)
	{
		if($this->getLayout() == 'jsonoptions')
		{
			return $this->_displayJsonOptions();
		}
		return;
	}

	/**
	 * view AJAX display method
	 *
	 * @return void
	 *
	 */
	function _displayJsonOptions($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$pid = $input->get('p');
		
		// Get some data from the model
		$db = Factory::getDbo();
		$db->setQuery(
				"	SELECT CASE WHEN CHAR_LENGTH(r.alias) THEN CONCAT_WS(':', r.roundcode, r.alias) ELSE r.roundcode END AS value,
									r.name AS text
							FROM #__joomleague_round AS r
							WHERE r.project_id = " . $pid . "
							ORDER BY r.roundcode");
		
		echo '[';
		foreach((array) $db->loadObjectList() as $option)
		{
			echo "{ value: '$option->value', text: '$option->text'},";
		}
		echo ']';
	}
}
