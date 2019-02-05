<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
jimport('joomla.application.component.modelitem');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.array');
jimport('joomla.utilities.arrayhelper') ;
jimport( 'joomla.utilities.utility' );
//require_once('project.php');
require_once(JLG_PATH_SITE.DS.'models'.DS.'project.php' );
require_once('predictionusers.php');
require_once('prediction.php');

/**
 * Joomleague Component prediction Ranking Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueModelPredictionRanking extends JoomleagueModelPrediction
{
	static $_roundNames = null;

	function __construct()
	{
		parent::__construct();
	}

	function getMatches($roundID,$project_id)
	{
		if ($roundID==0){$roundID=1;}
		$query = 	"	SELECT	m.id AS mID,
								m.match_date,
								m.team1_result AS homeResult,
								m.team2_result AS awayResult,
								m.team1_result_decision AS homeDecision,
								m.team2_result_decision AS awayDecision,
								t1.name AS homeName,
								t2.name AS awayName,
								c1.logo_small AS homeLogo,
								c2.logo_small AS awayLogo

						FROM #__joomleague_match AS m

						INNER JOIN #__joomleague_round AS r ON	r.id=m.round_id AND
																r.project_id=$project_id AND
																r.id=$roundID
						LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id
						LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id
						LEFT JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id
						LEFT JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id
						LEFT JOIN #__joomleague_club AS c1 ON c1.id=t1.club_id
						LEFT JOIN #__joomleague_club AS c2 ON c2.id=t2.club_id
						WHERE (m.cancel IS NULL OR m.cancel = 0)
						ORDER BY m.match_date, m.id ASC";
		$this->_db->setQuery( $query );
		//echo($this->_db->getQuery( ));
		$results = $this->_db->loadObjectList();
		return $results;
	}

	function createFromMatchdayList($project_id)
	{
		$from_matchday=array();
		$from_matchday[]= HTMLHelper::_('select.option','0',Text::_('JL_RANKING_FROM_MATCHDAY'));
		$from_matchday=array_merge($from_matchday,$this->getRoundNames($project_id));
		return $from_matchday;
	}

	function createToMatchdayList($project_id)
	{
		$to_matchday=array();
		$to_matchday[]=HTMLHelper::_('select.option','0',Text::_('JL_RANKING_TO_MATCHDAY'));
		$to_matchday=array_merge($to_matchday,$this->getRoundNames($project_id));
		return $to_matchday;
	}
	public function getTable($type = 'predictioranking', $prefix = 'table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}
	
}
?>