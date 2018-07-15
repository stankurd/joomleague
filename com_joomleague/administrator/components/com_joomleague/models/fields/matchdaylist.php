<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\ListField;

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');

/**
 * Matchdaylist form field class
 */
class JFormFieldMatchdaylist extends ListField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'Matchdaylist';

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
		}		

		if ($project_id)
		{		
			$options = JoomleagueHelper::getRoundsOptions($project_id, 'ASC', true);
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
				
		return $options;
	}
}