<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class JFormFieldPosition extends FormField
{
	protected $type = 'position';

	function getInput()
	{
		$required 	= $this->element['required'] == "true" ? 'true' : 'false';
		$result = array();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$lang = Factory::getLanguage();
		$extension = "com_joomleague";
		$source 	= Path::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query->select('pos.id, pos.name AS name')
			->from('#__joomleague_position pos')
			->join('INNER','#__joomleague_sports_type AS s ON s.id=pos.sports_type_id')
			->where('pos.published=1')
			->order('pos.ordering, pos.name');
		$db->setQuery($query);
		if (!$result=$db->loadObjectList())
		{
			return false;
		}
		foreach ($result as $position)
		{
			$position->name=Text::_($position->name);
		}
		if($this->required == false) {
			$mitems = array(HTMLHelper::_('select.option', '', Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION')));
		}
		
		foreach ( $result as $item )
		{
			$mitems[] = HTMLHelper::_('select.option',  $item->id, '&nbsp;'.$item->name. ' ('.$item->id.')' );
		}
		return HTMLHelper::_('select.genericlist',  $mitems, $this->name, 
						'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id);
	}
}
 