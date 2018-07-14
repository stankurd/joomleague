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
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/models/project.php';

/**
 * Model-Treetonode
 */
class JoomleagueModelTreetonode extends JoomleagueModelProject
{
	var $projectid=0;
	var $treetoid=0;

	function __construct( )
	{
		parent::__construct( );
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->projectid=$input->getInt('p',0);
		$this->treetoid=$input->getInt('tnid',0);
	}

	function getTreetonode()
	{
		if (!$this->projectid) {
			$this->setError(Text::_('Missing project id'));
			return false;
		}
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = 'SELECT ttn.* ';
		$query .=	' ,ttn.id AS ttnid';
		$query .=	' ,c.country AS country';
		$query .=	' ,c.logo_small AS logo_small';
		$query .=	' ,t.name AS team_name ';
		$query .=	' ,t.middle_name AS middle_name ';
		$query .=	' ,t.short_name AS short_name ';
		$query .=	' ,t.id AS tid ';
		$query .=	' ,ttn.title AS title ';
		$query .=	' ,ttn.content AS content ';
		$query .=	' ,tt.tree_i AS tree_i ';
		$query .=	' ,tt.hide AS hide ';
		$query .=	' FROM #__joomleague_treeto_node AS ttn ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt ON pt.id = ttn.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t ON t.id = pt.team_id ';
		$query .=	' LEFT JOIN #__joomleague_club AS c ON c.id = t.club_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto AS tt ON tt.id = ttn.treeto_id ';
		$query .=	' WHERE ttn.treeto_id = ' .  $db->Quote($this->treetoid) ;
		$query .=	' ORDER BY ttn.row ';
		$query .=	';';
		$db->setQuery( $query );
		$this->treetonode = $db->loadObjectList();
		
		return $this->treetonode;
	}
	
	function getNodeMatches($ttnid=0)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = ' SELECT mc.id AS value ';
		$query .=	' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
	//	$query .=	' ,mc.id AS notes ';
	//	$query .=	' ,mc.id AS info ';
		$query .=	' FROM #__joomleague_match AS mc ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .=	' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .=	' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .=	' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .=	' LEFT JOIN #__joomleague_treeto_match AS ttm ON mc.id = ttm.match_id ';
		$query .=	' WHERE  ttm.node_id = ' . (int) $ttnid ;
		$query .=	' ORDER BY mc.id ';
		$query .=	';';
		$db->setQuery($query);
	//	if ( !$result = $db->loadObjectList() )
	//	{
	//		$this->setError( $db->getErrorMsg() );
	//		return false;
	//	}
	//	else
	//	{
			//return $result;
			return $db->loadObjectList();
	//	}
	}
	
	function showNodeMatches(&$nodes)
	{
		//TODO
		$matches=$this->model->getNodeMatches($nodes);
		$lineinover='';
		foreach ($matches as $mat)
		{
			$lineinover .= $mat->text.'<br/>';
		}
		echo $lineinover;
	}
	
	function getRoundName()
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$query = 'SELECT * '
			. ' FROM #__joomleague_round AS r '
			. ' WHERE r.project_id = ' .  $db->Quote($this->projectid)
			. ' ORDER BY r.round_date_first, r.ordering '
			;
		$db->setQuery( $query );
		$this->roundname = $db->loadObjectList();

		return $this->roundname;
	}
}
