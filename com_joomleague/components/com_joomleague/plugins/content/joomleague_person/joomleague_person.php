<?php
/**
 * Joomleague - Person plugin
 * 
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

class plgContentJoomleague_Person extends CMSPlugin
{
	public $params = null;

	public function __construct(&$subject, $config = array())
	{
		$app = Factory::getApplication();
		
		parent::__construct($subject);
		
		// Get the parameters.
		if (isset($config['params']))
		{
			if ($config['params'] instanceof Registry)
			{
				$this->params = $config['params'];
			}
			else
			{
				$this->params = new Registry;
				$this->params->loadString($config['params']);
			}
		}
		
		// load language file for frontend
		CMSPlugin::loadLanguage('plg_content_joomleague_person',JPATH_ADMINISTRATOR);
	}
	
	
	/**
	 * onContentPrepare
	 */
	public function onContentPrepare($context, &$row, $params, $page = 0)
	{
		$db = Factory::getDbo();
		
		if (StringHelper::strpos($row->text,'jl_player') === false)
		{
			return;
		}
			
		$regex = "#{jl_player}(.*?){/jl_player}#s";
		
		// check for jl_player
		$match = preg_match_all($regex,$row->text,$matches);

		if ($match)
		{			
			require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';
			foreach ($matches[0] as $match)
			{
				$name = preg_replace("/{.+?}/", "", $match);

				$aname = explode(" ", html_entity_decode($name) );
				$firstname = $aname[0];
				$lastname = $aname[1];	

				$db       = Factory::getDbo();
				$query = $db->getQuery(true);
				
				$query->select(array('pr.id AS pid','pr.firstname','pr.lastname'));
				$query->from('#__joomleague_person AS pr');
				
				// join team_player table
				$query->select(array('tp.person_id','tp.id AS tpid','tp.project_position_id'));
				$query->join('INNER','#__joomleague_team_player AS tp ON tp.person_id = pr.id');
				
				// join project_team table
				$query->select(array('pt.project_id','pt.id AS ptid'));
				$query->join('INNER','#__joomleague_project_team AS pt ON pt.id = tp.projectteam_id');
				
				// join team table
				$query->select(array('t.name AS team_name','t.id AS team_id'));
				$query->join('INNER','#__joomleague_team AS t ON t.id = pt.team_id');
				
				// join project table
				$query->select(array('p.name AS project_name'));
				$query->join('INNER','#__joomleague_project AS p ON p.id = pt.project_id');
				
				// join season table
				$query->select(array('s.name AS season_name'));
				$query->join('INNER','#__joomleague_season AS s ON s.id = p.season_id');
				
				// join league table
				$query->join('INNER','#__joomleague_league AS l ON l.id = p.league_id');
				
				// join project_position table
				$query->join('INNER','#__joomleague_project_position AS ppos ON ppos.id = tp.project_position_id');
				
				// join position table
				$query->select(array('pos.name AS position_name','pos.id AS posID'));
				$query->join('INNER','#__joomleague_position AS pos ON pos.id = ppos.position_id');
				
				// case when statement - team
				$case_when_team_alias = ' CASE WHEN ';
				$case_when_team_alias .= $query->charLength('t.alias', '!=', '0');
				$case_when_team_alias .= ' THEN ';
				$t_id = $query->castAsChar('t.id');
				$case_when_team_alias .= $query->concatenate(array($t_id, 't.alias'), ':');
				$case_when_team_alias .= ' ELSE ';
				$case_when_team_alias .= $t_id . ' END AS team_slug';
				$query->select($case_when_team_alias);
				
				// case when statement - project
				$case_when_project_alias = ' CASE WHEN ';
				$case_when_project_alias .= $query->charLength('p.alias', '!=', '0');
				$case_when_project_alias .= ' THEN ';
				$p_id = $query->castAsChar('p.id');
				$case_when_project_alias .= $query->concatenate(array($p_id, 'p.alias'), ':');
				$case_when_project_alias .= ' ELSE ';
				$case_when_project_alias .= $p_id . ' END AS project_slug';
				$query->select($case_when_project_alias);
				
				// Filters
				$query->where(array(
						'pr.firstname = '. $db->Quote($firstname),
						'pr.lastname = '. $db->Quote($lastname),
						'p.published = 1',
						'tp.published = 1',
						'pr.published = 1'
				));
						
				$query->order('p.id DESC');
				
				// run query
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
				// get result
				// replace only if project id set
				if (isset($rows[0]->project_id))
				{
					$personid = $rows[0]->pid;
					$projectid = $rows[0]->project_id;
					$teamid = $rows[0]->team_id;
					$url = JoomLeagueHelperRoute::getPlayerRoute($projectid, $teamid, $personid, null);
					$link = '<a class="player" href="' . $url . '">';
					$row->text = preg_replace("#{jl_player}" . $name . "{/jl_player}#s", $link . $name . "</a>", $row->text);
				}
				else
				{
					$row->text = preg_replace("#{jl_player}" . $name . "{/jl_player}#s", $name, $row->text);
				}
			}
			return true;
		}
	}
}
