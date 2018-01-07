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

/**
 * Treetomatches Model
 */
class JoomleagueModelTreetomatches extends JLGModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array();
		}

		parent::__construct($config);
	}

	protected function populateState($ordering = null,$direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
	}

	protected function getStoreId($id = '')
	{
		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$user = Factory::getUser();
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		// Treenode
		$nid = $input->get('nid',array(),'array');
		ArrayHelper::toInteger($nid);
		if($nid)
		{
			$node_id = $nid[0];
			$app->setUserState('com_joomleaguenode_id',$node_id);
		}
		else
		{
			$node_id = $app->getUserState($option . 'node_id');
		}

		// Select the required fields from the table.
		$query->select(
				$this->getState('list.select',
						'a.id AS mid,a.match_number AS match_number,
				a.team1_result AS projectteam1result,a.team2_result AS projectteam2result,
				a.round_id AS rid,a.published AS published,a.checked_out'));
		$query->from('#__joomleague_match AS a');

		// join project-team table (projectteam1 )
		$query->join('LEFT','#__joomleague_project_team AS pt1 ON pt1.id = a.projectteam1_id');
		// join project-team table (projectteam2)
		$query->join('LEFT','#__joomleague_project_team AS pt2 ON pt2.id = a.projectteam2_id');
		// join team table (projectteam1)
		$query->select('t1.name AS projectteam1');
		$query->join('LEFT','#__joomleague_team AS t1 ON t1.id = pt1.team_id');
		// join team table (projectteam2)
		$query->select('t2.name AS projectteam2');
		$query->join('LEFT','#__joomleague_team AS t2 ON t2.id = pt2.team_id');
		// join round table
		$query->select('r.roundcode AS roundcode');
		$query->join('LEFT','#__joomleague_round AS r ON r.id = a.round_id');
		// join tree_to_match table
		$query->select('ttm.node_id AS node_id');
		$query->join('LEFT','#__joomleague_treeto_match AS ttm ON ttm.match_id = a.id');

		$query->where('ttm.node_id = ' . $node_id);
		$query->order('r.roundcode');

		return $query;
	}

	function store($data)
	{
		$result = true;
		$peid = $data['node_matcheslist'];
		ArrayHelper::toInteger($peid);

		$db = Factory::getDbo();

		if($peid == null)
		{
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_treeto_match');
			$query->where('node_id = '.$data['id']);
		}
		else
		{
			$peids = implode(',',$peid);
			$query = $db->getQuery(true);
			$query->delete('#__joomleague_treeto_match');
			$query->where(array('node_id = '.$data['id'],'match_id NOT IN  ('.$peids.')'));
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
	
		for($x = 0;$x < count($data['node_matcheslist']);$x ++)
		{
			$query = "	INSERT IGNORE
						INTO #__joomleague_treeto_match
						(node_id, match_id)
						VALUES ( '".$data['id']."', '".$data['node_matcheslist'][$x]."')";
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


	/**
	 *
	 */
	function getMatches()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$input = $app->input;
		$option = $input->getCmd('option');
		$node_id = $app->getUserState($option.'node_id');
		$treeto_id = $app->getUserState($option.'treeto_id');
		$project_id = $app->getUserState($option.'project');

		$query = ' SELECT mc.id AS value ';
		$query .= ' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
		$query .= ' ,mc.id AS info ';
		$query .= ' FROM #__joomleague_match AS mc ';
		$query .= ' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .= ' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .= ' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .= ' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .= ' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .= ' WHERE  r.project_id = ' . $project_id;
		$query .= ' AND NOT mc.projectteam1_id IN ';
		$query .= ' ( ';
		$query .= ' SELECT ttn.team_id ';
		$query .= ' FROM #__joomleague_treeto_node AS ttn';
		$query .= ' LEFT JOIN #__joomleague_treeto_node AS ttn2 ';
		$query .= ' ON (ttn.node = 2*ttn2.node OR ttn.node = 2*ttn2.node + 1) ';
		$query .= ' WHERE  ttn2.id = ' . $node_id;
		$query .= ' AND  ttn.treeto_id = ' . $treeto_id;
		$query .= ' ) ';
		$query .= ' AND NOT mc.projectteam2_id IN ';
		$query .= ' ( ';
		$query .= ' SELECT ttn.team_id ';
		$query .= ' FROM #__joomleague_treeto_node AS ttn';
		$query .= ' LEFT JOIN #__joomleague_treeto_node AS ttn2 ';
		$query .= ' ON (ttn.node = 2*ttn2.node OR ttn.node = 2*ttn2.node + 1) ';
		$query .= ' WHERE  ttn2.id = ' . $node_id;
		$query .= ' AND  ttn.treeto_id = ' . $treeto_id;
		$query .= ' ) ';
		$query .= ' ORDER BY r.id ';
		$query .= ';';
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

	/**
	 *
	 */
	function getNodeMatches($node_id = 0)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$input = $app->input;
		$node_id = $app->getUserState('com_joomleaguenode_id');

		$query = ' SELECT mc.id AS value ';
		$query .= ' ,CONCAT(t1.name, \'_vs_\', t2.name, \' [round:\',r.roundcode,\']\') AS text ';
		$query .= ' ,mc.id AS notes ';
		$query .= ' ,mc.id AS info ';
		$query .= ' FROM #__joomleague_match AS mc ';
		$query .= ' LEFT JOIN #__joomleague_project_team AS pt1 ON pt1.id = mc.projectteam1_id ';
		$query .= ' LEFT JOIN #__joomleague_project_team AS pt2 ON pt2.id = mc.projectteam2_id ';
		$query .= ' LEFT JOIN #__joomleague_team AS t1 ON t1.id = pt1.team_id ';
		$query .= ' LEFT JOIN #__joomleague_team AS t2 ON t2.id = pt2.team_id ';
		$query .= ' LEFT JOIN #__joomleague_round AS r ON r.id = mc.round_id ';
		$query .= ' LEFT JOIN #__joomleague_treeto_match AS ttm ON mc.id = ttm.match_id ';
		$query .= ' WHERE  ttm.node_id = ' . $node_id;
		$query .= ' ORDER BY mc.id ';
		$query .= ';';
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
