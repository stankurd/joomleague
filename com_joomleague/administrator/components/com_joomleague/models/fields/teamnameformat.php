<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

defined('_JEXEC') or die;

class JFormFieldTeamNameFormat extends FormField
{
	protected $type = 'teamnameformat';

	function getInput() {
		$lang = Factory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		$mitems = array();
		$mitems[] = JHtml::_('select.option', 0, JText::_('COM_JOOMLEAGUE_GLOBAL_TEAM_NAME_FORMAT_SHORT'));
		$mitems[] = JHtml::_('select.option', 1, JText::_('COM_JOOMLEAGUE_GLOBAL_TEAM_NAME_FORMAT_MEDIUM'));
		$mitems[] = JHtml::_('select.option', 2, JText::_('COM_JOOMLEAGUE_GLOBAL_TEAM_NAME_FORMAT_FULL'));

		$output= JHtml::_('select.genericlist',  $mitems,
				$this->name,
				'class="inputbox" size="1"',
				'value', 'text', $this->value, $this->id);
		return $output;
	}
}
