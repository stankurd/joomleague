<?php
/**
 * Joomleague
 * @subpackage	Module-NavigationMenu
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

/**
 * JLMenuItems form field class
 */
class JFormFieldJLMenuItems extends JFormFieldList
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'JLMenuItems';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array(
				JHtml::_('select.option', '', JText::_('JNONE')),
				JHtml::_('select.option', 'separator', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_SEPARATOR')),
				JHtml::_('select.option', 'calendar', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_CALENDAR')),
				JHtml::_('select.option', 'curve', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_CURVE')),
				JHtml::_('select.option', 'eventsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_EVENTSRANKING')),
				JHtml::_('select.option', 'matrix', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_MATRIX')),
				JHtml::_('select.option', 'ranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TABLE')),
				JHtml::_('select.option', 'referees', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_REFEREES')),
				JHtml::_('select.option', 'results', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_RESULTS')),
				JHtml::_('select.option', 'resultsmatrix', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_RESULTSMATRIX')),
				JHtml::_('select.option', 'resultsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TABLE_AND_RESULTS')),
				JHtml::_('select.option', 'roster', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_ROSTER')),
				JHtml::_('select.option', 'stats', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_STATS')),
				JHtml::_('select.option', 'statsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_STATSRANKING')),
				JHtml::_('select.option', 'teaminfo', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMINFO')),
				JHtml::_('select.option', 'teamplan', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMPLAN')),
				JHtml::_('select.option', 'teamstats', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMSTATS')),
				JHtml::_('select.option', 'treetonode', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TREETONODE')),
				);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
