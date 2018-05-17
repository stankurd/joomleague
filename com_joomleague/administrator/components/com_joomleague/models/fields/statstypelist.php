<?php
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');

/**
 * Statstypelist form field class
 */
class JFormFieldStatstypelist extends ListField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'statstypelist';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		//$filter = (string) $this->element['filter'];
		//$exclude = (string) $this->element['exclude'];
		//$hideNone = (string) $this->element['hide_none'];
		//$hideDefault = (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$files = Folder::files(JPATH_COMPONENT_ADMINISTRATOR.'/statistics', 'php$');
		$options = array();
		foreach ($files as $file)
		{
			$parts = explode('.', $file);
			if ($parts[0] != 'base') {
				$options[] = HTMLHelper::_('select.option', $parts[0], $parts[0]);
			}
		}
		
		// check for statistic in extensions
		$extensions = JoomleagueHelper::getExtensions(0);		
		foreach ($extensions as $type)
		{
			$path = JLG_PATH_SITE.'/extensions/'.$type.'/admin/statistics';
			if (!file_exists($path)) {
				continue;
			}
			$files = Folder::files($path, 'php$');
			foreach ($files as $file)
			{
				$parts = explode('.', $file);
				if ($parts[0] != 'base') {
					$options[] = HTMLHelper::_('select.option', $parts[0], $parts[0]);
				}
			}	
		}
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
