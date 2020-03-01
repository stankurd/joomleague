<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

JLoader::register('JLGRanking', JLG_PATH_SITE.'/helpers/ranking.php');
JLoader::register('JoomleagueModelProject', JLG_PATH_SITE.'/models/project.php');

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

	function __construct( )
	{
		parent::__construct( );
		$this->projectid = Factory::getApplication()->input->getInt( "p", 0 );
		$this->round = Factory::getApplication()->input->getInt( "r", $this->getCurrentRound());
		$this->part  = Factory::getApplication()->input->getInt( "part", 0);
		$this->from  = Factory::getApplication()->input->getInt( 'from', $this->round );
		$this->to	 = Factory::getApplication()->input->getInt( 'to', $this->round);
		$this->type  = Factory::getApplication()->input->getInt( 'type', 0 );
		$this->last  = Factory::getApplication()->input->getInt( 'last', 0 );

		$this->selDivision = Factory::getApplication()->input->getInt( 'division', 0 );
	}

	
	/**
	 * get previous games for each team
	 * 
	 * @return array games array indexed by project team ids
	 */
	public function getPreviousGames()
	{
		if (!$this->round) {
			return false;
		}
		
		// current round roundcode
		$rounds = $this->getRounds();
		$current = null;
		foreach ($rounds as $r)
		{
			if ($r->id == $this->round) {
			$current = $r;
				break;
			}
		}
			if (!$current) {
			return false;
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// previous games of each team, until current round
		$query
		      ->select('m.*')
		      ->select('r.roundcode')
		      ->select('CASE WHEN CHAR_LENGTH(' . $db->quoteName('t1.alias') . ')' .
		          ' AND CHAR_LENGTH(' . $db->quoteName('t2.alias') . ')' .
		          ' THEN CONCAT_WS(\':\',' . $db->quoteName('m.id') . ', CONCAT_WS("_",' . $db->quoteName('t1.alias') .
		          ', ' . $db->quoteName('t2.alias') . '))' .
		          ' ELSE ' . $db->quoteName('m.id') .  ' END AS slug')
		      ->select($this->constructSlug($db, 'project_slug', 'p.alias', 'p.id'))
		      ->from('#__joomleague_match AS m')
		      ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id')
		      ->innerJoin('#__joomleague_project AS p ON p.id = r.project_id')
		      ->innerJoin('#__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id')
		      ->innerJoin('#__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id')
		      ->innerJoin('#__joomleague_team AS t1 ON pt1.team_id = t1.id')
		      ->innerJoin('#__joomleague_team AS t2 ON pt2.team_id = t2.id')
		      ->where('r.project_id = ' . $db->Quote($this->projectid))
		      ->where('r.roundcode <= ' . $db->Quote($current->roundcode))
		      ->where('m.team1_result IS NOT NULL');
		      if($this->selDivision>0) {
		          $query->where('(pt1.division_id = ' . $db->Quote($this->selDivision). ' OR pt2.division_id = ' . $db->Quote($this->selDivision) . ')');
		      }
		      $query->order('r.roundcode ASC');
		          ;
		$db->setQuery($query);
		$games = $db->loadObjectList();

		$teams = $this->getTeamsIndexedByPtid();

		// get games per team
		$res = array();
		foreach ($teams as $ptid =>$team)
		{
			$teamgames = array();
			foreach ((array) $games as $g)
			{
				if ($g->projectteam1_id == $team->projectteamid || $g->projectteam2_id == $team->projectteamid) {
					$teamgames[] = $g;
				}
			}

			if (!count($teamgames)) {
				$res[$ptid] = array();
				continue;
			}
				
			// get last x games
			//$nb_games = 5;
			$config = $this->getTemplateConfig('ranking');
			$nb_games = $config['nb_previous'];			
			$res[$ptid] = array_slice($teamgames, -$nb_games);
		}
		return $res;
	}
		
	/**
	 * computes the ranking
	 *
	 */
	public function computeRanking()
	{
		$app		= Factory::getApplication();
		$project	= $this->getProject();
		
		$mdlRound	= BaseDatabaseModel::getInstance("Round", "JoomleagueModel");
		$mdlRounds	= BaseDatabaseModel::getInstance("Rounds", "JoomleagueModel");
		$mdlRounds->setProjectId($project->id);
		
		$firstRound	= $mdlRounds->getFirstRound($project->id);
		$lastRound	= $mdlRounds->getLastRound($project->id);

		// url if no sef link comes along (ranking form)
		$url = JoomleagueHelperRoute::getRankingRoute( $this->projectid );
		$tableconfig= $this->getTemplateConfig( "ranking" );

		$this->round = ($this->round == 0) ? $this->getCurrentRound() : $this->round;

		$this->rounds = $this->getRounds();
		if ( $this->part == 1 )
		{
			$this->from = $firstRound['id'];
			$this->to = $this->rounds[intval(count($this->rounds)/2)]->id;
		}
		elseif ( $this->part == 2 )
		{
			$this->from = $this->rounds[intval(count($this->rounds)/2)+1]->id;
			$this->to = $lastRound['id'];
		}
		else
		{
			if (Factory::getApplication()->input->getInt( 'from' ) == "0") {
				$this->from = $firstRound['id'];
			}
			else
			{
				$this->from = Factory::getApplication()->input->getInt( 'from', $firstRound['id'] );
			}
			if (Factory::getApplication()->input->getInt( 'to' ) == "0") {
				$this->to   = $this->round;
			}
			else
			{
				$this->to   = Factory::getApplication()->input->getInt( 'to', $this->round, $lastRound['id'] );
			}
		}
		if( $this->part > 0 )
		{
			$url.='&amp;part='.$this->part;
		}
		elseif ( $this->from != 1 || $this->to != $this->round )
		{
			$url.='&amp;from='.$this->from.'&amp;to='.$this->to;
		}
		$this->type = Factory::getApplication()->input->getInt( 'type', 0 );
		if ( $this->type > 0 )
		{
			$url.='&amp;type='.$this->type;
		}

		$this->divLevel = 0;

		//for sub division ranking tables
		if ( $project->project_type=='DIVISIONS_LEAGUE' )
		{
			$selDivision = Factory::getApplication()->input->getInt( 'division', 0 );
			$this->divLevel = Factory::getApplication()->input->getInt( 'divLevel', $tableconfig['default_division_view'] );

			if ( $selDivision > 0 )
			{
				$url .= '&amp;division='.$selDivision;
				$divisions = array( $selDivision );
			}
			else
			{
				// check if division level view is allowed. if not, replace with default
				if ( ( $this->divLevel == 0 && $tableconfig['show_project_table']==0 ) ||
					 ( $this->divLevel == 1 && $tableconfig['show_level1_table']== 0 ) ||
					 ( $this->divLevel == 2 && $tableconfig['show_level2_table']==0  ) )
				{
					$this->divLevel = $tableconfig['default_division_view'];
				}
				$url .= '&amp;divLevel='.$this->divLevel;
				if ( $this->divLevel )
				{
					$divisions = $this->getDivisionsId( $this->divLevel );
				}
				else
				{
					$divisions = array(0);
				}
			}
		}
		else
		{
			$divisions = array(0); //project
		}
		$selectedvalue = 0;

		$last = Factory::getApplication()->input->getInt( 'last', 0 );
		if ($last > 0)
		{
			$url .= '&amp;last='.$last;
		}
		if ( Factory::getApplication()->input->getInt( 'sef', 0) == 1 )
		{
			$app->redirect( Route::_( $url ) );
		}

		/**
		* create ranking object	
		*
		*/
		$ranking = JLGRanking::getInstance($project);
		$ranking->setProjectId( $this->projectid );
		
		foreach ( $divisions as $division )
		{

			//away rank
			if ($this->type == 2) {
				$this->currentRanking[$division] = $ranking->getRankingAway($this->from, $this->to, $division);
			}
			//home rank
			else if ($this->type == 1) {
				$this->currentRanking[$division] = $ranking->getRankingHome($this->from, $this->to, $division);
			}
			//total rank
			else {
				$this->currentRanking[$division]	= $ranking->getRanking($this->from, $this->to, $division);
				$this->homeRank[$division]			= $ranking->getRankingHome($this->from, $this->to, $division);
				$this->awayRank[$division]			= $ranking->getRankingAway($this->from, $this->to, $division);
			}
			$this->_sortRanking($this->currentRanking[$division]);

			
			//previous rank
			if( $tableconfig['last_ranking']==1 )
			{
				if ( $this->to == 1 || ( $this->to == $this->from ) )
				{
					$this->previousRanking[$division] = &$this->currentRanking[$division];
				}
				else
				{	
					//away rank
					if ($this->type == 2) {
						$this->previousRanking[$division] = $ranking->getRankingAway($this->from, $this->_getPreviousRoundId($this->to), $division);
					}
					//home rank
					else if ($this->type == 1) {
						$this->previousRanking[$division] = $ranking->getRankingHome($this->from, $this->_getPreviousRoundId($this->to), $division);
					}
					//total rank
					else {
						$this->previousRanking[$division] = $ranking->getRanking($this->from, $this->_getPreviousRoundId($this->to), $division);
					}
					$this->_sortRanking($this->previousRanking[$division]);
				}
			}
		}
		$this->current_round = $this->round;
		return ;
	}
	
	/**
	 * get id of previous round accroding to roundcode
	 * 
	 * @param int $round_id
	 * @return int
	 */
	public function _getPreviousRoundId($round_id)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query
	           ->select('id')
	           ->from('#__joomleague_round')
	           ->where('project_id = ' . $this->projectid)
	           ->order('roundcode ASC');
		$db->setQuery($query);
		$res = $db->loadColumn();
		
		if (!$res) {
			return $round_id;
		}
		
		$index = array_search($round_id, $res);
		if ($index && $index > 0) {
			return $res[$index - 1];
		}
		// if not found, return same round
		return $round_id;
	}

	/**************************************
	 * Compare functions for ordering     *
	 **************************************/

	public function _sortRanking(&$ranking)
	{
		$order     = Factory::getApplication()->input->get( 'order', '' );
		$order_dir = Factory::getApplication()->input->get( 'dir', 'ASC' );

		switch ($order)
		{
			case 'played':
			uasort( $ranking, array("JoomleagueModelRanking","playedCmp" ));
			break;				
			case 'name':
			uasort( $ranking, array("JoomleagueModelRanking","teamNameCmp" ));
			break;
			case 'rank':
			break;
			case 'won':
			uasort( $ranking, array("JoomleagueModelRanking","wonCmp" ));
			break;
			case 'draw':
			uasort( $ranking, array("JoomleagueModelRanking","drawCmp" ));
			break;
			case 'loss':
			uasort( $ranking, array("JoomleagueModelRanking","lossCmp" ));
			break;
			case 'wot':
			uasort( $ranking, array("JoomleagueModelRanking","wotCmp" ));
			break;		
			case 'wso':
			uasort( $ranking, array("JoomleagueModelRanking","wsoCmp" ));
			break;	
			case 'lot':
			uasort( $ranking, array("JoomleagueModelRanking","lotCmp" ));
			break;		
			case 'lso':
			uasort( $ranking, array("JoomleagueModelRanking","lsoCmp" ));
			break;			
			case 'winpct':
			uasort( $ranking, array("JoomleagueModelRanking","winpctCmp" ));
			break;
			case 'quot':
			uasort( $ranking, array("JoomleagueModelRanking","quotCmp" ));
			break;
			case 'goalsp':
			uasort( $ranking, array("JoomleagueModelRanking","goalspCmp" ));
			break;
			case 'goalsfor':
			uasort( $ranking, array("JoomleagueModelRanking","goalsforCmp" ));
			break;
			case 'goalsagainst':
			uasort( $ranking, array("JoomleagueModelRanking","goalsagainstCmp" ));
			break;
			case 'legsdiff':
			uasort( $ranking, array("JoomleagueModelRanking","legsdiffCmp" ));
			break;
			case 'legsratio':
			uasort( $ranking, array("JoomleagueModelRanking","legsratioCmp" ));
			break;				
			case 'diff':
			uasort( $ranking, array("JoomleagueModelRanking","diffCmp" ));
			break;
			case 'points':
			uasort( $ranking, array("JoomleagueModelRanking","pointsCmp" ));
			break;
			case 'start':
			uasort( $ranking, array("JoomleagueModelRanking","startCmp" ));
			break;
			case 'bonus':
			uasort( $ranking, array("JoomleagueModelRanking","bonusCmp" ));
			break;
			case 'negpoints':
			uasort( $ranking, array("JoomleagueModelRanking","negpointsCmp" ));
			break;
			case 'pointsratio':
			uasort( $ranking, array("JoomleagueModelRanking","pointsratioCmp" ));
			break;			

			default:
				if (method_exists($this, $order.'Cmp')) {
					uasort( $ranking, array($this, $order.'Cmp'));
				}
				break;
		}
		if ($order_dir == 'DESC')
		{
			$ranking = array_reverse( $ranking, true );
		}
		return true;
	}

	public function playedCmp( &$a, &$b){
	  $res = $a->cnt_matches - $b->cnt_matches;
	  return $res;
	}	
	
	public function teamNameCmp( &$a, &$b){
	  return strcasecmp ($a->_name, $b->_name);
	}

	public function wonCmp( &$a, &$b){
	  $res = $a->cnt_won - $b->cnt_won;
	  return $res;
	}

	public function drawCmp( &$a, &$b){
	  $res = ($a->cnt_draw - $b->cnt_draw);
	  return $res;
	}

	public function lossCmp( &$a, &$b){
	  $res = ($a->cnt_lost - $b->cnt_lost);
	  return $res;
	}

	public function wotCmp( &$a, &$b){
	  $res = $a->cnt_wot - $b->cnt_wot;
	  return $res;
	}

	public function wsoCmp( &$a, &$b){
	  $res = $a->cnt_wso - $b->cnt_wso;
	  return $res;
	}		
	
	public function lotCmp( &$a, &$b){
	  $res = $a->cnt_lot - $b->cnt_lot;
	  return $res;
	}
	
	public function lsoCmp( &$a, &$b){
	  $res = $a->cnt_lso - $b->cnt_lso;
	  return $res;
	}	
	
	public function winpctCmp( &$a, &$b){
	  $pct_a = $a->cnt_won/($a->cnt_won+$a->cnt_lost+$a->cnt_draw);
	  $pct_b = $b->cnt_won/($b->cnt_won+$b->cnt_lost+$b->cnt_draw);
	  $res =($pct_a < $pct_b);
	  return $res;
	}

	public function quotCmp( &$a, &$b){
	  $pct_a = $a->cnt_won/($a->cnt_won+$a->cnt_lost+$a->cnt_draw);
	  $pct_b = $b->cnt_won/($b->cnt_won+$b->cnt_lost+$b->cnt_draw);
	  $res =($pct_a < $pct_b);
	  return $res;
	}

	public function goalspCmp( &$a, &$b){
	  $res = ($a->sum_team1_result - $b->sum_team1_result);
	  return $res;
	}

	public function goalsforCmp( &$a, &$b){
	  $res = ($a->sum_team1_result - $b->sum_team1_result);
	  return $res;
	}

	public function goalsagainstCmp( &$a, &$b){
	  $res = ($a->sum_team2_result - $b->sum_team2_result);
	  return $res;
	}	
	
	public function legsdiffCmp( &$a, &$b){
	  $res = ($a->diff_team_legs - $b->diff_team_legs);
	  return $res;
	}

	public function legsratioCmp( &$a, &$b){
	  $res = ($a->legsRatio - $b->legsRatio);
	  return $res;
	}
	
	public function diffCmp( &$a, &$b){
	  $res = ($a->diff_team_results - $b->diff_team_results);
	  return $res;
	}

	public function pointsCmp( &$a, &$b){
	  $res = ($a->getPoints() - $b->getPoints());
	  return $res;
	}
		
	public function startCmp( &$a, &$b){
	  $res = ($a->team->start_points * $b->team->start_points);
	  return $res;
	}
	
	public function bonusCmp( &$a, &$b){
	  $res = ($a->bonus_points - $b->bonus_points);
	  return $res;
	}

	public function negpointsCmp( &$a, &$b){
	  $res = ($a->neg_points - $b->neg_points);
	  return $res;
	}	

	public function pointsratioCmp( &$a, &$b){
	  $res = ($a->pointsRatio - $b->pointsRatio);
	  return $res;
	}	
	
}
?>