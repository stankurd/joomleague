<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');

/**
 * Country form field class
 */
class JFormFieldCountry extends ListField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'Country';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$options = Countries::getCountryOptions();
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
