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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Projectteam Model
 */
class JoomleagueModelProjectteams extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					't.name','team_id',
					'a.id'
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

		$value = $this->getUserStateFromRequest($this->context.'.filter.division','filter_division');
		$this->setState('filter.division',$value);
		
		parent::populateState('t.name','desc');
	}


	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.division');

		return parent::getStoreId($id);
	}


	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option.'project');

		// Query
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*','a.id AS projectteamid'));
		$query->from('#__joomleague_project_team AS a');

		// join team table
		$query->select(array('t.name','t.club_id','t.name AS teamname'));
		$query->join('LEFT','#__joomleague_team AS t ON t.id = a.team_id');

		// join club table
		$query->select(array('c.email AS club_email'));
		$query->join('LEFT','#__joomleague_club AS c ON c.id = t.club_id');

		// join division table
		$query->join('LEFT','#__joomleague_division AS d ON d.id = a.division_id');

		// join playground table
		$query->join('LEFT','#__joomleague_playground AS plg ON plg.id = a.standard_playground');

		// counts
		$query->select('(SELECT COUNT(*) FROM #__joomleague_team_player AS tp WHERE tp.projectteam_id = a.id AND tp.published = 1) AS '.$db->QuoteName('playercount'));
		$query->select('(SELECT COUNT(*) FROM #__joomleague_team_staff AS ts WHERE ts.projectteam_id = a.id AND ts.published = 1) AS '.$db->QuoteName('staffcount'));

		// filter - project
		$query->where('a.project_id = '.$project_id);

		// filter - division
		$division = $this->getState('filter.division');
		if($division > 0)
		{
			$query->where('d.id = '.$db->Quote($division));
		}

		// Orderby
		$filter_order = $this->state->get('list.ordering','t.name');
		$filter_order_Dir = $this->state->get('list.direction','desc');
		if($filter_order == 't.name')
		{
			$query->order('t.name '.$filter_order_Dir);
		}
		else
		{
			$query->order($filter_order.' '.$filter_order_Dir,'t.name ');
		}

		return $query;
	}


	/**
	 * Method to update project teams list
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function store($data)
	{
		$app = Factory::getApplication();
		$result = true;
		$peid = $data['project_teamslist'];
		if($peid == null)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query = "	DELETE
						FROM #__joomleague_project_team
						WHERE project_id = '" . $data['id'] . "'";
			//$query->delete('#__joomleague_project_team');
			//$query->where('project_id = ' . $data['id']);
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
		else
		{
			ArrayHelper::toInteger($peid);
			$peids = implode(',',$peid);

			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query = "	DELETE
						FROM #__joomleague_project_team
						WHERE project_id = '" . $data['id'] . "' AND team_id NOT IN  (" . $peids . ")";
			//$query->delete('#__joomleague_project_team');
			//$query->where('project_id = ' . $data['id']);
			//$query->where('team_id NOT IN  (' . $peids . ')');
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

			$query = "	UPDATE  #__joomleague_match
						SET projectteam1_id = NULL
						WHERE projectteam1_id in (select id from #__joomleague_project_team
												where project_id = '" . $data['id'] . "'
												AND team_id NOT IN  (" . $peids . "))";
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
			
			$query = "	UPDATE  #__joomleague_match
						SET projectteam2_id = NULL
						WHERE projectteam2_id in (select id from #__joomleague_project_team
												where project_id = '" . $data['id'] . "'
												AND team_id NOT IN  (" . $peids . "))";
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

		$ordering = "1";
		for($x = 0;$x < count($data['project_teamslist']);$x ++)
		{
			$query = "	INSERT IGNORE
						INTO #__joomleague_project_team
						(project_id, team_id, ordering)
						VALUES ( '" . $data['id'] . "', '" . $data['project_teamslist'][$x] . "', '" . $ordering ++ . "')";
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
		return $result;
	}

	/**
	 * Method to update checked project teams
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function storeshort($cid,$data)
	{
		$result = true;
		for($x = 0;$x < count($cid);$x ++)
		{

			$tblProjectteam = Table::getInstance('ProjectTeam','Table');
			$tblProjectteam->id = $cid[$x];
			if(isset($data['division_id' . $cid[$x]]))
			{
				$tblProjectteam->division_id = $data['division_id' . $cid[$x]];
			}
			$tblProjectteam->start_points = $data['start_points' . $cid[$x]];
			$tblProjectteam->points_finally = $data['points_finally' . $cid[$x]];
			$tblProjectteam->neg_points_finally = $data['neg_points_finally' . $cid[$x]];
			$tblProjectteam->matches_finally = $data['matches_finally' . $cid[$x]];
			$tblProjectteam->won_finally = $data['won_finally' . $cid[$x]];
			$tblProjectteam->draws_finally = $data['draws_finally' . $cid[$x]];
			$tblProjectteam->lost_finally = $data['lost_finally' . $cid[$x]];
			$tblProjectteam->homegoals_finally = $data['homegoals_finally' . $cid[$x]];
			$tblProjectteam->guestgoals_finally = $data['guestgoals_finally' . $cid[$x]];
			$tblProjectteam->diffgoals_finally = $data['diffgoals_finally' . $cid[$x]];

			if(! $tblProjectteam->check())
			{
				$this->setError($tblProjectteam->getError());
				$result = false;
			}
			if(! $tblProjectteam->store())
			{
				$this->setError($tblProjectteam->getError());
				$result = false;
			}
		}
		return $result;
	}


	/**
	 * Method to update checked project teams
	 *
	 * @access public
	 * @return boolean on success
	 */
	function storeshortinline($name,$value,$pk)
	{
		$result = true;

		$tblProjectteam = Table::getInstance('ProjectTeam','Table');
		$tblProjectteam->id = $pk;
		$tblProjectteam->$name = $value;
		if(!$tblProjectteam->check())
		{
			$this->setError($tblProjectteam->getError());
			$result = false;
		}
		if(!$tblProjectteam->store())
		{
			$this->setError($tblProjectteam->getError());
			$result = false;
		}

		return $result;
	}



	/**
	 * Method to return the teams array (id, name)
	 *
	 * @access public
	 * @return array
	 */
	function getTeams()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id AS value,name AS text,info');
		$query->from('#__joomleague_team');
		$query->order('text ASC');
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

	public function changeTeamId($arrOldTeamIds,$arrNewTeamIds)
	{
		$result = true;
		for($t = 0;$t < sizeof($arrOldTeamIds);$t ++)
		{
			$app = Factory::getApplication();

			$project_team_id = $arrOldTeamIds[$t];
			$team_id_new = $arrNewTeamIds[$project_team_id];

			$tblProjectTeam = Table::getInstance('ProjectTeam','Table');
			$tblProjectTeam->load($project_team_id);

			$tblOldTeam = Table::getInstance('Team','Table');
			$tblOldTeam->load($tblProjectTeam->team_id);
			$old_team_name = $tblOldTeam->name;

			$tblNewTeam = Table::getInstance('Team','Table');
			$tblNewTeam->load($team_id_new);
			$new_team_name = $tblNewTeam->name;

			$app->enqueueMessage(Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ASSIGNED_OLD_TEAMNAME',$old_team_name,$new_team_name),
					'Notice');

			$tblProjectTeam->id = $project_team_id;
			$tblProjectTeam->team_id = $team_id_new;

			if(! $tblProjectTeam->store())
			{
				$this->setError($tblProjectTeam->getError());
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Method to return a Teams array (id,name)
	 *
	 * @access public
	 * @return array seasons
	 */
	function getAllTeams($pid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		
		if($pid)
		{
			// jetzt brauchen wir noch das land der liga !
			$querycountry = "SELECT l.country
							from #__joomleague_league as l
							inner join #__joomleague_project as p
							on p.league_id = l.id
							where p.id = '$pid'
							";

			$db->setQuery($querycountry);
			$country = $db->loadResult();

			$query = "SELECT t.id as value, CASE WHEN CHAR_LENGTH(t.info) THEN CONCAT(t.name, ' (', t.info, ')') ELSE t.name END AS text
					FROM #__joomleague_team as t
					INNER JOIN #__joomleague_club as c
					ON c.id = t.club_id
					WHERE c.country = '$country'
					ORDER BY t.name ASC
					";
		}
		else
		{
			$query = 'SELECT id as value, name as text
					FROM #__joomleague_team
					ORDER BY name ASC ';
		}
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
		foreach($result as $teams)
		{
			$teams->name = $teams->text;
		}
		return $result;
	}

	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @param
	 *        	$project_id
	 * @access public
	 * @return array
	 */
	function getProjectTeams($project_id = 0)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.id AS value,t.name AS text,t.notes');
		$query->from('#__joomleague_team AS t');

		$query->select('pt.info');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');

		$query->where('pt.project_id = ' . $project_id);
		$query->order('text ASC ');
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

	/**
	 * copy teams to other projects
	 *
	 * @param int $dest
	 *        	destination project id
	 * @param array $ptids
	 *        	teams to transfer
	 */
	function copy($dest,$ptids)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if(! $dest)
		{
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_Destination_project_required'));
			return false;
		}

		if(!is_array($ptids) || ! count($ptids))
		{
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_no_teams_to_copy'));
			return false;
		}

		// first copy the teams
		$query = ' INSERT INTO #__joomleague_project_team (team_id, project_id, info, picture, standard_playground, extended)' . ' SELECT team_id, ' .
				 $dest . ', info, picture, standard_playground, extended ' . ' FROM #__joomleague_project_team ' . ' WHERE id IN (' .
				 implode(',',$ptids) . ')';
				 try
				 {
				 	$db->setQuery($query);
				 	$res = $db->execute();
				 }
				 catch (Exception $e)
				 {
				 	$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				 	return false;
				 }

		// now copy the players
		$query = ' INSERT INTO #__joomleague_team_player (projectteam_id, person_id, jerseynumber, picture, extended, published) ' .
				 ' SELECT dest.id AS projectteam_id, tp.person_id, tp.jerseynumber, tp.picture, tp.extended,tp.published ' .
				 ' FROM #__joomleague_team_player AS tp ' . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id ' .
				 ' INNER JOIN #__joomleague_project_team AS dest ON pt.team_id = dest.team_id AND dest.project_id = ' . $dest . ' WHERE pt.id IN (' .
				 implode(',',$ptids) . ')';
		$db->setQuery($query);
		$res = $db->execute();

		// and finally the staff
		$query = ' INSERT INTO #__joomleague_team_staff (projectteam_id, person_id, picture, extended, published) ' .
				 ' SELECT dest.id AS projectteam_id, tp.person_id, tp.picture, tp.extended,tp.published ' . ' FROM #__joomleague_team_staff AS tp ' .
				 ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id ' .
				 ' INNER JOIN #__joomleague_project_team AS dest ON pt.team_id = dest.team_id AND dest.project_id = ' . $dest . ' WHERE pt.id IN (' .
				 implode(',',$ptids) . ')';
				 try
				 {
				 	$db->setQuery($query);
				 	$res = $db->execute();
				 }
				 catch (Exception $e)
				 {
				 	$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				 	return false;
				 }

		return true;
	}

	/**
	 * return count of projectteams
	 *
	 * @param
	 *        	int project_id
	 * @return int
	 */
	function getProjectTeamsCount($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(pt.id) AS count');
		$query->from('#__joomleague_project_team AS pt');
		$query->join('LEFT','#__joomleague_project AS p on p.id = pt.project_id');
		$query->where('p.id = ' . $project_id);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
