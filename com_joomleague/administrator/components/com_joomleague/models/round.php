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
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Round Model
 */
class JoomleagueModelRound extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.round';


	/**
	 * Method to remove a matchday
	 *
	 * @access public
	 * @return boolean on success
	 */
	function deleteMatches($cid = array(),$mdlMatches,$mdlMatch,$onlyMatches = false)
	{
		$result = false;
		if(count($cid))
		{
			ArrayHelper::toInteger($cid);
			$cids = implode(',',$cid);
			for($r = 0;$r < count($cid);$r ++)
			{
				// echo "Deleting Round: ".$cid[$r]."<br>";
				$matches = $mdlMatches->getMatchesByRound($cid[$r]);
				$matchids = array();
				for($m = 0;$m < count($matches);$m ++)
				{
					$matchids[] = $matches[$m]->id;
					// echo " Deleting Match: ".$matches[$m]->id."<br>";
				}
				$mdlMatch->delete($matchids);
			}
			if(! $onlyMatches)
			{
				return parent::delete($cids);
			}
		}
		return true;
	}

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type The table type to instantiate
	 * @param	string A prefix for the table class name. Optional.
	 * @param	array Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Round',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
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
		$form = $this->loadForm('com_joomleague.round','round',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}

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
		$data = $app->getUserState('com_joomleague.edit.round.data',array());

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


	/**
	 * Method to update checked rounds
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function storeshort($cid,$post)
	{
		$app = Factory::getApplication();
		$result = true;
		$db = Factory::getDbo();
		$inplaceEditing = $post['inplaceEditing'];
	
		for($x = 0;$x < count($cid);$x ++)
		{
			$query = $db->getQuery(true);
			$query->update('#__joomleague_round');
			$query->set(
					array(				
							'round_date_first	= '.$db->quote($post['round_date_first'.$cid[$x]]),
							'round_date_last	= '.$db->quote($post['round_date_last'.$cid[$x]]),
							'checked_out		= 0',
							'checked_out_time	= 0'
					));
			
			if ($inplaceEditing == '0') {
				$query->set(
						array(
								'roundcode	= '.$db->quote($post['roundcode'.$cid[$x]]),
								'name		= '.$db->quote($post['name'.$cid[$x]])
						));
			}
			$query->where('id = '.$cid[$x]);
			try
			{
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
	 * @param	$roundid
	 */
	function getRoundcode($roundid)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('roundcode');
		$query->from('#__joomleague_round');
		$query->where('id = ' . $roundid);
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * @param	$roundcode
	 * @param	$project_id
	 */
	function getRoundId($roundcode,$project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__joomleague_round');
		$query->where(array(
				'roundcode = ' . $roundcode,
				'project_id = ' . $project_id
		));
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}
}
