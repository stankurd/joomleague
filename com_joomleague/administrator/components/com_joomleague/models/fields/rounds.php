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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

class JFormFieldRounds extends FormField
{

	protected $type = 'rounds';

	protected function getInput() {
		$required 	= $this->element['required'] == 'true' ? 'true' : 'false';
		$order 		= $this->element['order'] == 'DESC' ? 'DESC' : 'ASC';
		$db 		= Factory::getDbo();
		$lang 		= Factory::getLanguage();
		$extension 	= "com_joomleague";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query = ' SELECT id as value '
		       . '      , CASE LENGTH(name) when 0 then CONCAT('.$db->Quote(Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAY_NAME')). ', " ", id)	else name END as text '
		       . '      , id, name, round_date_first, round_date_last, roundcode '
		       . ' FROM #__joomleague_round '
		       . ' WHERE project_id= ' .$project_id
		       . ' ORDER BY roundcode '.$order;
		$db->setQuery( $query );
		$rounds = $db->loadObjectList();
		if($required == 'false') {
			$mitems = array(HTMLHelper::_('select.option', $this->element['default'], Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
		}
		foreach ( $rounds as $round ) {
			$mitems[] = HTMLHelper::_('select.option',  $round->id, '&nbsp;&nbsp;&nbsp;'.$round->name );
		}
		
		$output = HTMLHelper::_('select.genericlist',  $mitems, $this->name.'[]', 'class="inputbox" style="width:90%;" multiple="multiple" size="10"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
