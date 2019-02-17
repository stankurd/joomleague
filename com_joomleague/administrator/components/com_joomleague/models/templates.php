<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

require_once (JPATH_COMPONENT.'/models/list.php');

/**
 * Templates Model
 *
 * @author	JoomLeague Team
 */

class JoomleagueModelTemplates extends JoomleagueModelList
{
	var $_identifier = "templates";
	var $_project_id=null;

	function __construct()
	{
		$app = Factory::getApplication();

		parent::__construct();
		$project_id=$app->getUserState('com_joomleague'.'project',0);
		$this->set('_project_id',$project_id);
		$this->set('_getALL',0);
	}

	function setProjectId($project_id)
	{
		$this->set('_project_id',$project_id);
	}

	function getData()
	{
		$this->checklist();
		return parent::getData();
	}

	function _buildQuery()
	{
		$db		= Factory::getDbo();
		$app	= Factory::getApplication();
		$jinput	= $app->input;
		$option = $jinput->get('option');
		$project_id = $app->getUserState($option.'project');
		
		$filter_order		= $app->getUserStateFromRequest($option.'tmpl_filter_order',		'filter_order',		'tmpl.template',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'tmpl_filter_order_Dir',	'filter_order_Dir',	'',					'word');
		
		// define query
		$query = $db->getQuery(true);
		
		$query->select(array('tmpl.*','(0) AS isMaster'));
		$query->from('#__joomleague_template_config AS tmpl');
		
		// join user table
		$query->select('u.name AS editor');
		$query->join('LEFT', '#__users AS u ON u.id = tmpl.checked_out');
		
		// filter - project_id
		$query->where('tmpl.project_id = '.$this->_project_id);
		
		// filter - template
		$oldTemplates = array(
				'frontpage','do_tipsl','tipranking','tipresults','user',
				'tippentry','tippoverall','tippranking','tippresults','tipprules','tippusers',
				'predictionentry','predictionoverall','predictionranking','predictionresults',
				'predictionrules','predictionusers'
		);
		
		$oldTemplates2 = array();
		foreach ($oldTemplates AS $oldTemplate) {
			$oldTemplates2[] = $db->Quote($oldTemplate); 
		}
		
		$oldTemplates = implode(',', $oldTemplates2);
		
		$query->where($db->quoteName('tmpl.template').' NOT IN ('.$oldTemplates.')');
		
		// order
		if ($filter_order == 'tmpl.template')
		{
			$orderby = array('tmpl.template '.$filter_order_Dir);
		}
		else
		{
			$orderby = array($filter_order.' '.$filter_order_Dir,'tmpl.template');
		}
		
		$query->order($orderby);
		
		return $query;
	}

	
	/**
	 * check that all templates in default location have a corresponding record,except if project has a master template
	 */
	function checklist()
	{
		$app 			= Factory::getApplication();
		$jinput			= $app->input;
		$project_id		= $app->getUserState('com_joomleague'.'project',0);
		
		if (!$project_id){
			return;
		}
		
		$defaultpath	= JPATH_COMPONENT_SITE.'/settings';
		$predictionTemplatePrefix = 'prediction';
		
		$db 	= Factory::getDbo();
		$app	= Factory::getApplication();
		$jinput = $app->input;

		// get info from project
		$query = $db->getQuery(true);
		$query->select(array('master_template','extension'));
		$query->from('#__joomleague_project');
		$query->where('id = '.$project_id);
		$db->setQuery($query);
		$params = $db->loadObject();
		
		// if it's not a master template, do not create records.
		if ($params->master_template){
			return true;
		}

		// otherwise, compare the records with the files get records
		$query = $db->getQuery(true);
		$query->select('template');
		$query->from('#__joomleague_template_config');
		$query->where('project_id = '.$project_id);
		$db->setQuery($query);
		$records = $db->loadColumn();
		if (empty($records)) { 
			$records = array(); 
		}
		
		// add default folder
		$xmldirs[] = $defaultpath.'/default';
		
		$extensions = JoomleagueHelper::getExtensions($jinput->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$extensiontpath =  JPATH_COMPONENT_SITE.'/extensions/'.$extension;
			if (is_dir($extensiontpath.'/settings/default'))
			{
				$xmldirs[] = $extensiontpath.'/settings/default';
			}
		}

		// now check for all xml files in these folders
		foreach ($xmldirs as $xmldir)
		{
			if ($handle=opendir($xmldir))
			{
				/* check that each xml template has a corresponding record in the
				database for this project. If not,create the rows with default values
				from the xml file */
				while ($file=readdir($handle))
				{
					if	(	$file!='.' &&
							$file!='..' &&
							$file!='do_tipsl' &&
							strtolower(substr($file,-3))=='xml' &&
							strtolower(substr($file,0,strlen($predictionTemplatePrefix)))!=$predictionTemplatePrefix
						)
					{
						$template=substr($file,0,(strlen($file)-4));

						if ((empty($records)) || (!in_array($template,$records)))
						{
							$jRegistry = new Registry();
							$form = Form::getInstance($file, $xmldir.'/'.$file);
							$fieldsets = $form->getFieldsets();
							foreach ($fieldsets as $fieldset) {
								foreach($form->getFieldset($fieldset->name) as $field) {
									$jRegistry->set($field->name, $field->value);
								}				
							}
							$defaultvalues = $jRegistry->toString('ini');
							
							$tblTemplate_Config = Table::getInstance('TemplateConfig', 'Table');
							$tblTemplate_Config->template = $template;
							$tblTemplate_Config->title = $file;
							$tblTemplate_Config->params = $defaultvalues;
							$tblTemplate_Config->project_id = $project_id;
							
								// Make sure the item is valid
							if (!$tblTemplate_Config->check())
							{
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
					
							// Store the item to the database
							if (!$tblTemplate_Config->store())
							{
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
							array_push($records,$template);
						}
					}
				}
				closedir($handle);
			}
		}
	}

	function getMasterTemplatesList()
	{
		$app 			= Factory::getApplication();
		$jinput			= $app->input;
		$project_id		= $app->getUserState('com_joomleague'.'project',0);
		
		$db = Factory::getDbo();
		
		// get current project settings
		$query = $db->getQuery(true);
		$query->select('template');
		$query->from('#__joomleague_template_config');
		$query->where('project_id = '.$project_id);
		$db->setQuery($query);
		$currentTemplates = $db->loadColumn();

		$query = $db->getQuery(true);
		if ($this->_getALL)
		{
			$query->select(array('t.*','(1) AS isMaster '));
		}
		else
		{
			$query->select(array('t.id AS value','t.title AS text','t.template AS template'));
		}
		$query->from('#__joomleague_template_config AS t');
		
		// join project table
		$query->join('INNER','#__joomleague_project AS pm ON pm.id = t.project_id');
		$query->join('INNER','#__joomleague_project AS p ON p.master_template = pm.id');
		
		// filter - project_id
		$query->where('p.id = '.$project_id);

		// filter - template
		$oldTemplates = array(
				'frontpage','do_tipsl','tipranking','tipresults','user',
				'tippentry','tippoverall','tippranking','tippresults','tipprules','tippusers',
				'predictionentry','predictionoverall','predictionranking','predictionresults',
				'predictionrules','predictionusers'
		);
		
		$oldTemplates2 = array();
		foreach ($oldTemplates AS $oldTemplate) {
			$oldTemplates2[] = $db->Quote($oldTemplate);
		}
		
		$oldTemplates = implode(',', $oldTemplates2);
		
		$query->where($db->quoteName('t.template').' NOT IN ('.$oldTemplates.')');
	
		// filter - current
		if (count($currentTemplates))
		{
			$currentTemplates2 = array();
			foreach ($currentTemplates AS $currentTemplate) {
				$currentTemplates2[] = $db->Quote($currentTemplate);
			}
			$currentTemplates = implode(',', $currentTemplates2);
			$query->where('t.template NOT IN ('.$currentTemplates.')');
		}
		
		$query->order('t.title');
		
		// Build in JText of template title here and sort it afterwards
		$db->setQuery($query);
		
		$current = $db->loadObjectList();
		$return = (count($current)) ? $current : array();
		
		return $return;
	}

	function getMasterName()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('master.name');
		$query->from('#__joomleague_project AS master');
		$query->join('INNER','#__joomleague_project AS p ON p.master_template = master.id');
		$query->where('p.id = '.$this->_project_id);
		
		$db->setQuery($query);
		$return = $db->loadResult();
		
		return $return;
	}
}
