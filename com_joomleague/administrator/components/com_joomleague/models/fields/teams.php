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
use Joomla\CMS\Form\FormHelper;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('list');

class JFormFieldTeams extends FormField
{

	Public $type = 'teams';
	
	
	/**
	 * Create Input
	 * @see JFormFieldList::getInput()
	 */
	public function getInput()
	{
		$attr = '';
	
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
				
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
				
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
	
		// Get the field options.
		$options = (array) $this->getOptions();
	
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value,$this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value,$this->id);
		}
		return implode($html);
	}
	
	
	protected function getOptions()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// load language string
		$lang = Factory::getLanguage();
		$extension = "com_joomleague";
		$source = JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		// retrieve options
		$query->select('t.id, t.name')
			->from('#__joomleague_team t')
			->order('name');
		$db->setQuery( $query );
		$teams = $db->loadObjectList();
		
		$options = array();
		
		foreach ( $teams as $team ) {
			$options[] = JHtml::_('select.option',  $team->id, '&nbsp;'.$team->name. ' ('.$team->id.')' );
		}
		
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}
