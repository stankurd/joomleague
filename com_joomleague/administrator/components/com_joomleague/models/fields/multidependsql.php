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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

//HTMLHelper::_('behavior.framework');

/**
 * Renders a Dynamic SQL element
 *
 * in the xml element, the following elements must be defined:
 * - depends: list of elements name this element depends on, separated by comma (e.g: "p, tid")
 * - task: the task used to return the query, using defined depends element names as parameters for query (=> 'index.php?option=com_joomleague&controller=ajax&task=<task>&p=1&tid=34')
 */
class JFormFieldMultiDependSQL extends FormField
{
	/**
	 * Element name
	 *
	 * @accessprotected
	 * @varstring
	 */
	protected $type = 'multidependsql';

	function getInput()
	{
		// TODO: for the moment always require a selection, because when it is set to 0, the multiselection
		// will also select the empty line, next to the real selected ones. This will lead to a longer link
		// (all selected ids (e.g. events or stats) will be included in the link address), so this should
		// be fixed later, so that when nothing is selected, only id=0 will be in the link address.
		//$required = (int) $node->attributes('required');
		$required = 1;
		$key = ($this->element['key_field'] ? $this->element['key_field'] : 'value');
		$val = ($this->element['value_field'] ? $this->element['value_field'] : $this->name);
		$task = $this->element['task'];
		$depends = $this->element['depends'];
		$query = $this->element['query'];
		
		$ctrl = $this->name;
		
		// Construct the various argument calls that are supported.
		$attribs	 = ' task="'.$task.'"';
		$attribs	.= ' isrequired="'.$required.'"';
		if ($v = $this->element['size'])
		{
			$attribs	.= 'size="'.$v.'"';
		}

		if ($depends)
		{
			$attribs	.= ' depends="'.$depends.'"';
		}
		$attribs	.= ' class="mdepend inputbox';
		// Optionally add "depend" to the class attribute
		if ($depends)
		{
			$attribs	.= ' depend"';
		}
		else
		{
			$attribs	.= '"';
		}
		
		$value = is_array($this->value) ? $this->value[0] : $this->value; 
		$attribs	.= ' current="'.$value.'"';
		$attribs	.= ' multiple="multiple"';
		
		$selected = explode("|", $value);

		if ($required)
		{
			$options = array();
		}
		else
		{
			$options = array(HTMLHelper::_('select.option', '', Text::_('Select'), $key, $val));
		}

		if ($query!='')
		
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$db->setQuery($query);
			$options = array_merge($options, $db->loadObjectList());
		}

		if ($depends)
		{
			$doc = Factory::getDocument();
			$doc->addScript(Uri::root() . '/administrator/components/com_joomleague/assets/js/depend.js' );
		}

		// Render the HTML SELECT list.
		$text = HTMLHelper::_('select.genericlist', $options, 'l'.$ctrl, $attribs, $key, $val, $selected );
		$text .= '<input type="hidden" name="'.$ctrl.'" id="'.$this->id.'" value="'.$value.'"/>';
		return $text;
	}
}
