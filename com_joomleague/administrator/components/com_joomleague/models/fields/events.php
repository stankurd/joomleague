<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2007-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class JFormFieldEvents extends FormField
{

	protected $type = 'events';

	function getInput() {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$lang = Factory::getLanguage();
		$extension = "com_joomleague";
		$source = Path::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query->select('e.id, e.name')
		->from('#__joomleague_eventtype e')
		->where('published=1')
		->order('name');
		$db->setQuery( $query );
		$events = $db->loadObjectList();
		$mitems = array(HTMLHelper::_('select.option', '', Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));

		foreach ( $events as $event ) {
			$mitems[] = HTMLHelper::_('select.option',  $event->id, '&nbsp;'.Text::_($event->name). ' ('.$event->id.')' );
		}
		
		$output= HTMLHelper::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 