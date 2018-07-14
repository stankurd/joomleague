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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Matrix
 */
class JoomleagueModelMatrix extends JoomleagueModelProject
{
	function __construct( )
	{
		parent::__construct( );
		$app = Factory::getApplication();
		$this->divisionid	= $app->input->getInt('division', 0);
		$this->roundid		= $app->input->getInt('r', 0);
	}

	function getDivisionID( )
	{
		return $this->divisionid;
	}

	function getRoundID( )
	{
		return $this->roundid;
	}

	function getDivision( )
	{
		$division = null;
		if ( $this->divisionid > 0 )
		{
			$division = $this->getTable("Division", "Table");
			$division->load( $this->divisionid );
		}
		return $division;
	}

	function getRound( )
	{
		$round = null;
		if ( $this->roundid > 0 )
		{
			$round = $this->getTable( "Round", "Table" );
			$round->load( $this->roundid );
		}
		return $round;
	}

	/**
	 * Returns rows of games info for matrix display
	 *
	 * @param Joomleague $project
	 * @param int $division
	 * @param string $unpublished
	 * @return array rows objects
	 */
	function getMatrixResults( $project_id, $unpublished = 0 )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
	    $query_WHERE = $db->getQuery(true);
		$query_WHERE = "";
		$query_END	 = $db->getQuery(true);
		$query_END	 = " ORDER BY r.ordering, r.roundcode";
		$query_SELECT = $db->getQuery(true);
		$query_SELECT = "SELECT DISTINCT(m.id), r.name AS roundname,
												r.id AS roundid,
												r.roundcode,
                                                r.ordering,
												m.show_report,
												m.cancel,
												m.cancel_reason,
												m.projectteam1_id,
												m.projectteam2_id,
												m.team1_result as e1,
												m.team2_result as e2,
												m.match_result_type as rtype,
												m.alt_decision as decision,
												m.team1_result_decision AS v1,
												m.team2_result_decision AS v2,
												m.new_match_id, m.old_match_id,
												tt1.division_id AS division_id";
		$query_FROM	= $db->getQuery(true);
		$query_FROM	= " FROM #__joomleague_match AS m
						INNER JOIN #__joomleague_round AS r ON r.id=m.round_id
						LEFT JOIN #__joomleague_project_team AS tt1 ON m.projectteam1_id = tt1.team_id
						LEFT JOIN #__joomleague_project_team AS tt2 ON m.projectteam2_id = tt2.team_id ";
		if ( $this->divisionid > 0 )
		{
			$query_FROM.= "	LEFT JOIN #__joomleague_division AS d1 ON tt1.division_id = d1.id
							LEFT JOIN #__joomleague_division AS d2 ON tt2.division_id = d2.id";
			if ( $this->divisionid > 0 )
			{
				$query_FROM .= " AND (	d1.id = ".$db->Quote($this->divisionid)." OR d1.parent_id = " . $db->Quote($this->divisionid) . "
									OR d2.id = " . $db->Quote($this->divisionid) . " OR d2.parent_id = " . $db->Quote($this->divisionid) . " )";
			}
		}
		$query_WHERE = " WHERE r.project_id = ".$project_id;
		if ( $this->roundid > 0 )
		{
			$query_WHERE .= " AND m.round_id = " . $db->Quote($this->roundid);
		}
		if ( $unpublished != 1 )
		{
			$query_WHERE .=" AND m.published = 1";
		}
		$query = $query_SELECT . $query_FROM . $query_WHERE . $query_END ;
		$db->setQuery( $query );
		try {
		    $result = $db->loadObjectList();
		} catch (RuntimeException $e) {
		    Factory::getApplication()->enqueueMessage(Text::_($e->getMessage()), 'error');	    
		}	
		return $result;
	}
}
