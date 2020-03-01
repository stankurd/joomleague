<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');
require_once JLG_PATH_SITE.'/models/roster.php';
require_once JLG_PATH_SITE.'/models/project.php';

class JoomleagueModelRosteralltime extends JoomleagueModelProject
{
	var $projectid=0;
	var $projectteamid=0;
	var $projectteam=null;
	var $team=null;
	var $teamid=0;


	/**
	 * caching for team in out stats
	 * @var array
	 */
	var $_teaminout=null;

	/**
	 * caching players
	 * @var array
	 */
	var $_players=null;

	function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid=$input->getInt('p',0);
		$this->teamid=$input->getInt('tid',0);
		$this->projectteamid=$input->getInt('ttid',0);
	}
    
    function getPlayerPosition()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);        
        $query = "SELECT po.*
        from #__joomleague_position as po
        where po.parent_id != '0' 
        and persontype = '1'";

$db->setQuery( $query );
return $db->loadObjectList();    
        
    }
    
    function getPositionEventTypes($positionId=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$result=array();
		$query='	SELECT	pet.*,
							
							et.name AS name,
							et.icon AS icon
					FROM #__joomleague_position_eventtype AS pet
					INNER JOIN #__joomleague_eventtype AS et ON et.id = pet.eventtype_id
					WHERE et.published=1 ';
		
		$query .= ' ORDER BY pet.ordering, et.ordering';
		$db->setQuery($query);
		$result=$db->loadObjectList();
		if ($result)
		{
			if ($positionId)
			{
				return $result;
			}
			else
			{
				$posEvents=array();
				foreach ($result as $r)
				{
					$posEvents[$r->position_id][]=$r;
				}
				return ($posEvents);
			}
		}
		return array();
	}
	
	
    function getTeam()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if (is_null($this->team))
		{
			if (!$this->teamid)
			{
				$this->setError(Text::_('Missing team id'));
				return false;
			}
			if (!$this->projectid)
			{
				$this->setError(Text::_('Missing project id'));
				return false;
			}
			$query='	SELECT	t.*,
								CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS slug
						FROM #__joomleague_team AS t
						WHERE t.id='.$db->Quote($this->teamid);
			$db->setQuery($query);
			$this->team=$db->loadObject();
		}
		return $this->team;
	}


	/**
	 * return team players by positions
	 * @return array
	 */
	function getTeamPlayers()
	{
		if (empty($this->_players))
		{
		    $db = Factory::getDbo();
		    $query = $db->getQuery(true);
		    $query
		    ->select($db->quoteName('pr.firstname'))
		    ->select($db->quoteName('pr.nickname'))
		    ->select($db->quoteName('pr.lastname'))
		    ->select($db->quoteName('pr.country'))
		    ->select($db->quoteName('pr.birthday'))
		    ->select($db->quoteName('pr.deathday'))
		    ->select($db->quoteName('tp.id' , 'playerid'))
		    ->select($db->quoteName('pr.id' , 'pid'))
		    ->select($db->quoteName('pr.picture' , 'ppic'))
		    ->select($db->quoteName('tp.jerseynumber' , 'position_number'))
		    ->select($db->quoteName('tp.notes' , 'description'))
		    ->select($db->quoteName('tp.injury' , 'injury'))
		    ->select($db->quoteName('tp.suspension' , 'suspension'))
		    ->select($db->quoteName('pt.team_id'))
		    ->select($db->quoteName('tp.away' , 'away'))
		    ->select($db->quoteName('tp.picture'))
		    ->select($db->quoteName('pos.name' , 'position'))
		    ->select($db->quoteName('ppos.position_id'))
		    ->select($db->quoteName('ppos.id' , 'pposid'))
		    ->select($this->constructSlug($db, 'slug', 'pr.alias', 'pr.id'))
		    ->from($db->quoteName('#__joomleague_team_player' , 'tp'))
		    ->innerJoin($db->quoteName('#__joomleague_project_team' , 'pt') . ' ON ' . $db->quoteName('pt.id') . ' = ' . $db->quoteName('tp.projectteam_id'))
		    ->innerJoin($db->quoteName('#__joomleague_person' , 'pr') . ' ON ' . $db->quoteName('tp.person_id') . ' = ' . $db->quoteName('pr.id'))
		    ->innerJoin($db->quoteName('#__joomleague_project_position' , 'ppos') . ' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('tp.project_position_id'))
		    ->innerJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
		   
		    ->where($db->quoteName('pt.team_id') . ' = ' . $db->quote($this->teamid))
		    ->where($db->quoteName('pr.published') . ' = ' . ('1'))
		    ->where($db->quoteName('tp.published'). ' = ' . ('1'))
		    ->order("pos.ordering, ppos.position_id, tp.jerseynumber, pr.lastname, pr.firstname");
		        try {
						$db->setQuery($query);		  
						//$this->_players = $db->loadObjectList();
						$this->_all_time_players = $db->loadObjectList('pid');
		           
		       } catch (Exception $e) {
		           Factory::getApplication()->enqueueMessage($e->getMessage());
		       } 
		    }
		
		foreach ($this->_players as $player)
		{
		$player->start = 0;
		$player->came_in = 0;
		$player->out = 0;
        
        if (  !isset($this->_all_time_players[$player->pid]->start) )
        {
            $this->_all_time_players[$player->pid]->start = 0;
        }
        if (  !isset($this->_all_time_players[$player->pid]->came_in) )
        {
            $this->_all_time_players[$player->pid]->came_in = 0;
        }
        if (  !isset($this->_all_time_players[$player->pid]->out) )
        {
            $this->_all_time_players[$player->pid]->out = 0;
        }
        
        /*        
        $query = '	SELECT count(*) as total
        FROM #__joomleague_match_player
        WHERE came_in = 0  
        and teamplayer_id = ' . $player->playerid;
        $db->setQuery( $query );
        $player->start = $db->loadResult();
        $this->_all_time_players[$player->pid]->start = $this->_all_time_players[$player->pid]->start + $player->start;
        		
        $query = '	SELECT count(*) as total
        FROM #__joomleague_match_player
        WHERE came_in = 1  
        and teamplayer_id = ' . $player->playerid;
        $db->setQuery( $query );
        $player->came_in = $db->loadResult();
        $this->_all_time_players[$player->pid]->came_in = $this->_all_time_players[$player->pid]->came_in + $player->came_in;
        
        $query = '	SELECT count(*) as total
        FROM #__joomleague_match_player
        WHERE out = 1  
        and teamplayer_id = ' . $player->playerid;
        $db->setQuery( $query );
        $player->out = $db->loadResult();
        $this->_all_time_players[$player->pid]->out = $this->_all_time_players[$player->pid]->out + $player->out;
        */

        $query = $db->getQuery(true);
        $query	= ' SELECT tp1.id AS tp_id1, '
					. ' tp1.person_id AS person_id1, '
					. ' tp2.id AS tp_id2, '
					. ' tp2.person_id AS person_id2,'
					. ' m.id AS mid, '
					. ' mp.came_in, mp.out, mp.in_for'
					. ' FROM #__joomleague_match AS m'
					. ' INNER JOIN #__joomleague_round r ON m.round_id = r.id '
					. ' INNER JOIN #__joomleague_project AS p ON p.id = r.project_id '
					. ' INNER JOIN #__joomleague_match_player AS mp ON mp.match_id = m.id '
					. ' INNER JOIN #__joomleague_team_player AS tp1 ON tp1.id = mp.teamplayer_id'
					. ' LEFT JOIN #__joomleague_team_player AS tp2 ON tp2.id = mp.in_for'
					. ' WHERE tp1.id = '.$player->playerid
					. ' AND m.published = 1 '
					. ' AND p.published = 1 ';
			$db->setQuery($query);
			$rows = $db->loadObjectList();
foreach ($rows AS $row)
			{
				$this->_all_time_players[$player->pid]->start += ($row->came_in == 0);
				$this->_all_time_players[$player->pid]->came_in  += ($row->came_in == 1);
				$this->_all_time_players[$player->pid]->out += ($row->out == 1);

			
			}            
            
for($a=1; $a < 5; $a++ )
{
    $query = $db->getQuery(true);
    
    $query = '	SELECT count(*) as total
    FROM #__joomleague_match_event
    WHERE event_type_id = '.$a .' and teamplayer_id = ' . $player->playerid;
$db->setQuery( $query );
$event_type_id = 'event_type_id_'.$a;
$player->$event_type_id = $db->loadResult();
$this->_all_time_players[$player->pid]->$event_type_id = $this->_all_time_players[$player->pid]->$event_type_id + $player->$event_type_id;
}		
		
		
		
		}
		
		//return $this->_players;
		return $this->_all_time_players;
	}
	function getStaffList()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	    ->select($db->quoteName('pr.firstname'))
	    ->select($db->quoteName('pr.nickname'))
	    ->select($db->quoteName('pr.lastname'))
	    ->select($db->quoteName('pr.country'))
	    ->select($db->quoteName('pr.birthday'))
	    ->select($db->quoteName('pr.deathday'))
	    ->select($db->quoteName('ts.id' , 'ptid'))
	    ->select($db->quoteName('pr.id' , 'pid'))
	    ->select($db->quoteName('ppos.position_id'))
	    ->select($db->quoteName('ppos.id' , 'pposid'))
	    ->select($db->quoteName('pr.id' , 'pid'))
	    ->select($db->quoteName('pr.picture' , 'ppic'))
	    ->select($db->quoteName('pos.name' , 'position'))
	    ->select($db->quoteName('ts.picture'))
	    ->select($db->quoteName('ts.notes' , 'description'))
	    ->select($db->quoteName('ts.injury' , 'injury'))
	    ->select($db->quoteName('ts.suspension' , 'suspension'))
	    ->select($db->quoteName('ts.away' , 'away'))
	    ->select($db->quoteName('pos.parent_id'))
	    ->select($db->quoteName('posparent.name' , 'parentname'))
	    ->select($this->constructSlug($db, 'slug', 'pr.alias', 'pr.id'))
	    ->from($db->quoteName('#__joomleague_team_staff' , 'ts'))
	    ->innerJoin($db->quoteName('#__joomleague_person' , 'pr') . ' ON ' . $db->quoteName('ts.person_id') . ' = ' . $db->quoteName('pr.id'))
	    ->innerJoin($db->quoteName('#__joomleague_project_position' , 'ppos')
	        . ' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('ts.project_position_id'))
	    ->innerJoin($db->quoteName('#__joomleague_position' , 'pos') . ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
	    ->leftJoin($db->quoteName('#__joomleague_position' , 'posparent') . ' ON ' . $db->quoteName('pos.parent_id') . ' = ' . $db->quoteName('posparent.id'))	        
	    ->where($db->quoteName('ts.projectteam_id') . ' = ' . $db->quote($this->projectteamid))
	    ->where($db->quoteName('pr.published') . ' = ' . ('1'))
	    ->where($db->quoteName('ts.published'). ' = ' . ('1'))
	    ->order($db->quoteName('pos.parent_id') . ' , ' . $db->quoteName('pos.ordering'));
	        
	        $db->setQuery($query);
	        $stafflist=$db->loadObjectList();
		return $stafflist;
	}

}
?>