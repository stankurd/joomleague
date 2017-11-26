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

class JFormFieldSportsTypes extends FormField
{

	protected $type = 'sport_types';

	function getInput()
	{
		$result		= array();
		$db			= Factory::getDbo();
		$query 		= $db->getQuery(true);
		$lang		= Factory::getLanguage();
		$extension 	= "com_joomleague_sport_types";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR.'/components/com_joomleague', 'en-GB', true);
		
		//$query='SELECT id, name FROM #__joomleague_sports_type ORDER BY name ASC ';
		
		$query->select('id, name')
			->from('#__joomleague_sports_type')
			->order('name ASC');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		$mitems = array(); 
		if($this->required == false) {
			$mitems = array(JHtml::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
		}
		
		foreach ( $results as $item )
		{
			$mitems[] = JHtml::_('select.option',  $item->id, '&nbsp;'.JText::_($item->name). ' ('.$item->id.')' );
		}
		
		return JHtml::_('select.genericlist',  $mitems, $this->name, 
				'class="inputbox" size="1"', 'value', 'text', $this->value, $this->id);
	}
}
 