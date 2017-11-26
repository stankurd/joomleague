<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Wolfgang Pinitsch <and_one@aon.at>
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * @return string html code for the colorpicker
 */
class JFormFieldColorpicker extends FormField
{

	protected $type = 'colorpicker';
	
	protected function getInput() 
	{
		// css+js
		$document = Factory::getDocument();
		$document->addStylesheet(Uri::root().'/media/com_joomleague/colorpicker/colorpicker.css');
		$document->addScript(Uri::root().'/media/com_joomleague/colorpicker/colorfunctions.js');
		$document->addScript(Uri::root().'/media/com_joomleague/colorpicker/colorpicker.js');
		
		// output
		$html	= array();
		$html[] = "<input type=\"text\" style=\"background: ".$this->value."\" name=\"".$this->name."\" id=\"".$this->id."\" value=\"".$this->value."\">"; 
		$html[] = "<input type=\"button\" value=\"".JText::_('JSELECT')."\" onclick=\"showColorPicker(this, document.getElementsByName('".$this->name."')[0])\">";
		
		return implode("\n", $html);
	}
}
 