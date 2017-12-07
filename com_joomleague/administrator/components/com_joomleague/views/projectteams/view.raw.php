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
 * AJAX View
 */
class JoomleagueViewProjectteams extends JLGView
{

	/**
	 * view AJAX display method
	 *
	 * @return void
	 *
	 */
	function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$pid = $jinput->getInt('p');
		
		// Get some data from the model
		$db = Factory::getDbo();
		$db->setQuery(
				"	SELECT CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(':', t.id, t.alias) ELSE t.id END AS value,
									t.name AS text
							FROM #__joomleague_project_team tt
							JOIN #__joomleague_team t ON t.id = tt.team_id
							JOIN #__joomleague_project p ON p.id = tt.project_id
							WHERE tt.project_id = " . $pid . "
							ORDER BY t.name");
		
		echo '[';
		foreach((array) $db->loadObjectList() as $option)
		{
			echo "{ value: '$option->value', text: '$option->text'},";
		}
		echo ']';
	}
}
?>