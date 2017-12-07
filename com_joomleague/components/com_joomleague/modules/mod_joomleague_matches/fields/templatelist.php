<?php
/**
 * Joomleague
 * @subpackage	Module-Matches
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;
 
jimport('joomla.form.formfield');
 
class JFormFieldTemplatelist extends FormField
{
	protected $type = 'Templatelist';
	
	function getInput()
	{
		jimport( 'joomla.filesystem.folder' );
		
		// path to images directory
		$path		= JPATH_ROOT.'/'.$this->element['directory'];
		$filter		= $this->element['filter'];
		$exclude	= $this->element['exclude'];
		$folders	= JFolder::folders($path, $filter);
		
		$options = array ();
		foreach ($folders as $folder)
		{
			if ($exclude)
			{
				if (preg_match( chr( 1 ) . $exclude . chr( 1 ), $folder )) {
					continue;
				}
			}
			$options[] = HTMLHelper::_('select.option', $folder, $folder);
		}
		
		$lang = Factory::getLanguage();
		$lang->load("com_joomleague", JPATH_ADMINISTRATOR);
		if (!$this->element['hide_none'])
		{
			array_unshift($options, HTMLHelper::_('select.option', '-1', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DO_NOT_USE')));
		}
		
		if (!$this->element['hide_default'])
		{
			array_unshift($options, HTMLHelper::_('select.option', '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_USE_DEFAULT')));
		}
		
		$doc = Factory::getDocument();
		$doc->addScriptDeclaration('
			function getPosition(element)
			{
				var pos = { y: 0, x: 0 };
		
				if(element)
				{
					var elem=element;
					while(elem && elem.tagName.toUpperCase() != \'BODY\')
					{
						pos.y += elem.offsetTop;
						pos.x += elem.offsetLeft;
						elem = elem.offsetParent;
					}
				}
				return pos;
			}
		
			function scrollToPosition(elementId)
			{
				var a,element,dynPos;
				element = $(elementId);
				a = getPosition(element);
				dynPos = a.y;
				window.scroll(a.x,dynPos);
		
			}
			');
		
		$app = Factory::getApplication();

		$select = '<table>'
				. '<tr>'
				. '<td>'
				. HTMLHelper::_('select.genericlist',  $options, $this->name,
						   'class="inputbox" onchange="$(\'TemplateImage\').src=\''
				           .$app->getCfg('live_site')
						   .'/modules/mod_joomleague_matches/tmpl/\'+this.options[this.selectedIndex].value+\'/template.png\';"', 
						   'value', 'text', $this->value, $this->id)
				. '<br /><br />'
				. JText::_($this->element['details'])
				. '</td>'
				. '</tr>'
				. '<tr>'
				. '<td style="text-align:right;background-color:grey;padding:4px;margin:20px;width:200px;height:150px;">'
				. HTMLHelper::_('image','modules/mod_joomleague_matches/tmpl/'.$this->value.'/template.png', 
						   'TemplateImage', 'id="TemplateImage" width="200"')
			    . '</td>'
			    . '</tr>'
		        . '</table>';

		return $select;
	}
}
 