<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;


/**
 * Treetonodes Model
 */
class JoomleagueModelTreetonodes extends JLGModelList
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		$limit = 130;
		$this->setState('limit',$limit);
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

		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = Factory::getUser();
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option . 'project');
		$treeto_id = $app->getUserState($option . 'treeto_id');

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_treeto_node AS a');

		// join Project-team table
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.id = a.team_id');
		// join Team table
		$query->select('t.name AS team_name');
		$query->join('LEFT','#__joomleague_team AS t ON t.id = pt.team_id');
		// join treeto table
		$query->select('tt.tree_i AS tree_i');
		$query->join('LEFT','#__joomleague_treeto AS tt ON tt.id = a.treeto_id');
		// join treeto match table
		$query->select('COUNT(ttm.id) AS countmatch');
		$query->join('LEFT','#__joomleague_treeto_match AS ttm ON ttm.node_id = a.id');

		$query->where('a.treeto_id = ' . $treeto_id);

		$query->order('a.row');
		$query->group('a.id');

		return $query;
	}

	/**
	 *getMaxRound
	 */
	function getMaxRound($project_id)
	{
		$result = 0;
		if($project_id > 0)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(roundcode)');
			$query->from('#__joomleague_round');
			$query->where('project_id = ' . $project_id);
			$db->setQuery($query);
			$result = $db->loadResult();
		}
		return $result;
	}

	/**
	 * setRemoveNode
	 */
	function setRemoveNode($post)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		// $treeto_id = $app->getUserState($option . 'treeto_id');
		$treeto_id = $post['treeto_id'];
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = ' DELETE ttn, ttm ';
		$query .= ' FROM #__joomleague_treeto_node AS ttn ';
		$query .= ' LEFT JOIN #__joomleague_treeto_match AS ttm ON ttm.node_id=ttn.id ';
		$query .= ' WHERE ttn.treeto_id = ' . $treeto_id;
		$query .= ';';
		$db->setQuery($query);
		$db->execute($query);
		$query = ' UPDATE #__joomleague_treeto AS tt ';
		$query .= ' SET ';
		$query .= ' tt.tree_i = 0 ';
		$query .= ' ,tt.global_bestof = 1 ';
		$query .= ' ,tt.global_matchday = 0 ';
		$query .= ' ,tt.global_known = 0 ';
		$query .= ' ,tt.global_fake = 0 ';
		$query .= ' ,tt.mirror = 0 ';
		$query .= ' ,tt.hide = 0 ';
		$query .= ' ,tt.leafed = 0 ';
		$query .= ' WHERE tt.id = ' . $treeto_id;
		$query .= ';';
		$db->setQuery($query);
		$db->execute($query);
		return true;
	}

	/**
	 * UPDATE selected node as a leaf AND unpublish ALL children node
	 */
	function storeshortleaf($cid,$post)
	{
		$result = true;

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');

		$tree_i = $post['tree_i'];
		$treeto_id = $post['treeto_id'];
		$global_fake = $post['global_fake'];

		$db = Factory::getDbo();

		// if user checked at least ONE node as leaf
		for($x = 0;$x < count($cid);$x ++)
		{
			// leaf(ing) this node
			$query = $db->getQuery(true);
			$query->update('#__joomleague_treeto_node');
			$query->set('is_leaf = 1');
			$query->where('id=' . $cid[$x]);
			$db->setQuery($query);
			$db->execute();
			// find index of checked node

			$db->getQuery(true);
			$query->select('node');
			$query->from('#__joomleague_treeto_node');
			$query->where('id=' . $cid[$x]);
			$db->setQuery($query);
			$db->execute();
			$resultleafnode = $db->loadResult();
			// unpublish children node
			if($resultleafnode < (pow(2,$tree_i)))
			{
				for($y = 1;$y <= ($tree_i - 1);$y ++)
				{
					$childleft = (pow(2,$y)) * $resultleafnode;
					$childright = ((pow(2,$y)) * ($resultleafnode + 1)) - 1;
					for($z = $childleft;$z <= $childright;$z ++)
					{
						if($z < pow(2,$tree_i + 1))
						{
							$query = $db->getQuery(true);
							$query->update('#__joomleague_treeto_node');
							$query->set('published=0');
							$query->where('(node= ' . $z . ' AND treeto_id = ' . $treeto_id . ')');
							$db->setQuery($query);
							$db->execute();
						}
					}
				}
			}
		}
		// 2, 4, 8, 16, 32, 64 teams, default leaf(ing)
		for($k = pow(2,$tree_i);$k < pow(2,$tree_i + 1);$k ++)
		{
			$query = $db->getQuery(true);
			$query->update('#__joomleague_treeto_node');
			$query->set('is_leaf=1');
			$query->where('(node= ' . $k . ' AND treeto_id = ' . $treeto_id . ')');
			$db->setQuery($query);
			$db->execute();
		}
		// only for menu
		$query = $db->getQuery(true);
		$query->update('#__joomleague_treeto');
		$query->set('leafed=3');
		$query->where('id = ' . $treeto_id);
		$db->setQuery($query);
		$db->execute();

		return $result;
	}


	/**
	 *
	 */
	function storefinishleaf($post)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$db 	= Factory::getDbo();

		$project_id 	= $app->getUserState($option.'project');
		$tree_i 		= $post['tree_i'];
		$treeto_id 		= $post['treeto_id'];
		$global_known 	= $post['global_known'];
		$global_bestof 	= $post['global_bestof'];

		$query = $db->getQuery(true);
		$query->update('#__joomleague_treeto');
		$query->set('leafed = 1');
		$query->where('id = '.$treeto_id);

		$db->setQuery($query);
		$db->execute($query);

		return true;
	}


	/**
	 *
	 */
	function getProjectTeamsOptions()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option.'project');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = ' SELECT	pt.id AS value, '
				. ' CASE WHEN CHAR_LENGTH(t.name) < 45 THEN t.name ELSE t.middle_name END AS text '
				. ' FROM #__joomleague_team AS t '
				. ' LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id '
				. ' WHERE pt.project_id = '
				. $project_id
				. ' ORDER BY text ASC ';
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
	function storeshort($cid,$post)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$result = true;
		$post = $input->post->getArray();
		$db = Factory::getDbo();

		for($x = 0;$x < count($cid);$x ++)
		{
			$query = $db->getQuery(true);
			$query->update('#__joomleague_treeto_node');
			$query->set('team_id = '.$post['team_id'.$cid[$x]]);
			$query->where('id = '.$cid[$x]);
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
}
