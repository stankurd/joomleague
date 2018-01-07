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

class JFormFieldProject extends FormField
{

	protected $type = 'project';

	protected function getInput() {
		$required 	= $this->required == "true" ? 'true' : 'false';
		$db			= Factory::getDbo();
		$query 		= $db->getQuery(true);
		$lang		= Factory::getLanguage();
		$extension	= "com_joomleague";
		$source 	= JPath::clean(JPATH_ADMINISTRATOR . '/components/' . $extension);
		$lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
		||	$lang->load($extension, $source, null, false, false)
		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
		$query->select('p.id, concat(p.name, \' ('.Text::_('COM_JOOMLEAGUE_GLOBAL_LEAGUE').': \', l.name, \')\', \' ('.Text::_('COM_JOOMLEAGUE_GLOBAL_SEASON').': \', s.name, \' )\' ) as name') 
			->from('#__joomleague_project AS p')
			->join('LEFT',' #__joomleague_season AS s ON s.id = p.season_id') 
			->join('LEFT','#__joomleague_league AS l ON l.id = p.league_id') 
			->where('p.published=1') 
			->order('p.ordering DESC');
		$db->setQuery( $query );
		$projects = $db->loadObjectList();
		$mitems = array();
		if($required=='false') {
			$mitems = array(HTMLHelper::_('select.option', '', Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT')));
		}

		foreach ( $projects as $project ) {
			$mitems[] = HTMLHelper::_('select.option',  $project->id, Text::_($project->name));
		}
		return  HTMLHelper::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" style="width:50%;" size="1"', 'value', 'text', $this->value, $this->id);
	}
}
