<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		comraden + other JL Team members
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Treeto Model
 */
class JoomleagueModelTreeto extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.club';


	function delete(&$pks = array())
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		if(count($pks))
		{
			$cids = implode(',',$pks);

			$query = ' DELETE tt, ttn, ttm ';
			$query .= ' FROM #__joomleague_treeto AS tt ';
			$query .= ' LEFT JOIN #__joomleague_treeto_node AS ttn ON ttn.treeto_id=tt.id ';
			$query .= ' LEFT JOIN #__joomleague_treeto_match AS ttm ON ttm.node_id=ttn.id ';
			$query .= ' WHERE tt.id IN (' . $cids . ')';
			try {
			$db->setQuery($query);
			$db->execute();
			}
			catch (Exception $e)
			{
				$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				return false;
			}
			return parent::delete($pks);
		}
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param
	 *        	type The table type to instantiate
	 * @param
	 *        	string A prefix for the table class name. Optional.
	 * @param
	 *        	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Treeto',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param integer $pk
	 *        	The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItemDisabled($pk = null)
	{
		if($item = parent::getItem($pk))
		{
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param array $data		the form.
	 * @param boolean $loadData	the form is to load its own data (default case), false if not.
	 * @return mixed JForm object on success, false on failure
	 */
	public function getForm($data = array(),$loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.treeto','treeto',array(
				'control' => 'jform',
				'load_data' => $loadData
		));
		if(empty($form))
		{
			return false;
		}

		/*
		 * $input = Factory::getApplication()->input;
		 *
		 * if ($this->getState('treeto.id'))
		 * {
		 * $pk = $this->getState('treeto.id');
		 * $item = $this->getItem($pk);
		 * }
		 */

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.treeto.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param array $data	The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName() . '.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}

	// ADDITIONAL FUNCTIONS //
	function _loadDataOBS()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Lets load the content if it doesn't already exist
		if(empty($this->_data))
		{
			$query = '	SELECT tt.*
					FROM #__joomleague_treeto AS tt
					WHERE tt.id = ' . (int) $this->_id;

			$db->setQuery($query);
			$this->_data = $db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	function _initDataOBS()
	{
		// Lets load the content if it doesn't already exist
		if(empty($this->_data))
		{
			$treeto = new stdClass();
			$treeto->id = 0;
			$treeto->project_id = 0;
			$treeto->division_id = 0;
			$treeto->tree_i = 0;
			$treeto->name = null;
			$treeto->global_bestof = 0;
			$treeto->global_matchday = 0;
			$treeto->global_known = 0;
			$treeto->global_fake = 0;
			$treeto->leafed = 0;
			$treeto->mirror = 0;
			$treeto->hide = 0;
			$treeto->trophypic = null;
			$treeto->extended = null;
			$treeto->published = 0;
			$treeto->checked_out = 0;
			$treeto->checked_out_time = 0;
			$treeto->modified = null;
			$treeto->modified_by = null;

			$this->_data = $treeto;
			return (boolean) $this->_data;
		}
		return true;
	}

	function setGenerateNode($post)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$treeto_id = (int) $post['id'];
		$tree_i = (int) $post['jform']['tree_i'];
		$global_bestof = 0;
		$global_matchday = 0;
		$global_known = 0;
		$global_fake = 0;

		if(isset($post['global_bestof']))
		{
			$global_bestof = (int) $post['global_bestof'];
		}

		if(isset($post['global_matchday']))
		{
			$global_matchday = (int) $post['global_matchday'];
		}

		if(isset($post['global_known']))
		{
			$global_known = (int) $post['global_known'];
		}

		if(isset($post['global_fake']))
		{
			$global_fake = (int) $post['global_fake'];
		}

		if($tree_i == 0) // nothing selected in dropdown
		{
			return false;
		}
		elseif($tree_i > 0)
		{
			// data(global parameters) to treeto
			$query = ' UPDATE #__joomleague_treeto AS tt ';
			$query .= ' SET ';
			$query .= ' global_bestof = ' . $global_bestof;
			$query .= ' ,global_matchday = ' . $global_matchday;
			$query .= ' ,global_known = ' . $global_known;
			$query .= ' ,global_fake = ' . $global_fake;
			$query .= ' ,leafed = ' . 2;
			$query .= ' ,tree_i = ' . $tree_i;
			$query .= ' WHERE tt.id = ' . $treeto_id;
			$query .= ';';
			$db->setQuery($query);
			$db->execute($query);
			// nodes to treeto_node
			for($nod = 1;$nod <= ((pow(2,$tree_i + 1)) - 1);$nod ++)
			{
				$i = $tree_i;
				$x = $nod;
				$ii = pow(2,$i);
				$row = $ii;

				while($x > 1)
				{
					if($x >= (pow(2,$i)))
					{
						if(($x) % 2 == 1)
						{
							$row += $ii * (1 / (pow(2,$i)));
							$i --;
						}
						else
						{
							$row -= $ii * (1 / (pow(2,$i)));
							$i --;
						}
						$x = floor($x / 2);
					}
					else
					{
						$i --;
					}
				}
				$query = ' INSERT INTO #__joomleague_treeto_node ';
				$query .= ' SET ';
				$query .= ' treeto_id = ' . $treeto_id;
				$query .= ' ,node = ' . $nod;
				$query .= ' ,row = ' . $row;
				$query .= ' ,bestof = ' . $global_bestof;
				$query .= ';';
				$db->setQuery($query);
				$db->execute($query);
			}
			return true;
		}
	}

	public function getScript()
	{
		$script = 'administrator/components/com_joomleague/models/forms/treeto.js';

		return $script;
	}
}
