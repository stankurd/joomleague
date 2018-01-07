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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/list.php';

/**
 * Quickadd Model
 */
class JoomleagueModelQuickAdd extends JoomleagueModelList
{

	var $_identifier = "quickadd";
	
	/*
	 * @param {string} query - the search string
	 * @param {int} projectteam_id - the projectteam_id
	 */
	function getNotAssignedPlayers($searchterm, $projectteam_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = "	SELECT a.*, a.id as id2 
					FROM #__joomleague_person AS a
					WHERE	(	LOWER( CONCAT(a.firstname, ' ', a.lastname) ) LIKE " . $db->Quote("%" . $searchterm . "%") . " OR
								alias LIKE " . $db->Quote("%" . $searchterm . "%") . " OR
								nickname LIKE " . $db->Quote("%" . $searchterm . "%") . " OR
								id = " . $db->Quote($searchterm) . ")
								AND a.published = '1'
								AND a.id NOT IN ( SELECT person_id
								FROM #__joomleague_team_player AS tp
								WHERE	projectteam_id = ". $db->Quote($projectteam_id) . " AND
										tp.person_id = a.id ) ";

		$option 			= $app->input->get('option');
		$filter_order		= $app->getUserStateFromRequest( $option . 'pl_filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pl_filter_order_Dir',	'filter_order_Dir', '',	'word' );

		if ( $filter_order == 'a.ordering' )
		{
			$orderby 	= ' ORDER BY a.ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , a.ordering ';
		}
		$query = $query . $orderby;
		$db->setQuery( $query );
		
		if ( !$this->_data = $this->_getList( 	$query, $this->getState( 'limitstart' ), $this->getState( 'limit' ) ) )
		{
			echo $db->getErrorMsg();
		}
		$this->_total = $this->_getListCount( $query );
		return $this->_data;
	}

	/*
	 * @param {string} query - the search string
	 * @param {int} projectteam_id - the projectteam_id
	 */
	function getNotAssignedStaff($searchterm, $projectteam_id)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = "SELECT a.* ";
		$query .= "FROM #__joomleague_person AS a ";
		$query .= "WHERE (LOWER( CONCAT(a.firstname, ' ', a.lastname) ) LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR alias LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR nickname LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR a.id = ".$db->Quote($searchterm) . ") ";
		$query .= "   AND a.published = '1'";
		$query .= "   AND a.id NOT IN ( SELECT person_id ";
		$query .= "                     FROM #__joomleague_team_staff AS ts ";
		$query .= "                     WHERE projectteam_id = ". $db->Quote($projectteam_id);
		$query .= "                     AND ts.person_id = a.id ) ";
		
		$option = $app->input->get('option');
		$filter_order		= $app->getUserStateFromRequest( $option . 'pl_filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pl_filter_order_Dir',	'filter_order_Dir', '',	'word' );

		if ( $filter_order == 'a.ordering' )
		{
			$orderby 	= ' ORDER BY a.ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , a.ordering ';
		}
		$query = $query . $orderby;
		$db->setQuery( $query );
		if ( !$this->_data = $this->_getList( 	$query,
												$this->getState( 'limitstart' ),
												$this->getState( 'limit' ) ) )
		{
			echo $db->getErrorMsg();
		}
		$this->_total = $this->_getListCount( $query );
		return $this->_data;
	}

	/*
	 * @param {string} query - the search string
	 * @param {int} projectteam_id - the projectteam_id
	 */
	function getNotAssignedReferees($searchterm, $projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = "SELECT a.* ";
		$query .= "FROM #__joomleague_person AS a ";
		$query .= "WHERE (LOWER( CONCAT(a.firstname, ' ', a.lastname) ) LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR alias LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR nickname LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR a.id = ".$db->Quote($searchterm) . ") ";
		$query .= "   AND a.published = '1'";
		$query .= "   AND a.id NOT IN ( SELECT person_id ";
		$query .= "                     FROM #__joomleague_project_referee AS pr ";
		$query .= "                     WHERE project_id = ". $db->Quote($projectid);
		$query .= "                     AND pr.person_id = a.id ) ";
		$option = $app->input->get('option');
		$filter_order		= $app->getUserStateFromRequest( $option . 'pl_filter_order', 'filter_order', 'a.ordering', 'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pl_filter_order_Dir',	'filter_order_Dir', '',	'word' );

		if ( $filter_order == 'a.ordering' )
		{
			$orderby 	= ' ORDER BY a.ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , a.ordering ';
		}
		$query = $query . $orderby;
		
		$db->setQuery( $query );
		
		if ( !$this->_data = $this->_getList( 	$query,
												$this->getState( 'limitstart' ),
												$this->getState( 'limit' ) ) )
		{
			echo $db->getErrorMsg();
		}
		$this->_total = $this->_getListCount( $query );
		return $this->_data;
	}

	/*
	 * @param {string} query - the search string
	 * @param {int} projectteam_id - the projectteam_id
	 */
	function getNotAssignedTeams($searchterm, $projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query  = "SELECT t.* ";
		$query .= "FROM #__joomleague_team AS t ";
		$query .= "WHERE (LOWER( t.name ) LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR alias LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR LOWER( short_name ) LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR LOWER( middle_name ) LIKE ".$db->Quote("%".$searchterm."%")." ";
		$query .= "   OR id = ".$db->Quote($searchterm) . ") ";
		$query .= "   AND t.id NOT IN ( SELECT team_id ";
		$query .= "                     FROM #__joomleague_project_team AS pt ";
		$query .= "                     WHERE project_id = ". $db->Quote($projectid);
		$query .= ") ";

		$db->setQuery( $query);

		if ( !$this->_data = $this->_getList( 	$query,
												$this->getState( 'limitstart' ),
												$this->getState( 'limit' ) ) )
		{
			echo $db->getErrorMsg();
		}
		$this->_total = $this->_getListCount( $query );
		return $this->_data;
	}
	
	function addPlayer($projectteam_id, $personid, $name = null)
	{	
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if ( !$personid && empty($name) ) {
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_ADD_PLAYER_REQUIRES_ID_OR_NAME'));
			return false;
		}
		
		// add the new individual as their name was sent through.
		if (!$personid)
		{
			$mdlPerson = JLGModel::getInstance('Person', 'JoomleagueModel');
			$name = explode(" ", $name);
			$firstname = ''; $nickname=''; $lastname='';
			if(count($name) == 1) {
				$firstname = ucfirst($name[0]);
				$nickname = $name[0];
				$lastname = ".";
			}
			if(count($name) == 2) {
				$firstname = ucfirst($name[0]);
				$nickname = $name[1];
				$lastname = ucfirst($name[1]);
			}
			if(count($name) == 3) {
				$firstname = ucfirst($name[0]);
				$nickname = $name[1];
				$lastname = ucfirst($name[2]);
			}
			$data = array(
					"firstname" => $firstname,
					"nickname" => $nickname,
					"lastname" => $lastname,
					"published" => 1
			);
			$personid = $mdlPerson->save($data);
		}
	
		if (!$personid) {
			$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_QUICKADD_CTRL_FAILED_ADDING_PERSON'));
			return false;
		}
		
		$personId = JoomleagueHelper::getContentBetweenDelimiters($personid,'[',']');
		
		// check if indivual belongs to project team already
		$query = ' SELECT person_id FROM #__joomleague_team_player '
		. ' WHERE projectteam_id = '. $db->Quote($projectteam_id)
		. '   AND person_id = '. $db->Quote($personid);
		$db->setQuery($query);
		$res = $db->loadResult();
		if (!$res)
		{
			$tblTeamplayer = Table::getInstance( 'TeamPlayer', 'Table' );
			$tblTeamplayer->person_id		= $personid;
			$tblTeamplayer->projectteam_id	= $projectteam_id;
				
			$tblProjectTeam = Table::getInstance( 'ProjectTeam', 'Table' );
			$tblProjectTeam->load($projectteam_id);
	
			if ( !$tblTeamplayer->check() )
			{
				$this->setError( $tblTeamplayer->getError() );
				return false;
			}
			// Get data from player
			$query = "	SELECT picture, position_id
							FROM #__joomleague_person AS pl
							WHERE pl.id=". $db->Quote($personid) . "
							AND pl.published = 1";
	
			$db->setQuery( $query );
			$person = $db->loadObject();
			if ( $person )
			{
				if ($person->position_id)
				{
					$query = "SELECT id FROM #__joomleague_project_position ";
					$query.= " WHERE position_id = " . $person->position_id;
					$query.= " AND project_id = " . $tblProjectTeam->project_id;
					$db->setQuery($query);
					if ($resPrjPosition = $db->loadObject())
					{
						$tblTeamplayer->project_position_id = $resPrjPosition->id;
					}
				}
	
				$tblTeamplayer->picture			= $person->picture;
				$tblTeamplayer->projectteam_id	= $projectteam_id;
	
			}
			$query = "	SELECT max(ordering) count
								FROM #__joomleague_team_player";
			$db->setQuery( $query );
			$tp = $db->loadObject();
			$tblTeamplayer->ordering = (int) $tp->count + 1;
				
			if ( !$tblTeamplayer->store() )
			{
				$this->setError( $tblTeamplayer->getError() );
				return false;
			}
		}
		return true;
	}
}
