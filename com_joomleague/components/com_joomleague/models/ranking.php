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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/helpers/ranking.php';
require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Ranking
 */
class JoomleagueModelRanking extends JoomleagueModelProject
{
	var $projectid = 0;
	var $round = 0;
	var $rounds = array(0);
	var $part = 0;
	var $type = 0;
	var $from = 0;
	var $to = 0;
	var $divLevel = 0;
	var $currentRanking = array();
	var $previousRanking = array();
	var $homeRank = array();
	var $awayRank = array();
	var $colors = array();
	var $result = array();
	var $pageNav = array();
	var $pageNav2 = array();
	var $current_round = 0;

	function __construct()
	{
		parent::__construct();
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid = $input->getInt('p', 0);
		$this->round = $input->getInt('r', $this->getCurrentRound());
		$this->part  = $input->getInt('part', 0);
		$this->from  = $input->getInt('from', $this->round);
		$this->to	 = $input->getInt('to', $this->round);
		$this->type  = $input->getInt('type', 0);
		$this->last  = $input->getInt('last', 0);
		$this->selDivision = $input->getInt('division', 0);
	}

	
	/**
	 * get previous games for each team
	 * 
	 * @return array games array indexed by project team ids
	 */
	function getPreviousGames()
	{
		if (!$this->round) {
			return false;
		}
		
		// current round roundcode
		$rounds = $this->getRounds();
		$current = null;
		foreach ($rounds as $r)
		{
			if ($r->id == $this->round)
			{
				$current = $r;
				break;
			}
		}
		if (!$current)
		{
			return false;
		}

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select('m.*')
			->select($db->quoteName('r.roundcode'))
			->select($this->constructCombiSlug($db, 'slug', 't1.alias', 't2.alias', 'm.id'))
			->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
			->from($db->quoteName('#__joomleague_match', 'm'))
			->join('INNER', $db->quoteName('#__joomleague_round', 'r') .
				' ON ' . $db->quoteName('r.id') . ' = ' . $db->quoteName('m.round_id'))
			->join('INNER', $db->quoteName('#__joomleague_project', 'p') .
				' ON ' . $db->quoteName('p.id') . ' = ' . $db->quoteName('r.project_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt1') .
				' ON ' . $db->quoteName('pt1.id') . ' = ' . $db->quoteName('m.projectteam1_id'))
			->join('INNER', $db->quoteName('#__joomleague_project_team', 'pt2') .
				' ON ' . $db->quoteName('pt2.id') . ' = ' . $db->quoteName('m.projectteam2_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't1') .
				' ON ' . $db->quoteName('t1.id') . ' = ' . $db->quoteName('pt1.team_id'))
			->join('INNER', $db->quoteName('#__joomleague_team', 't2') .
				' ON ' . $db->quoteName('t2.id') . ' = ' . $db->quoteName('pt2.team_id'))
			->where($db->quoteName('r.project_id') . ' = ' . (int)$this->projectid)
			->where($db->quoteName('r.roundcode') . ' <= ' . (int)$current->roundcode)
			->where($db->quoteName('m.team1_result') . ' IS NOT NULL');

		// previous games of each team, until current round
		if($this->selDivision > 0)
		{
			$query
				->where('(' . $db->quoteName('pt1.division_id') . ' = ' . (int)$this->selDivision .
					' OR ' . $db->quoteName('pt2.division_id') . ' = ' . (int)$this->selDivision . ')');
		}
		$query
			->order($db->quoteName('r.roundcode') . ' ASC');

		$db->setQuery($query);
		$games = $db->loadObjectList();
		$teams = $this->getTeamsIndexedByPtid();

		// get last x games
		$config = $this->getTemplateConfig('ranking');
		$nb_games = $config['nb_previous'];
		// get games per team
		$res = array();
		foreach ($teams as $ptid => $team)
		{
			$teamGames = array();
			foreach ((array) $games as $g)
			{
				if ($g->projectteam1_id == $team->projectteamid || $g->projectteam2_id == $team->projectteamid) {
					$teamGames[] = $g;
				}
			}
			$res[$ptid] = count($teamGames) > 0 ? array_slice($teamGames, -$nb_games) : array();
		}
		return $res;
	}
		
	/**
	 * computes the ranking
	 *
	 */
	function computeRanking()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$project = $this->getProject();
		$mdlRound = BaseDatabaseModel::getInstance('Round', 'JoomleagueModel');
		$mdlRounds = BaseDatabaseModel::getInstance('Rounds', 'JoomleagueModel');
		$mdlRounds->setProjectId($project->id);
		
		$firstRound = $mdlRounds->getFirstRound($project->id);
		$lastRound = $mdlRounds->getLastRound($project->id);

		// url if no sef link comes along (ranking form)
		$url = JoomleagueHelperRoute::getRankingRoute($this->projectid);
		$tableconfig = $this->getTemplateConfig('ranking');

		if ($this->round == 0)
		{
			$this->round = $this->getCurrentRound();
		}

		$this->rounds = $this->getRounds();
		if ($this->part == 1)
		{
			$this->from = $firstRound['id'];
			$this->to = $this->rounds[intval(count($this->rounds) / 2)]->id;
		}
		elseif ($this->part == 2)
		{
			$this->from = $this->rounds[intval(count($this->rounds) / 2) + 1]->id;
			$this->to = $lastRound['id'];
		}
		else
		{
			$this->from = $input->getInt('from', $firstRound['id']);
			$this->to = $input->getInt('to', $lastRound['id']);
//			if ($input->getInt('to') == '0') {
//				$this->to   = $this->round;
//			}
//			else
//			{
//				$this->to   = $input->getInt('to', $this->round, $lastRound['id']);
//			}
		}
		if($this->part > 0)
		{
			$url .= '&amp;part=' . $this->part;
		}
		elseif ($this->from != 1 || $this->to != $this->round)
		{
			$url .= '&amp;from=' . $this->from . '&amp;to=' . $this->to;
		}
		$this->type = $input->getInt('type', 0);
		if ($this->type > 0)
		{
			$url .= '&amp;type=' . $this->type;
		}

		$this->divLevel = 0;

		//for sub division ranking tables
		if ($project->project_type=='DIVISIONS_LEAGUE')
		{
			$selDivision = $input->getInt('division', 0);
			$this->divLevel = $input->getInt('divLevel', $tableconfig['default_division_view']);

			if ($selDivision > 0)
			{
				$url .= '&amp;division=' . $selDivision;
				$divisions = array($selDivision);
			}
			else
			{
				// check if division level view is allowed. if not, replace with default
				if (($this->divLevel == 0 && $tableconfig['show_project_table'] == 0) ||
					 ($this->divLevel == 1 && $tableconfig['show_level1_table'] == 0) ||
					 ($this->divLevel == 2 && $tableconfig['show_level2_table'] == 0))
				{
					$this->divLevel = $tableconfig['default_division_view'];
				}
				$url .= '&amp;divLevel=' . $this->divLevel;
				$divisions = $this->divLevel ? $this->getDivisionsId($this->divLevel) : array(0);
			}
		}
		else
		{
			$divisions = array(0); //project
		}
		$selectedvalue = 0;

		$last = $input->getInt('last', 0);
		if ($last > 0)
		{
			$url .= '&amp;last=' . $last;
		}
		if ($input->getInt('sef', 0) == 1)
		{
			$app->redirect(JRoute::_($url));
		}

		/**
		* create ranking object	
		*
		*/
		$ranking = JLGRanking::getInstance($project);
		$ranking->setProjectId($this->projectid);
		foreach ($divisions as $division)
		{
			//away rank
			if ($this->type == 2)
			{
				$this->currentRanking[$division] = $ranking->getRankingAway($this->from, $this->to, $division);
			}
			//home rank
			else if ($this->type == 1)
			{
				$this->currentRanking[$division] = $ranking->getRankingHome($this->from, $this->to, $division);
			}
			//total rank
			else
			{
				$this->currentRanking[$division]	= $ranking->getRanking($this->from, $this->to, $division);
				$this->homeRank[$division]			= $ranking->getRankingHome($this->from, $this->to, $division);
				$this->awayRank[$division]			= $ranking->getRankingAway($this->from, $this->to, $division);
			}
			$this->_sortRanking($this->currentRanking[$division]);

			//previous rank
			if($tableconfig['last_ranking']==1)
			{
				if ($this->to == 1 || ($this->to == $this->from))
				{
					$this->previousRanking[$division] = &$this->currentRanking[$division];
				}
				else
				{	
					//away rank
					if ($this->type == 2)
					{
						$this->previousRanking[$division] = $ranking->getRankingAway($this->from,
							$this->_getPreviousRoundId($this->to), $division);
					}
					//home rank
					else if ($this->type == 1)
					{
						$this->previousRanking[$division] = $ranking->getRankingHome($this->from,
							$this->_getPreviousRoundId($this->to), $division);
					}
					//total rank
					else
					{
						$this->previousRanking[$division] = $ranking->getRanking($this->from,
							$this->_getPreviousRoundId($this->to), $division);
					}
					$this->_sortRanking($this->previousRanking[$division]);
				}
			}
		}
		$this->current_round = $this->round;
	}
	
	/**
	 * get id of previous round accroding to roundcode
	 * 
	 * @param int $round_id
	 * @return int
	 */
	function _getPreviousRoundId($round_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query
			->select($db->quoteName('id'))
			->from($db->quoteName('#__joomleague_round'))
			->where($db->quoteName('project_id') . ' = ' . (int)$this->projectid)
			->order($db->quoteName('roundcode') . ' ASC');

		$db->setQuery($query);
		$res = $db->loadColumn();
		
		if (!$res)
		{
			return $round_id;
		}
		
		$index = array_search($round_id, $res);
		if ($index && $index > 0)
		{
			return $res[$index - 1];
		}
		// if not found, return same round
		return $round_id;
	}

	/**************************************
	 * Compare functions for ordering     *
	 **************************************/

	function _sortRanking(&$ranking)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$order = $input->get('order', '');
		$order_dir = $input->get('dir', 'ASC');

		switch ($order)
		{
			case 'played':
				uasort($ranking, array('JoomleagueModelRanking','playedCmp'));
				break;
			case 'name':
				uasort($ranking, array('JoomleagueModelRanking','teamNameCmp'));
				break;
			case 'rank':
				break;
			case 'won':
				uasort($ranking, array('JoomleagueModelRanking', 'wonCmp'));
				break;
			case 'draw':
				uasort($ranking, array('JoomleagueModelRanking', 'drawCmp'));
				break;
			case 'loss':
				uasort($ranking, array('JoomleagueModelRanking', 'lossCmp'));
				break;
			case 'wot':
				uasort($ranking, array('JoomleagueModelRanking', 'wotCmp'));
				break;
			case 'wso':
				uasort($ranking, array('JoomleagueModelRanking', 'wsoCmp'));
				break;
			case 'lot':
				uasort($ranking, array('JoomleagueModelRanking', 'lotCmp'));
				break;
			case 'lso':
				uasort($ranking, array('JoomleagueModelRanking', 'lsoCmp'));
				break;
			case 'winpct':
				uasort($ranking, array('JoomleagueModelRanking', 'winpctCmp'));
				break;
			case 'quot':
				uasort($ranking, array('JoomleagueModelRanking', 'quotCmp'));
				break;
			case 'goalsp':
				uasort($ranking, array('JoomleagueModelRanking', 'goalspCmp'));
				break;
			case 'goalsfor':
				uasort($ranking, array('JoomleagueModelRanking', 'goalsforCmp'));
				break;
			case 'goalsagainst':
				uasort($ranking, array('JoomleagueModelRanking', 'goalsagainstCmp'));
				break;
			case 'legsdiff':
				uasort($ranking, array('JoomleagueModelRanking', 'legsdiffCmp'));
				break;
			case 'legsratio':
				uasort($ranking, array('JoomleagueModelRanking', 'legsratioCmp'));
				break;
			case 'diff':
				uasort($ranking, array('JoomleagueModelRanking', 'diffCmp'));
				break;
			case 'points':
				uasort($ranking, array('JoomleagueModelRanking', 'pointsCmp'));
				break;
			case 'start':
				uasort($ranking, array('JoomleagueModelRanking', 'startCmp'));
				break;
			case 'bonus':
				uasort($ranking, array('JoomleagueModelRanking', 'bonusCmp'));
				break;
			case 'negpoints':
				uasort($ranking, array('JoomleagueModelRanking', 'negpointsCmp'));
				break;
			case 'pointsratio':
				uasort($ranking, array('JoomleagueModelRanking', 'pointsratioCmp'));
				break;

			default:
				if (method_exists($this, $order . 'Cmp'))
				{
					uasort($ranking, array($this, $order . 'Cmp'));
				}
				break;
		}
		if ($order_dir == 'DESC')
		{
			$ranking = array_reverse($ranking, true);
		}
		return true;
	}

	function playedCmp($a, $b)
	{
		return $a->cnt_matches - $b->cnt_matches;
	}
	
	function teamNameCmp($a, $b)
	{
		return strcasecmp($a->_name, $b->_name);
	}

	function wonCmp($a, $b)
	{
		return $a->cnt_won - $b->cnt_won;
	}

	function drawCmp($a, $b)
	{
		return $a->cnt_draw - $b->cnt_draw;
	}

	function lossCmp($a, $b)
	{
		return $a->cnt_lost - $b->cnt_lost;
	}

	function wotCmp($a, $b)
	{
		return $a->cnt_wot - $b->cnt_wot;
	}

	function wsoCmp($a, $b)
	{
		return $a->cnt_wso - $b->cnt_wso;
	}
	
	function lotCmp($a, $b)
	{
		return $a->cnt_lot - $b->cnt_lot;
	}
	
	function lsoCmp($a, $b)
	{
		return $a->cnt_lso - $b->cnt_lso;
	}
	
	function winpctCmp($a, $b)
	{
		$pct_a = $a->cnt_won / ($a->cnt_won + $a->cnt_lost + $a->cnt_draw);
		$pct_b = $b->cnt_won / ($b->cnt_won + $b->cnt_lost + $b->cnt_draw);
		return $pct_a < $pct_b;
	}

	function quotCmp($a, $b)
	{
		$pct_a = $a->cnt_won / ($a->cnt_won + $a->cnt_lost + $a->cnt_draw);
		$pct_b = $b->cnt_won / ($b->cnt_won + $b->cnt_lost + $b->cnt_draw);
		return $pct_a < $pct_b;
	}

	function goalspCmp($a, $b)
	{
		return $a->sum_team1_result - $b->sum_team1_result;
	}

	function goalsforCmp($a, $b)
	{
		return $a->sum_team1_result - $b->sum_team1_result;
	}

	function goalsagainstCmp($a, $b)
	{
		return $a->sum_team2_result - $b->sum_team2_result;
	}
	
	function legsdiffCmp($a, $b)
	{
		return $a->diff_team_legs - $b->diff_team_legs;
	}

	function legsratioCmp($a, $b)
	{
		return $a->legsRatio - $b->legsRatio;
	}
	
	function diffCmp($a, $b)
	{
		return $a->diff_team_results - $b->diff_team_results;
	}

	function pointsCmp($a, $b)
	{
		return $a->getPoints() - $b->getPoints();
	}

	function startCmp($a, $b)
	{
		return $a->team->start_points * $b->team->start_points;
	}
	
	function bonusCmp($a, $b)
	{
		return $a->bonus_points - $b->bonus_points;
	}

	function negpointsCmp($a, $b)
	{
		return $a->neg_points - $b->neg_points;
	}

	function pointsratioCmp($a, $b)
	{
		return $a->pointsRatio - $b->pointsRatio;
	}
}
