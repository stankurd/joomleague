<?php
/**
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Playground
 */
class JoomleagueModelPlayground extends JoomleagueModelProject
{
    var $projectid = 0;
    var $playgroundid = 0;
    var $playground = null;

    function __construct()
    {
        parent::__construct();

        $app = Factory::getApplication();
        $input = $app->input;
        
        $this->projectid = $input->getInt('p', 0);
        $this->playgroundid = $input->getInt('pgid', 0);
    }

    function getPlayground()
    {
        if (is_null($this->playground))
        {
            $app = Factory::getApplication();
            $input = $app->input;
            $pgid = $input->getInt('pgid', 0);
            if ($pgid > 0)
            {
                $this->playground = $this->getTable('Playground','Table');
                $this->playground->load($pgid);
            }
        }
        return $this->playground;
    }

    function getAddressString()
    {
        $playground = $this->getPlayground();
        $address_string = $playground->address . ', ' . $playground->zipcode . ' ' .$playground->city;
        return $address_string;
    }

    // TODO: this function is inherited from the project model, but its signature is different
    // (which is not allowed in PHP (gives warning)). Look into how this can be solved best.
    function getTeams($division = 0)
    {
        $teams = array();

        $playground = $this->getPlayground();
        
        if ($playground->id > 0)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select($db->quoteName('pt.id'))
                ->select($db->quoteName('pt.team_id'))
                ->select($db->quoteName('pt.project_id'))
                ->select($db->quoteName('t.name', 'team_name'))
                ->select($db->quoteName('t.short_name', 'team_short_name'))
                ->select($db->quoteName('t.notes', 'team_notes'))
                ->select($db->quoteName('p.name', 'project_name'))
                ->from($db->quoteName('#__joomleague_project_team', 'pt'))
                ->join('INNER', $db->quoteName('#__joomleague_team', 't') .
                    ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt.team_id'))
                ->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
                    ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pt.project_id'))
                ->where($db->quoteName('standard_playground') . ' = ' . (int)$playground->id);

            $db->setQuery($query);
            $teams = $db->loadObjectList();
        }
        return $teams;
    }

    function getNextGames($project_id = 0, $bShowReferees = 0)
    {
        $playground = $this->getPlayground();
        
        if ($playground->id > 0)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('m.*')
                ->select('DATE_FORMAT(' . $db->quoteName('m.time_present') . ', "%H:%i") AS time_present')
                ->select($db->quoteName('p.name', 'project_name'))
                ->select($db->quoteName('p.timezone'))
                ->select($db->quoteName('pt1.team_id', 'team1'))
                ->select($db->quoteName('pt2.team_id', 'team2'))
                ->from($db->quoteName('#__joomleague_match', 'm'))
                ->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
                    ' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
                ->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
                    ' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
                ->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
                    ' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('pt1.project_id'))
                ->join('INNER', $db->quoteName('#__joomleague_team', 't') .
                    ' ON ' . $db->quoteName('t.id') . ' = ' . $db->quoteName('pt1.team_id'))
                ->join('INNER', $db->quoteName('#__joomleague_club', 'c') .
                    ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.club_id'))
                ->where($db->quoteName('p.published') . ' = 1')
                ->where($db->quoteName('m.published') . ' = 1')
                ->where($db->quoteName('m.match_date') . ' > NOW()')
                ->where('(' . $db->quoteName('m.playground_id') . ' = ' . (int)$playground->id .
                    ' OR (' . $db->quoteName('pt1.standard_playground') . ' = ' . (int)$playground->id .
                        ' AND ' . $db->quoteName('m.playground_id') . ' IS NULL)' .
                    ' OR (' . $db->quoteName('c.standard_playground') . ' = ' . (int)$playground->id .
                        ' AND ' . $db->quoteName('m.playground_id') . '))');

            if ($project_id > 0)
            {
                $query
                    ->where($db->quoteName('project_id') . ' = ' . (int)$project_id);
            }
            $query
                ->group($db->quoteName('m.id'))
                ->order($db->quoteName('match_date') . ' ASC');

            $db->setQuery($query);
            $matches = $db->loadObjectList();
            if ($matches)
            {
            	foreach ($matches as $match)
            	{
            		JoomleagueHelper::convertMatchDateToTimezone($match);
            	}
            }
            if ($bShowReferees > 0)
            {
            	$project = $this->getProject();
            	$this->_getRefereesByMatch($matches, $project);
            }
        }
        return $matches;
    }

    function getTeamLogo($team_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select($db->quoteName('c.logo_small'))
            ->select($db->quoteName('c.country'))
            ->from($db->quoteName('#__joomleague_team', 't'))
            ->join('LEFT', $db->quoteName('#__joomleague_club', 'c') .
                ' ON ' . $db->quoteName('c.id') . ' = ' . $db->quoteName('t.club_id'))
            ->where($db->quoteName('t.id') . ' = ' . (int)$team_id);

        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getTeamsFromMatches(&$games)
    {
        $teams = array();
       
        if (count($games))
        {
            foreach ($games as $m)
            {
                $teamsId[] = (int)$m->team1;
                $teamsId[] = (int)$m->team2;
            }
            $listTeamId = implode(',', array_unique($teamsId));

            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select(array('t.id','t.name'));
            $query->from('#__joomleague_team AS t');
            $query->where('t.id IN (' . $listTeamId . ')');

            $db->setQuery($query);
            $result = $db->loadObjectList('id');
            $teams = $result;
//            foreach ($result as $r)
//            {
//                $teams[$r->id] = $r;
//            }
        }

        return $teams;
    }
    
    function _getRefereesByMatch($matches, $joomleague)
    {
    	for ($index = 0; $index < count($matches); $index++)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            if ($joomleague->teams_as_referees)
            {
                $query
                    ->select($db->quoteName('t.name', 'referee_name'))
                    ->from($db->quoteName('#__joomleague_team', 't'))
                    ->join('LEFT', $db->quoteName('#__joomleague_match_referee', 'mref') .
                        ' ON ' . $db->quoteName('mref.project_referee_id') . ' = ' . $db->quoteName('t.id'))
                    ->where($db->quoteName('mref.match_id') . ' = ' . (int)$matches[$index]->id)
                    ->order($db->quoteName('mref.ordering'));
            }
            else
            {
                $query
                    ->select($db->quoteName('per.firstname', 'referee_firstname'))
                    ->select($db->quoteName('per.lastname', 'referee_lastname'))
                    ->select($db->quoteName('per.id', 'referee_id'))
                    ->select($db->quoteName('ppos.position_id'))
                    ->select($db->quoteName('pos.name', 'referee_position_name'))
                    ->from($db->quoteName('#__joomleague_person', 'per'))
                    ->join('LEFT', $db->quoteName('#__joomleague_project_referee', 'pref') .
                        ' ON ' . $db->quoteName('pref.person_id') . ' = ' . $db->quoteName('per.id'))
                    ->join('LEFT', $db->quoteName('#__joomleague_match_referee', 'mref') .
                        ' ON ' . $db->quoteName('mref.project_referee_id') . ' = ' . $db->quoteName('pref.id'))
                    ->join('INNER', $db->quoteName('#__joomleague_project_position', 'ppos') .
                        ' ON ' . $db->quoteName('ppos.id') . ' = ' . $db->quoteName('mref.project_position_id'))
                    ->join('INNER', $db->quoteName('#__joomleague_position', 'pos') .
                        ' ON ' . $db->quoteName('pos.id') . ' = ' . $db->quoteName('ppos.position_id'))
                    ->where($db->quoteName('mref.match_id') . ' = ' . (int)$matches[$index]->id)
                    ->where($db->quoteName('per.published') . ' = 1')
                    ->order($db->quoteName('mref.ordering'));
            }

    		$db->setQuery($query);
    		if (!$referees = $db->loadObjectList())
    		{
    			//$this->setError($this->_db->getErrorMsg());
				return false;
    		}
    		$matches[$index]->referees = $referees;
    	}
    	return $matches;
    }    
}
