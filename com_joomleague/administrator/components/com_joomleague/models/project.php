<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

/**
 * Project Model
 */
class JoomleagueModelProject extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.project';


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type 	The table type to instantiate
	 * @param	string 	A prefix for the table class name. Optional.
	 * @param	array 	Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Project',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}


	/**
	 * Method to get a single record.
	 *
	 * @param integer $pk The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if($item = parent::getItem($pk))
		{
		}

		return $item;
	}
		/**
		 * Method to set the item identifier
		 *
		 * @access	public
		 * @param	int item identifier
		 */
	function setId($id)
		{
			// Set item id and wipe data
			$this->_id=$id;
			$this->_data=null;
		}

	/**
	 * Method to get the record form.
	 *
	 * @param array $data		the form.
	 * @param boolean $loadData	the form is to load its own data (default case), false if not.
	 * @return mixed JForm object on success, false on failure
	 */
	public function getForm($data = array(),$loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.project','project',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}

		$input = Factory::getApplication()->input;

		if($this->getState('project.id'))
		{
			$pk = $this->getState('project.id');
			$item = $this->getItem($pk);
		}

		// @todo:
		// fix adding new league/new season
		$form->removeField('newLeagueCheck');
		$form->removeField('newSeasonCheck');
		$form->removeField('leagueNew');
		$form->removeField('seasonNew');

		return $form;
	}

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		if (empty($data->id))
		{
			return;
		}

		// extension management
		$extensions = JoomleagueHelper::getExtensions($data->id);
		$extendedData = new Registry($data->extended);
		$data->extended = $extendedData->toArray();

		foreach ($extensions as $e => $extension)
		{
			$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/admin';

			//General extension extended xml
			$file = $JLGPATH_EXTENSION.'/assets/extended/project.xml';

			if (file_exists(JPath::clean($file)))
			{
				$form->loadFile($file, false);
				$form->bind($data);
				break;
			}
		}

		parent::preprocessForm($form, $data, $group);
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.project.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   Table  $table  A Table object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__joomleague_project');

				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified    = $date->toSql();
			$table->modified_by = $user->get('id');
		}
	}


	/**
	 * Method to save the form data.
	 *
	 *
	 * @param array $data	The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		if(isset($data['fav_team']))
		{
			if(count($data['fav_team']) > 0)
			{
				$temp = implode(",",$data['fav_team']);
			}
			else
			{
				$temp = '';
			}
			$data['fav_team'] = $temp;
		}
		else
		{
			$data['fav_team'] = '';
		}
		if(isset($data['extension']))
		{
			if(count($data['extension']) > 0)
			{
				$temp = implode(",",$data['extension']);
			}
			else
			{
				$temp = '';
			}
			$data['extension'] = $temp;
		}
		else
		{
			$data['extension'] = '';
		}


		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName() . '.id');
			$item = $this->getItem($pk);

			if(isset($data['leagueNew']))
			{
				$mdlLeague = $this->getModel('league');
				$data['league_id'] = $mdlLeague->addLeague($data['leagueNew']);
				$msg .= JText::_('COM_JOOMLEAGUE_LEAGUE_CREATED') . ',';
			}
			if(isset($post['seasonNew']))
			{
				$mdlSeason = $this->getModel('season');
				$post['season_id'] = $mdlSeason->addSeason($data['seasonNew']);
				$msg .= JText::_('COM_JOOMLEAGUE_SEASON_CREATED') . ',';
			}

			return true;
		}

		return false;
	}


	/**
	 * Method to return a season array (id, name)
	 *
	 * @access public
	 * @return array seasons
	 */
	function getSeasons()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('s.id','s.name'));
		$query->from('#__joomleague_season AS s');
		$query->where('s.published = 1');
		$query->order('s.name DESC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return array();
		}
		return $result;
	}


	/**
	 * Method to return template independent projects (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getMasters()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_project');
		$query->where('master_template = 0');
		$query->order('name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}


	/**
	 * Method to return a project array (id, name)
	 *
	 * @access public
	 * @return array project
	 */
	function getProjects()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('p.id','p.name'));
		$query->from('#__joomleague_project AS p');
		$query->where('p.published = 1');
		$query->order('p.ordering,p.name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		
		return $result;
	}


	/**
	 * Method to return a project array (id, name)
	 *
	 * @access public
	 * @return array project
	 */
	function getProjectsBySportsType($sportstype_id,$season = null)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('p.id','p.name'));
		$query->from('#__joomleague_project as p');
		$query->where('p.sports_type_id = ' . $sportstype_id);
		$query->where('p.published = 1');
		if($season)
		{
			$query->where('p.season_id = ' . $season);
		}
		$query->order('p.ordering, p.name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}


	/**
	 * Method to return a project array (id, name)
	 *
	 * @access public
	 * @return array project
	 */
	function getSeasonProjects($season = null)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('p.id');
		$query->from('#__joomleague_project AS p');

		// Season
		$query->join('LEFT','#__joomleague_season AS s ON p.season_id = s.id');
		$query->select('concat(s.name," - ", p.name) AS name');
		if($season)
		{
			$query->where('p.season_id = ' . $season);
		}
		$query->order('p.ordering, p.name ASC ');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}


	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getProjectteams()
	{
		$app = Factory::getApplication();
		$pk = $app->getUserState('com_joomleagueproject');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('t.name AS text','t.notes'));
		$query->from('#__joomleague_team AS t');

		// Project-Team
		$query->select('pt.id AS value');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');

		// $query->where('pt.project_id = '.$pk);
		$query->order('t.name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}


	/**
	 * Method to return the project teams array by team_id (team_id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getProjectteamsbyID()
	{
		$pk = $this->getState($this->getName().'.id');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.name AS text');
		$query->from('#__joomleague_team AS t');

		$query->select('pt.team_id AS value');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');
		$query->where('pt.project_id = '.$pk);
		$query->order('t.name ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}


	/**
	 * returns associative array of parameters values from specified template
	 *
	 * @param string $template name
	 * @return array
	 */
	function getTemplateConfig($template,$projectid = false)
	{
		$pk = $this->getState($this->getName().'.id');
		$item = $this->getItem($pk);
		$app	= Factory::getApplication();
		$input = $app->input;

		if (!$projectid) {
			$projectid = $app->getUserState('com_joomleagueproject');
		}

		$result = '';
		$configvalues = array();
		$project = $item;
		$db = Factory::getDbo();

		// load template param associated to project, or to master template if none found.
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__joomleague_template_config');
		$query->where('template = '.$db->Quote($template));
		$query->where('project_id = '.$projectid);
		$db->setQuery($query);
		$result = $db->loadResult();
		if(!$result)
		{
			if($project->master_template)
			{
				$query = $db->getQuery(true);
				$query->select('params');
				$query->from('#__joomleague_template_config');
				$query->where('template = '.$db->Quote($template));
				$query->where('project_id = '.$project->master_template);
				$db->setQuery($query);
				if(! $result = $db->loadResult())
				{
				    $app->enqueueMessage(sprintf(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_MODEL_MISSING_MASTER_TEMPLATE'),$template),'warning');
					return array();
				}
			}
			else
			{
			    //$app->enqueueMessage(sprintf(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECT_MODEL_MISSING_TEMPLATE'),$template),'warning');
				return array();
			}
		}

		$registry = new Registry;
		$registry->loadString($result);
		$configvalues = $registry;

		return $configvalues;
	}


	/**
	 *
	 * @param	$project_id
	 */
	function getProjectName($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name');
		$query->from('#__joomleague_project');
		$query->where('id = '.$project_id);

		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}


	/**
	 * Checks if an id is a valid Project
	 *
	 * @param	$project_id
	 */
	function exists($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__joomleague_project');
		$query->where('id = '.$project_id);
		$db->setQuery($query);
		return (boolean) $db->loadResult();
	}


	/**
	 * Method to return the query that will obtain all ordering versus projects
	 * It can be used to fill a list box with value/text data.
	 *
	 * @access public
	 * @return string
	 */
	function getOrderingAndProjectQuery()
	{
		return 'SELECT ordering AS value, name AS text FROM #__joomleague_project ORDER BY ordering';
	}


	/**
	 * Convert all games dates from specified project to utc
	 *
	 * this assumes they were originally saved in tz set in project settings
	 */
	public function utc_fix_dates($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select(array('m.id','m.match_date'));
		$query->from('#__joomleague_match AS m');
		$query->join('INNER','#__joomleague_round AS r ON r.id = m.round_id');
		$query->select('p.timezone');
		$query->join('INNER','#__joomleague_project AS p ON p.id = r.project_id');
		$query->where('p.id = '.(int) $project_id);
		$query->where('NOT (m.match_date is null OR m.match_date = \'0000-00-00 00:00:00\')');
		$db->setQuery($query);
		$res = $db->loadObjectList();
		$bConverted = false;
		foreach($res as $match)
		{
			if(is_numeric($match->timezone))
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTS_WRONG_TIMEZONE_FORMAT'));
				return false;
			}
			$utc_date = Factory::getDate($match->match_date,$match->timezone)->setTimezone(new DateTimeZone('UTC'))->toSql();

			$query = $db->getQuery(true);

			$query->update('#__joomleague_match AS m');
			$query->set('m.match_date = '.$db->quote($utc_date));
			$query->where('m.id = '.$match->id);
			try
			{
				$db->setQuery($query);
				$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(JText::_($e->getMessage()), 'error');
				return false;
			}
			
			
				$bConverted = true;
			
		}
		if($bConverted)
		{
			$tblProject = new stdClass();
			$tblProject->id = $project_id;
			$tblProject->is_utc_converted = 1;
			if(!$db->updateObject('#__joomleague_project',$tblProject,'id'))
			{
				$this->setError($db->getError());
				return false;
			}
		}
		return true;
	}
}
