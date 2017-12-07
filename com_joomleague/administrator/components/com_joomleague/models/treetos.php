<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;

defined('_JEXEC') or die;


/**
 * Treetos Model
 */
class JoomleagueModelTreetos extends JLGModelList
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

		$project_id = $app->getUserState($option . 'project');
		$division = (int) $app->getUserStateFromRequest($option . '.division','division',0);
		$division = StringHelper::strtolower($division);

		// Select the required fields from the table.
		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_treeto AS a');

		// join division table
		$query->join('LEFT','#__joomleague_division AS d on d.id = a.division_id');

		$query->where('a.project_id = ' . $project_id);
		if($division > 0)
		{
			$query->where('d.id = ' . $db->Quote($division));
		}

		$query->order('a.id DESC ');

		return $query;
	}

	function storeshort($cid,$data)
	{
		$result = true;
		for($x = 0;$x < count($cid);$x ++)
		{

			$tblTreeto = Table::getInstance('Treeto','Table');
			$tblTreeto->id = $cid[$x];
			$tblTreeto->division_id = $data['division_id' . $cid[$x]];

			if(! $tblTreeto->check())
			{
				$this->setError($tblTreeto->getError());
				$result = false;
			}
			if(! $tblTreeto->store())
			{
				$this->setError($tblTreeto->getError());
				$result = false;
			}
		}
		return $result;
	}
}
