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
jimport( 'joomla.filesystem.folder' );

class JFormFieldFlagsFolder extends FormField
{
	protected $type = 'FlagsFolder';

	function getInput()
	{
		$folderlist = array();
		$folderlist1 = JFolder::folders(JPATH_ROOT.'/images', '', true, true, array(0 => 'system'));
	    $folderlist2 = JFolder::folders(JPATH_ROOT.'/media' , '', true, true, array(0 => 'system'));
	    foreach ($folderlist1 AS $key => $val)
	    {
	    	$folderlist[] = str_replace(JPATH_ROOT.'/', '', $val);
	    }
	    foreach ($folderlist2 AS $key => $val)
	    {
	    	$folderlist[] = str_replace(JPATH_ROOT.'/', '', $val);
	    }

		$lang = Factory::getLanguage();
		$lang->load("com_joomleague", JPATH_ADMINISTRATOR);
		$items = array(HTMLHelper::_('select.option',  '', JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DO_NOT_USE')));

		foreach ( $folderlist as $folder )
		{
			$items[] = HTMLHelper::_('select.option',  $folder, '&nbsp;'.$folder );
		}

		$output= HTMLHelper::_('select.genericlist',  $items, $this->name,
						  'class="inputbox"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 