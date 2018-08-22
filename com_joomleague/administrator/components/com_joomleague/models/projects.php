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
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
/**
 * Projects Model
 */
class JoomleagueModelProjects extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'ordering','a.ordering',
					'a.name','l.name',
					's.name','st.name',
					'a.id','a.project_type'
			);
		}

		parent::__construct($config);
	}


	protected function populateState($ordering = null,$direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$value = $this->getUserStateFromRequest($this->context.'.filter.search','filter_search');
		$this->setState('filter.search',$value);

		// List state information.
		parent::populateState('a.name','asc');
		
		// values of select outside form
		$value = $this->getUserStateFromRequest($this->context.'.filter.season','filter_season');
		$this->setState('filter.season',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.sportstype','filter_sportstype');
		$this->setState('filter.sportstype',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.state','filter_state');
		$this->setState('filter.state',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter.league','filter_league');
		$this->setState('filter.league',$value);
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.league');
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.season');
		$id .= ':'.$this->getState('filter.sportstype');
		$id .= ':'.$this->getState('filter.state');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_project AS a');

		// join League table
		$query->select('l.name AS league');
		$query->join('LEFT','#__joomleague_league AS l ON l.id = a.league_id');

		// join Season table
		$query->select('s.name AS season');
		$query->select('s.id AS seasonid');
		$query->join('LEFT','#__joomleague_season AS s ON s.id = a.season_id');
		$query->order('s.name DESC');
		// join SportsType table
		$query->select('st.name AS sportstype');
		$query->join('LEFT','#__joomleague_sports_type AS st ON st.id = a.sports_type_id');

		// join User table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// filter - league
		$filter_league = $this->getState('filter.league');
		if($filter_league > 0)
		{
			$query->where('a.league_id = '.$filter_league);
		}

		// filter - season
		$filter_season = $this->getState('filter.season');
		if($filter_season > 0)
		{
			$query->where('a.season_id = '.$filter_season);
		}

		// filter - sportsType
		$filter_sportstype = $this->getState('filter.sportstype');
		if($filter_sportstype > 0)
		{
			$query->where('a.sports_type_id = '.$db->Quote($filter_sportstype));
		}

		// filter - search
		$search = $this->getState('filter.search');
		if($search)
		{
			$query->where('LOWER(a.name) LIKE '.$db->Quote('%'.$search.'%'));
		}

		// filter - state
		$filter_state = $this->getState('filter.state');
		if($filter_state)
		{
			if($filter_state == 'P')
			{
				$query->where('a.published = 1');
			}
			elseif($filter_state == 'U')
			{
				$query->where('a.published = 0');
			}
			elseif($filter_state == 'A')
			{
				$query->where('a.published = 2');
			}
			elseif($filter_state == 'T')
			{
				$query->where('a.published = -2');
			}
		}

		// filter - order
		$filter_order = $this->state->get('list.ordering','a.id');
		$filter_order_Dir = $this->state->get('list.direction','asc');
		if($filter_order == 'a.ordering')
		{
			$query->order('a.ordering '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'a.ordering');
		}

		return $query;
	}


	/**
	 * Method to check if the project to be copied already exists
	 * 
	 * @access public
	 * @return array
	 */
	function cpCheckPExists($post)
	{
		$name = $post['name'];
		$league_id = $post['league_id'];
		$season_id = $post['season_id'];
		$old_id = $post['old_id'];

		// check project unicity if season and league are not both new
		if($league_id && $season_id)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_project');
			$query->where('name = '.$db->Quote($name));
			$query->where('league_id = '.$league_id);
			$query->where('season_id = '.$season_id);

			$db->setQuery($query);
			$db->execute();
			$num = $db->getAffectedRows();

			if($num > 0)
			{
				return false;
			}
		}

		return true;
	}


	/**
	 * Method to assign teams of an existing project to a copied project
	 * 
	 * @access public
	 * @return array
	 */
	function cpCopyStaff($post)
	{
		$app = Factory::getApplication();
		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];
		$db = Factory::getDbo();

		$query = $db->getQuery(true);
		$query->select(array('a.projectteam_id','a.person_id','a.project_position_id'));
		$query->from('#__joomleague_team_staff AS a');
		$query->select(array('jt.id','jt.team_id'));
		$query->join('LEFT', '#__joomleague_project_team as jt ON jt.id = a.projectteam_id');
		$query->where('jt.project_id = '.$old_id);
		$query->order('jt.id');
		try {
		$db->setQuery($query);
		if($results = $db->loadAssocList())
		{
			foreach($results as $result)
			{
				$query = $db->getQuery(true);
				$query->select(array('a.id','a.team_id'));
				$query->from('#__joomleague_project_team AS a');
				$query->where(array('a.project_id = '.$project_id,'a.team_id = '.$result['team_id']));
				$query->order('a.id');
				$db->setQuery($query);
				$newprojectteam_id = $db->loadResult();

				$p_staff = $this->getTable("TeamStaff");
				$p_staff->bind($result);
				$p_staff->set('teamstaff_id',NULL);
				$p_staff->set('projectteam_id',$newprojectteam_id);
			}
			
				$p_staff->store();
			
		}
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}		
		return true;
	}


	/**
	 * Method to return a season array (id, name)
	 *
	 * @access public
	 * @return array seasons
	 */
	function getSeasons()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','name'));
		$query->from('#__joomleague_season');
		$query->order('name DESC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}

		return $result;
	}


	// COPY //


	/**
	 * Method to assign divisions of an existing project to a copied project
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyDivisions($post)
	{
		$app = Factory::getApplication();
		$o_source_to_copy_division = array('0' => 0);
		$source_to_copy_division = $o_source_to_copy_division;
		if($post['project_type'] != 'DIVISIONS_LEAGUE')
		{
			// No divisions to copy
			return $source_to_copy_division;
		}

		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];
		$db = Factory::getDbo();

		// copy divisions
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_division');
		$query->where(array('project_id = '.$old_id,'parent_id = 0'));
		
		$db->setQuery($query);
		if($results = $db->loadAssocList())
			{
				foreach($results as $result)
				{
				$p_division = Table::getInstance('division','Table');
				$p_division->bind($result);
				$p_division->set('id',NULL);
				$p_division->set('project_id',$project_id);
				$p_division->set('parent_id',0);
				}
			}
		try
			{
			$p_division->store();	
			}		
		catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return $o_source_to_copy_division;
			}

				$source_to_copy_division[$result['id']] = $p_division->get('id');
				// subdivisions
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__joomleague_division');
				$query->where(array('project_id = '.$old_id,'parent_id = '.$result['id']));
				$db->setQuery($query);

				if($subs = $db->loadAssocList())
				{
					foreach($subs as $sub)
					{
						$p_subdiv = Table::getInstance('division','Table');
						$p_subdiv->bind($sub);
						$p_subdiv->set('id',NULL);
						$p_subdiv->set('project_id',$project_id);
						$p_subdiv->set('parent_id',$p_division->get('id'));

						if($p_subdiv->store())
						{
							$source_to_copy_division[$sub['id']] = $p_subdiv->get('id');
						}
					}
				}
			
		return $source_to_copy_division;
	}


	/**
	 * Method to assign teams of an existing project to a copied project
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyRounds($post)
	{
		$app = Factory::getApplication();
		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_round');
		$query->where('project_id = ' . $old_id);
		$query->order('id ASC');
		$db->setQuery($query);
		if($results = $db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_round = Table::getInstance('round','Table');
				$p_round->bind($result);
				$p_round->set('id',NULL);
				$p_round->set('project_id',$project_id);
				try
				{
					$p_round->store();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * Method to assign teams of an existing project to a copied project
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyTeams($post,$source_to_copy_division)
	{
		$app = Factory::getApplication();
		$mdlTeamPlayer = JLGModel::getInstance('teamplayer','JoomleagueModel');
		$mdlTeamStaff = JLGModel::getInstance('teamstaff','JoomleagueModel');

		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];

		$db = Factory::getDbo();

		// copy teams
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_project_team');
		$query->where('project_id = '.$old_id);
		$db->setQuery($query);
		if($results = $db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_team = Table::getInstance('ProjectTeam','table');
				$p_team->bind($result);
				$p_team->set('id',NULL);
				$p_team->set('project_id',$project_id);
				$p_team->set('start_points',0);
				$p_team->set('start_points',0);
				$p_team->set('points_finally',0);
				$p_team->set('neg_points_finally',0);
				$p_team->set('matches_finally',0);
				$p_team->set('won_finally',0);
				$p_team->set('draws_finally',0);
				$p_team->set('lost_finally',0);
				$p_team->set('homegoals_finally',0);
				$p_team->set('guestgoals_finally',0);
				$p_team->set('diffgoals_finally',0);
				$p_team->set('is_in_score',1);
				$p_team->set('use_finally',0);

				// divisions have to be copied first to get a new division id to
				// replace it here
				if($post['project_type'] == 'DIVISIONS_LEAGUE')
				{
					if($result['division_id'] != null && array_key_exists($result['division_id'],$source_to_copy_division))
					{
						$p_team->set('division_id',$source_to_copy_division[$result['division_id']]);
					}
				}
				try 
					{
						$to_projectteam_id = $p_team->store();
					}
				catch(Exception $e)
					{
						$app->enqueueMessage(Text::_($e->getMessage()), 'error');
						return false;
					}

				$from_projectteam_id = $result['id'];
				
				$query = $db->getQuery(true);
				$query->select('MAX(id)');
				$query->from('#__joomleague_project_team');
				$db->setQuery($query);
				$to_projectteam_id = $db->loadResult();
				
				// copy project team-players
				if($mdlTeamPlayer->cpCopyPlayers($from_projectteam_id,$to_projectteam_id))
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TP_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.$model->getError().
					'<br />';
				}

				// copy project team-staff
				if($mdlTeamStaff->cpCopyTeamStaffs($from_projectteam_id,$to_projectteam_id))
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_TS_COPIED',$from_projectteam_id).'<br />';
				}
				else
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TS_COPIED',$from_projectteam_id).'<br />'.$model->getError() .
					'<br />';
				}

				// copy project team trainingdata
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__joomleague_team_trainingdata');
				$query->where('project_team_id ='.$from_projectteam_id);
				$db->setQuery($query);
				if($results = $db->loadAssocList())
				{
					foreach($results as $result)
					{
						$tData = $this->getTable('TeamTrainingData');
						$tData->bind($result);
						$tData->set('id',NULL);
						$tData->set('project_team_id',$to_projectteam_id);
						if(!$tData->store())
						{
							echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TP_COPIED',$from_projectteam_id).'<br />'.
							$model->getError().'<br />';
						}
						else
						{
							echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ERROR_TRAINING_COPIED',$from_projectteam_id).'<br />';
						}
					}
				}
			}
		}
		return true;
	}


	/**
	 * Method to assign positions of an existing project to a copied project
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyPositions($post)
	{
		$app = Factory::getApplication();
		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_project_position');
		$query->where('project_id = '.$old_id);
		$db->setQuery($query);
		if($results = $db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_position = Table::getInstance('projectposition','Table');
				$p_position->bind($result);
				$p_position->set('id',NULL);
				$p_position->set('project_id',$project_id);
				try
				{
					$p_position->store();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
					return false;
				}
				$newid = $p_position->id;

				$query = $db->getQuery(true);
				$query->update('#__joomleague_team_player');
				$query->set('project_position_id = '.$newid);
				$query->where('project_position_id = '.$result['id']);
				try
				{
					$db->setQuery($query);
					$db->execute();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * Method to assign teams of an existing project to a copied project
	 *
	 * @todo
	 * Needs to be adapted to work with persons and not projectreferee
	 *
	 * @access public
	 * @return array
	 */
	function cpCopyProjectReferees($post)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$old_id = (int) $post['old_id'];
		$project_id = (int) $post['id'];

		// copy ProjectReferees
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__joomleague_project_referee');
		$query->where('project_id = '.$old_id);
		$db->setQuery($query);
		if($results = $db->loadAssocList())
		{
			foreach($results as $result)
			{
				$p_player = Table::getInstance('ProjectReferee','Table');
				$p_player->bind($result);
				$p_player->set('id',NULL);
				$p_player->set('project_id',$project_id);
				try
				{
					$p_player->store();
				}
				catch(Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
					return false;
				}
			}
		}
		return true;
	}


	public function copyProjectData($cid = false)
	{
		 foreach($cid as $pid)
		 {
		 	$project_id = (int) $pid;
		 	//$mdlProject = JModelLegacy::getInstance('project','joomleagueModel');
		 	$mdlProject = new JoomleagueModelProject();
		 	//$mdlProjects = JModelLegacy::getInstance('projects','joomleagueModel');
		 	$mdlProjects = new JoomleagueModelProjects();
		 	echo '<h3>'.Text::sprintf('COM_JOOMLEAGUE_PROJECT_COPYING','<i>'.$mdlProject->getProjectName($project_id).'</i>').'</h3>';
		 	$post = ArrayHelper::fromObject($mdlProject->getItem($project_id));
		 	$post['old_id'] = $project_id;
		 	$post['id'] = 0; // will save it as new project
		 	$post['name'] = Text::_('COM_JOOMLEAGUE_PROJECT_COPY_COPY_OF').' '.$mdlProject->getProjectName($project_id);
		 	echo '<br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_SETTINGS');

		 	$tblProject = Table::getInstance('project','Table');
		 	$newProject = $tblProject->save($post);
		 	if($newProject) // copy project data and get a new project_id
		 	{
		 		$this->_success();

		 		$db = Factory::getDbo();
		 		$query = $db->getQuery(true);
		 		$query->select('MAX(id)');
		 		$query->from('#__joomleague_project');
		 		$db->setQuery($query);
		 		$lastId = $db->loadResult();

		 		// set id
		 		if($post['id'] == 0)
		 		{
		 			$post['id'] = $lastId;
		 		}

		 		// TEMPLATE //
		 		$mdlTemplates = JLGModel::getInstance('Templates','JoomleagueModel');
		 		$mdlTemplates->checklist($post['id']);

		 		// Check the table in so it can be edited.... we are done with it anyway
		 		$tblProject->checkin();

		 		// DIVISIONS //
				echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_DIVISIONS');
		 		$source_to_copy_division = Array('0' => 0);
		 		if($source_to_copy_division = $mdlProjects->cpCopyDivisions($post)) // copy projectdivisions
		 		{
		 			$this->_success();

		 			// PROJECT-TEAM //
		 			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_TEAMS');
		 			if($mdlProjects->cpCopyTeams($post,$source_to_copy_division)) // copy project teams
		 			{
		 				$this->_success();
		 			}
		 			else
		 			{
		 				echo '<br /><br />'.$this->_error().'<br />'.$mdlProjects->getError().'<br />';
		 			}

		 			// PROJECT-POSITION //
		 			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_POSITIONS');
		 			if($mdlProjects->cpCopyPositions($post)) // copy project team-positions
		 			{
		 				$this->_success();

		 				// Rounds
		 				echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_ROUNDS');
		 				if($mdlProjects->cpCopyRounds($post)) // copy project rounds
		 				{
		 					$this->_success();
		 				}
		 				else
		 				{
		 					echo '<br /><br />'.$this->_error().'<br />'.$mdlProjects->getError().'<br />';
		 				}
		 			}
		 			else
		 			{
		 				echo '<br /><br />'.$this->_error().'<br />'.$mdlProjects->getError().'<br />';
		 			}
		 		}
		 		else
		 		{
		 			echo '<br /><br />'.$this->_error().'<br />'.$mdlProjects->getError().'<br />';
		 		}

		 		// REFEREES //
		 		echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_COPY_REFEREES');
		 		if($mdlProjects->cpCopyProjectReferees($post))
		 		{
		 			$this->_success();
		 		}
		 		else
		 		{
		 			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'<br />'.$mdlProjects->getError().'<br />';
		 		}
		 		// END //
		 		echo '<br><br><span class="label label-info">Finished copying</span>';
		 	}
		 	else
		 	{
		 		echo '<br /><br />'.$this->_error().'<br />'.$mdlProjects->getError().'<br />';
		 	}
		 }
	}



	public function deleteProjectData($cid = false)
	{
		foreach($cid as $pid)
		{	
			// delete project
			$mdlProject = BaseDatabaseModel::getInstance('project','joomleagueModel');
			$mdlProjects = BaseDatabaseModel::getInstance('projects','joomleagueModel');
			$project_id = (int) $pid;
			if(!$mdlProject->exists($project_id))
			{
				echo Text::sprintf('COM_JOOMLEAGUE_PROJECT_NOT_EXISTS',"<b>$project_id</b>").'<br />';
				break;
			}

			echo '<h3>' . Text::sprintf('COM_JOOMLEAGUE_PROJECT_DELETING','<i>'.$mdlProject->getProjectName($project_id).'</i>').'</h3>';

			// project-match
			echo Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_MATCHES').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectMatch($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// project-round
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_ROUNDS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectRound($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// POSITION //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_POSITIONS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectPosition($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// REFEREE //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_REFEREES').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectReferee($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// TEAMPLAYER //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_PLAYERS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectPlayers($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// TEAMSTAFF
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_STAFFS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectStaff($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}

			// PROJECT-TEAM //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_TEAMS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectTeam($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// TREETO
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_TREETOS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectTreeto($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// DIVISION //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_DIVISIONS').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectDivision($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// TEMPLATE //
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_TEMPLATES').'&nbsp;&nbsp;';
			if(!$mdlProjects->deleteProjectTemplate($project_id))
			{
				echo '<span style="color:red">' . Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProjects->getError();
				break;
			}
			else
			{
				$this->_success();
			}

			// PROJECT
			echo '<br /><br />'.Text::_('COM_JOOMLEAGUE_PROJECT_DELETING_SETTINGS').'&nbsp;&nbsp;';
			if(!$mdlProject->delete($project_id))
			{
				echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span> - '.$mdlProject->getError();
				break;
			}
			else
			{
				$this->_success();
			}
			
			// END //
			echo '<br><br><span class="label label-info">Finished deleting</span>';
		}
	}


	/**
	 *
	 */
	private function _success()
	{
		echo '<span style="color:green">'.Text::_('COM_JOOMLEAGUE_GLOBAL_SUCCESS').'</span>';
	}


	/**
	 *
	 */
	private function _error()
	{
		echo '<span style="color:red">'.Text::_('COM_JOOMLEAGUE_GLOBAL_ERROR').'</span>';
	}



	// DELETE //

	/**
	 * Method to remove matches and match_persons of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectMatch($project_id)
	{
		$app = Factory::getApplication();
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query = 'SELECT DISTINCT
						  #__joomleague_match_1.id AS match_id
						FROM
						  #__joomleague_project_team
						  INNER JOIN #__joomleague_match #__joomleague_match_1 ON #__joomleague_project_team.id=#__joomleague_match_1.projectteam2_id
						  INNER JOIN #__joomleague_project_team #__joomleague_project_team_1 ON #__joomleague_project_team_1.id=#__joomleague_match_1.projectteam1_id
						WHERE
						  #__joomleague_project_team.project_id=' . (int) $project_id;
			$db->setQuery($query);
			if($results = $db->loadAssocList())
			{
				foreach($results as $result)
				{
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_statistic');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_staff_statistic');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_staff');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_event');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_referee');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match_player');
					$query->where('match_id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
					$query = $db->getQuery(true);
					$query->delete('#__joomleague_match');
					$query->where('id = ' . $result['match_id']);
					$db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());
						/* return false; */
					}
				}
			}
		}
		return true;
	}


	/**
	 * Method to remove rounds of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectRound($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_round');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * Method to remove positons of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectPosition($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_project_position');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * Method to remove all project referees of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectReferee($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_project_referee');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * Method to remove teams and assigned teamstaff of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectTeam($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();

			$query = $db->getQuery(true);
			$query->delete('#__joomleague_team_trainingdata');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
				
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_project_team');
			$query->where('project_id=' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * Delete Project-Treeto
	 */
	function deleteProjectTreeto($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_treeto');
			$query->where('project_id='.$project_id);
			$db->setQuery($query);
			try
			{
				$result = $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}

			$mdlTreeto = BaseDatabaseModel::getInstance('treeto','joomleagueModel');
			$mdlTreeto->delete($result);
		}
		return true;
	}



	/**
	 * Method to remove divisions of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectDivision($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__joomleague_division');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$result = $db->loadColumn();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
			$mdlDivision = BaseDatabaseModel::getInstance('division','joomleagueModel');
			$mdlDivision->delete($result);
		}
		return true;
	}



	/**
	 * Method to remove templates of only one project
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteProjectTemplate($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_template_config');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * Method to remove match_events of only one project
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function deleteProjectEventType($project_id)
	{
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			
			$query = '	DELETE
						FROM #__joomleague_match_event
						WHERE match_id in (
						SELECT DISTINCT
						  #__joomleague_match_1.id AS match_id
						FROM
						  #__joomleague_project_team
						  INNER JOIN #__joomleague_match #__joomleague_match_1 ON #__joomleague_project_team.id=#__joomleague_match_1.projectteam2_id
						  INNER JOIN #__joomleague_project_team #__joomleague_project_team_1 ON #__joomleague_project_team_1.id=#__joomleague_match_1.projectteam1_id
						WHERE
						  #__joomleague_project_team.project_id=' . (int) $project_id . '
						)';
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * remove all players from a project
	 */
	function deleteProjectPlayers($project_id)
	{
		$result = false;
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			
			$query = "	DELETE
			FROM #__joomleague_team_player
			WHERE projectteam_id in (SELECT id FROM #__joomleague_project_team WHERE project_id = $project_id)";
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}


	/**
	 * remove all staff from a project
	 */
	function deleteProjectStaff($project_id)
	{
		$result = false;
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = "	DELETE
			FROM #__joomleague_team_staff
			WHERE projectteam_id in (SELECT id FROM #__joomleague_project_team WHERE project_id=$project_id)";
			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
				/* return false; */
			}
		}
		return true;
	}
}
