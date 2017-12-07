<?php
/**
 * Joomleague
 * @subpackage	Module-NavigationMenu
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\Helper;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');

/**
 * JLMenuItems form field class
 */
class JFormFieldJLMenuItems extends FormField
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
				HTMLHelper::_('select.option', '', JText::_('JNONE')),
				HTMLHelper::_('select.option', 'separator', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_SEPARATOR')),
				HTMLHelper::_('select.option', 'calendar', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_CALENDAR')),
				HTMLHelper::_('select.option', 'curve', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_CURVE')),
				HTMLHelper::_('select.option', 'eventsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_EVENTSRANKING')),
				HTMLHelper::_('select.option', 'matrix', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_MATRIX')),
				HTMLHelper::_('select.option', 'ranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TABLE')),
				HTMLHelper::_('select.option', 'referees', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_REFEREES')),
				HTMLHelper::_('select.option', 'results', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_RESULTS')),
				HTMLHelper::_('select.option', 'resultsmatrix', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_RESULTSMATRIX')),
				HTMLHelper::_('select.option', 'resultsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TABLE_AND_RESULTS')),
				HTMLHelper::_('select.option', 'roster', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_ROSTER')),
				HTMLHelper::_('select.option', 'stats', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_STATS')),
				HTMLHelper::_('select.option', 'statsranking', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_STATSRANKING')),
				HTMLHelper::_('select.option', 'teaminfo', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMINFO')),
				HTMLHelper::_('select.option', 'teamplan', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMPLAN')),
				HTMLHelper::_('select.option', 'teamstats', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TEAMSTATS')),
				HTMLHelper::_('select.option', 'treetonode', JText::_('MOD_JOOMLEAGUE_NAVIGATION_NAVSELECT_TREETONODE')),
				);

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
