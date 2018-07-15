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
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('list');

/**
 * Favteam form field class
 */
class JFormFieldFavteam extends ListField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'Favteam';

	
	protected function getInput()
	{
		$html = array();
		$attr = '';
	
		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';
	
		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}
	
		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
	
		// Get the field options.
		$options = (array) $this->getOptions();
	
		if (strpos($this->value,',') !== false) {
			$this->value = explode(',',$this->value);
		}
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value,$this->id);
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
		}
		else
		// Create a regular list.
		{
			$html[] = HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value,$this->id);
		}
	
		return implode($html);
	}
	
	
	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		$app = Factory::getApplication();
		// Initialize variables.
		$options = array();

		$varname = (string) $this->element['varname'];
		
		$project_id = $app->input->getVar($varname);
		if (is_array($project_id)) {
			$project_id = $project_id[0];
		} else {
			$project_id = $this->form->getValue('id');
		}
		
		if ($project_id)
		{		
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			
			$query->select(array('pt.team_id AS value', 't.name AS text'));
			$query->from('#__joomleague_team AS t');
			$query->join('inner', '#__joomleague_project_team AS pt ON pt.team_id=t.id');
			$query->where('pt.project_id = '.$project_id);
			$query->order('t.name');
			$db->setQuery($query);
			$options = $db->loadObjectList();
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}
}
