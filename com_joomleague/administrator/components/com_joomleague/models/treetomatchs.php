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
use Joomla\Utilities\ArrayHelper;

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT.'/models/list.php';

/**
 * Treetomatchs Model
 */

class JoomleagueModelTreetomatchs extends JoomleagueModelList
{
	var $_identifier = "treetomatchs";
	function _buildContentOrderBy()
	{
		$orderby = ' ORDER BY r.roundcode ';
	
		return $orderby;
	}
	
	function _buildContentWhere()
	{
	
		$app	= Factory::getApplication();
		$option = $app->input->getCmd('option');
		$node_id = $app->getUserState($option . 'node_id');
		$where = ' WHERE  ttm.node_id = ' . $node_id ;
	
		return $where;
	}

	function _buildQuery()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
		
		$query = ' SELECT mc.id AS mid ';
		$query .=	' ,mc.match_number AS match_number';
		$query .=	' ,t1.name AS projectteam1';
		$query .=	' ,mc.team1_result AS projectteam1result';
		$query .=	' ,mc.team2_result AS projectteam2result';
		$query .=	' ,t2.name AS projectteam2';
		$query .=	' ,mc.round_id AS rid ';
		$query .=	' ,mc.published AS published ';
		$query .=	' ,ttm.node_id AS node_id ';
		$query .=	' ,r.roundcode AS roundcode, mc.checked_out ';
		$query .=	' FROM #__joomleague_match AS mc ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .=	' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto_match AS ttm ON mc.id = ttm.match_id ';
		$query .=	$where ;
		$query .=	$orderby ;
		return $query;
	}


	function store( $data )
	{
		$app	= Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$result = true;
		$peid = $data['node_matcheslist'];
		if ( $peid == null )
		{
			$query = "	DELETE
						FROM #__joomleague_treeto_match
						WHERE node_id = '" . $data['id'] . "'";
		}
		else
		{
			ArrayHelper::toInteger( $peid );
			$peids = implode( ',', $peid );
			$query = "	DELETE
						FROM #__joomleague_treeto_match
						WHERE node_id = '" . $data['id'] . "' AND match_id NOT IN  (" . $peids . ")";
		}
		try {
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		
		for ( $x = 0; $x < count( $data['node_matcheslist'] ); $x++ )
		{
			$query = "	INSERT IGNORE
						INTO #__joomleague_treeto_match
						(node_id, match_id)
						VALUES ( '" . $data['id'] . "', '".$data['node_matcheslist'][$x] . "')";
			try {
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

	//function getMatchToNode()
	function getMatches()
	{
		$app	= Factory::getApplication();
		$option = $app->input->getCmd('option');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$node_id = $app->getUserState($option . 'node_id');
		$treeto_id = $app->getUserState($option . 'treeto_id');
		$project_id = $app->getUserState($option . 'project');
		
		$query = ' SELECT mc.id AS value ';
		$query .=	' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
		$query .=	' ,mc.id AS info ';
		$query .=	' FROM #__joomleague_match AS mc ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .=	' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .=	' WHERE  r.project_id = ' . $project_id ;
		$query .=	' AND NOT mc.projectteam1_id IN ';
		$query .=	' ( ';
		$query .=		' SELECT ttn.team_id ';
		$query .=		' FROM #__joomleague_treeto_node AS ttn' ;
		$query .=		' LEFT JOIN #__joomleague_treeto_node AS ttn2 ';
		$query .=		' ON (ttn.node = 2*ttn2.node OR ttn.node = 2*ttn2.node + 1) ' ;
		$query .=		' WHERE  ttn2.id = ' . $node_id ;
		$query .=		' AND  ttn.treeto_id = ' . $treeto_id ;
		$query .=	' ) ';
		$query .=	' AND NOT mc.projectteam2_id IN ';
		$query .=	' ( ';
		$query .=		' SELECT ttn.team_id ';
		$query .=		' FROM #__joomleague_treeto_node AS ttn' ;
		$query .=		' LEFT JOIN #__joomleague_treeto_node AS ttn2 ';
		$query .=		' ON (ttn.node = 2*ttn2.node OR ttn.node = 2*ttn2.node + 1) ' ;
		$query .=		' WHERE  ttn2.id = ' . $node_id ;
		$query .=		' AND  ttn.treeto_id = ' . $treeto_id ;
		$query .=	' ) ';
		$query .=	' ORDER BY r.id ';
		$query .=	';';
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

	//function getMatchInNode()
	function getNodeMatches($node_id=0)
	{
		$app	= Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);	
		$query = ' SELECT mc.id AS value ';
		$query .=	' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
		$query .=	' ,mc.id AS notes ';
		$query .=	' ,mc.id AS info ';
		$query .=	' FROM #__joomleague_match AS mc ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .=	' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto_match AS ttm ON mc.id = ttm.match_id ';
		$query .=	' WHERE  ttm.node_id = ' . $node_id ;
		$query .=	' ORDER BY mc.id ';
		$query .=	';';
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
}
