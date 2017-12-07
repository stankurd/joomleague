<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Rounds Model
 */
class JoomleagueModelRounds extends JLGModelList
{

	public $project_id = 0;

	/**
	 * 
	 */
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
					'a.id'
			);
		}

		parent::__construct($config);
	}

	
	/**
	 * @see JModelList::populateState()
	 */
	protected function populateState($ordering = null,$direction = null)
	{
		$app = Factory::getApplication();

		// Adjust the context to support modal layouts.
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.'.$layout;
		}

		$input = $app->input;
		$project_id = $app->getUserState('com_joomleagueproject');
		$this->project_id = $project_id;
		
		$value = $this->getUserStateFromRequest($this->context.'.filter_order','filter_order','a.round_date_first','string');
		$this->setState('list.ordering',$value);
		
		$value = $this->getUserStateFromRequest($this->context.'.filter_order_Dir','filter_order_Dir','ASC','word');
		$this->setState('list.direction',$value);
	}

	
	/**
	 * @see JModelList::getStoreId()
	 */
	protected function getStoreId($id = '')
	{
		return parent::getStoreId($id);
	}

	
	/**
	 * @see JModelList::getListQuery()
	 */
	protected function getListQuery()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');

		$project_id = $app->getUserState($option.'project');
		$params = ComponentHelper::getParams('com_joomleague');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','a.*'));
		$query->from('#__joomleague_round AS a');

		// join user table
		$query->select('u.name AS editor');
		$query->join('LEFT','#__users AS u ON u.id = a.checked_out');

		// count-unpublished
		$query->select(
				'(SELECT COUNT(published) FROM #__joomleague_match WHERE round_id = a.id AND published = 0) AS '.$db->QuoteName('countUnPublished'));

		// count-noresults
		$query->select(
				'(SELECT COUNT(*) FROM #__joomleague_match WHERE round_id = a.id AND cancel=0 AND (team1_result is null OR team2_result is null)) AS ' .
						 $db->QuoteName('countNoResults'));

		// count-matches
		$query->select('(SELECT COUNT(*) FROM #__joomleague_match WHERE round_id = a.id) AS '.$db->QuoteName('countMatches'));

		// Where
		$query->where('a.project_id = '.$project_id);

		// order
		$filter_order = $this->getState('list.ordering');
		$filter_order_Dir = $this->getState('list.direction');
		if($filter_order == 'a.id')
		{
			$query->order($db->escape('a.id '.$filter_order_Dir));
		}
		else
		{
			$query->order(array($db->escape($filter_order.' '.$filter_order_Dir)));
		}
		
		return $query;
	}

	
	/**
	 * 
	 */
	function setProjectId($project_id)
	{
		$this->_project_id = $project_id;
	}

	
	/**
	 * 
	 */
	function getProjectId()
	{
		return $this->_project_id;
	}

	/**
	 * Method to return the projectTeams array (id,name)
	 *
	 * @access public
	 * @return array
	 */
	function getProjectTeams()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$project_id = $app->getUserState($option . 'project');
		
		$division_id = $input->getInt('division_id',0);

		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('t.id AS value,t.name AS text,t.notes');
		$query->from('#__joomleague_team AS t');

		// Project-team
		$query->select('pt.id AS projectteam_id, pt.ordering');
		$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');

		// Where
		$query->where('pt.project_id = '.$project_id);

		if($division_id > 0)
		{
			$query->where('pt.division_id = '.$division_id);
		}
		$query->order('pt.ordering, text ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadObjectList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return array();
		}
		return $result;
	}

	/**
	 *
	 * @param int $projectid
	 * @return assocarray
	 */
	function getFirstRound($projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, roundcode');
		$query->from('#__joomleague_round');
		$query->where('project_id = '.$projectid);
		$query->order('roundcode ASC, id ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		
		return $result[0];
	}
	
	
	/**
	 *
	 */
	function getLastRound($projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, roundcode');
		$query->from('#__joomleague_round');
		$query->where('project_id='.$projectid);
		$query->order('roundcode DESC, id DESC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result[0];
	}


	/**
	 *
	 */
	function getNextRound($roundid,$projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, roundcode');
		$query->from('#__joomleague_round');
		$query->where('project_id='.$projectid);
		$query->order('id ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		for($i = 0,$n = count($result);$i < $n;$i ++)
		{
			if($result[$i]['id'] == $roundid)
			{
				if(isset($result[$i + 1]))
				{
					return $result[$i + 1];
				}
				else
				{
					return $result[$i];
				}
			}
		}
	}


	/**
	 * Get the next round by todays date
	 *
	 * @param int $project_id
	 * @return assocarray
	 */
	function getNextRoundByToday($projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('r.id, r.roundcode, r.round_date_first , r.round_date_last');
		$query->from('#__joomleague_round AS r');
		$query->where('r.project_id = '.$db->Quote($projectid));
		$query->where('DATEDIFF(CURDATE(), DATE(r.round_date_first)) < 0');
		$query->order('r.round_date_first ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}

	/**
	 *
	 * @param int $roundid
	 * @param int $projectid
	 * @return assocarray
	 */
	function getPreviousRound($roundid,$projectid)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, roundcode');
		$query->from('#__joomleague_round');
		$query->where('project_id = ' . $projectid);
		$query->order('id ASC');
		try
		{
			$db->setQuery($query);
			$result = $db->loadAssocList();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(JText::_($e->getMessage()), 'error');
			return false;
		}
		for($i = 0,$n = count($result);$i < $n;$i ++)
		{
			if(isset($result[$i - 1]))
			{
				return $result[$i - 1];
			}
			else
			{
				return $result[$i];
			}
		}
	}

	/**
	 * return project rounds as array of objects(roundid as value, name as text)
	 *
	 * @param string $ordering
	 * @return array
	 */
	function getRoundsOptions($project_id,$ordering = 'ASC')
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = "SELECT
					id as value,
				    CASE LENGTH(name)
				    	when 0 then CONCAT('" . JText::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAY_NAME') . "',' ', id)
				    	else name
				    END as text, id, name, round_date_first, round_date_last, roundcode
				  FROM #__joomleague_round
				  WHERE project_id=" . $project_id . "
				  ORDER BY roundcode " . $ordering;

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * return count of project rounds
	 *
	 * @param
	 *        	int project_id
	 * @return int
	 */
	function getRoundsCount($project_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(id) AS count');
		$query->from('#__joomleague_round');
		$query->where('project_id = ' . $project_id);
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Add multiple rounds to an existing project
	 *
	 * @param int $projectId		of the project to which the rounds will be added
	 * @param string $scheduling	scheduling method
	 * @param int $addRoundCount	of rounds to add
	 * @param string $startDate		of the first round that is added
	 * @param int $interval			(in days) between rounds
	 * @return string Message 		to display on redirect
	 */
	function massAddRounds($projectId,$scheduling,$addRoundCount,$startDate,$interval)
	{
		$feedback = array('msg' => '','msgType' => 'message');

		try
		{
			// Fill $rounds array from the input data
			$rounds = array();
			if($scheduling === "0")
			{
				$rounds = $this->prepareRoundsFromInterval($projectId,$addRoundCount,$startDate,$interval);
			}
			else
			{
				$filename = $scheduling;
				$rounds = $this->prepareRoundsFromTemplate($projectId,$filename);
			}

			$this->storeRounds($rounds);
			$feedback['msg'] = JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_SUCCESS',count($rounds));
		}
		catch(Exception $e)
		{
			$feedback['msgType'] = 'error';
			$feedback['msg'] = Jtext::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_FAILURE',$e->getMessage());
		}
		return $feedback;
	}

	/**
	 * Prepare the rounds that need to be added
	 *
	 * @param int $projectId of the project to which the rounds will be added
	 * @param int $addRoundCount of rounds to add
	 * @param string $startDate of the first round that is added (in the format "dd-mm-yyyy")
	 * @param int $interval (in days) between rounds
	 * @throws Exception
	 * @return array of rounds, with the following keys: project_id, roundcode, name, round_date_first, round_date_last
	 */
	private function prepareRoundsFromInterval($projectId,$addRoundCount,$startDate,$interval)
	{
		$rounds = array();
		if($addRoundCount > 0)
		{
			$roundCode = $this->getRoundsCount($projectId) + 1;
			$roundDate = DateTime::createFromFormat("d-m-Y",$startDate,new DateTimeZone('UTC'));
			if($roundDate == false)
			{
				throw new Exception(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_ERROR_START_DATE'));
			}
			for($x = 0;$x < $addRoundCount;$x ++)
			{
				$round = array(
						'project_id' => $projectId,
						'roundcode' => $roundCode,
						'name' => JText::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_NAME',$roundCode),
						'round_date_first' => $roundDate->format('Y-m-d'),
						'round_date_last' => $roundDate->format('Y-m-d')
				);
				$rounds[] = $round;

				$roundCode ++;
				$roundDate->add(new DateInterval('P' . $interval . 'D'));
			}
		}
		return $rounds;
	}

	/**
	 * Read the round definition from given CSV-file
	 *
	 * @param int $projectId of the project to which the rounds are added
	 * @param string $filename of the file containing the rounds template
	 * @throws Exception
	 * @return array of rounds, with the following keys: project_id, roundcode, name, round_date_first, round_date_last
	 */
	private function prepareRoundsFromTemplate($projectId,$filename)
	{
		$exception = null;

		try
		{
			$path = JPath::clean(JPATH_ROOT.'/media/com_joomleague/database/round_templates');
			$handle = fopen($path.'/'.$filename,'r');
			if(! $handle)
			{
				$app = Factory::getApplication();
				$app->enqueueMessage(JText::_('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_CANNOT_OPEN'),'error');
				$app->redirect('index.php?option=com_joomleague&view=rounds');
				return false;
			}
			$delimiter = ";";
			$row = 1;
			$schedule = array();
			while(($data = fgetcsv($handle,10000,$delimiter,'"')) !== FALSE)
			{
				if(count($data) != 4)
				{
					throw new Exception(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_ERROR_INVALID_TEMPLATE'));
				}

				$round = array(
						'project_id' => $projectId,
						'roundcode' => $data[0],
						'name' => $data[1]
				);
				$dateFirst = DateTime::createFromFormat("d-m-Y",$data[2],new DateTimeZone('UTC'));
				if($dateFirst == false)
				{
					throw new Exception(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_ERROR_INVALID_FIRST_DATE'));
				}
				$round['round_date_first'] = $dateFirst->format('Y-m-d');
				$dateLast = DateTime::createFromFormat("d-m-Y",$data[3],new DateTimeZone('UTC'));
				if($dateLast == false)
				{
					throw new Exception(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_ERROR_INVALID_LAST_DATE'));
				}
				$round['round_date_last'] = $dateLast->format('Y-m-d');
				$schedule[] = $round;
			}
		}
		catch(Exception $e)
		{
			$exception = $e;
		}

		// Close the file when there is a valid handle, also when an exception occurred!
		if ($handle)
		{
			fclose($handle);
		}
		// If an exception occurred, then pass it on to the caller
		if ($exception != null)
		{
			throw $exception;
		}

		return $schedule;
	}

	/**
	 * Store the prepared rounds in the database
	 *
	 * @param array $rounds
	 *        	of rounds, with the following keys:
	 *        	project_id, roundcode, name, round_date_first, round_date_last
	 * @throws Exception
	 */
	private function storeRounds($rounds)
	{
		if(count($rounds) > 0)
		{
			foreach($rounds as $round)
			{
				$tblRound = Table::getInstance('Round','Table');
				$tblRound->project_id = $round['project_id'];
				$tblRound->round_date_first = $round['round_date_first'];
				$tblRound->round_date_last = $round['round_date_last'];
				$tblRound->roundcode = $round['roundcode'];
				$tblRound->name = $round['name'];
				if(! ($tblRound->check() && $tblRound->store()))
				{
					throw new Exception(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_ERROR_STORING_ROUND') . ': ' . $tblRound->getError());
				}
			}
		}
	}

	/**
	 * Populate project with matchdays
	 *
	 * @param int $project_id project id
	 * @param int $scheduling scheduling type
	 * @param string $time start time for games
	 * @param int $interval interval between rounds
	 * @param string $start start date for new roundsrounds
	 * @param string $roundname round name format (use %d for round number)
	 * @param array $teamsorder list of teams
	 * @param int $matchnummer starting by number, increasing by 1
	 * @return boolean true on success
	 */
	function populate($project_id,$scheduling,$time,$interval,$start,$roundname,$teamsorder = null,$iMatchnumber = 0)
	{
		$teams = $this->getOrderedProjectTeams($teamsorder);
	
		if(!$teams)
		{
			// we need teams to create a round
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_NO_TEAM'));
			return false;
		}
		if($scheduling == "0" || $scheduling == "1")
		{
			// scheduling 1 = Single Round Robin
			// scheduling 2 = Double Round robin 
			$schedule = $this->getRoundRobinSchedule($teams,$scheduling);
			if (count($teams) < 3) {
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_NEEDMORETEAMS'));
				return false;
			}
		}
		else
		{
			// here we did select a different scheduling type
			$filename = $scheduling;
			$schedule = $this->getScheduleFromTemplate($teams,$filename);
		}
		if(!$schedule)
		{
			return false;
		}

		return $this->createRoundsAndGamesFromSchedule($project_id,$time,$interval,$start,$roundname,$iMatchnumber,$schedule);
	}

	/**
	 * Get the project teams, ordered by the ordering field of the project
	 * teams.
	 *
	 * @param array $teamsorder list of teams
	 * @return array of project teams in the desired order, or false if there are no project teams
	 */
	private function getOrderedProjectTeams($teamsorder)
	{
		
		$teams = $this->getProjectTeams();
		
		if ($teamsorder)
		{
			$ordered = array();
			foreach ($teamsorder as $ptid)
			{
				foreach ($teams as $t)
				{
					if ($t->projectteam_id == $ptid) {
		 				$ordered[] = $t;
						break;
					}
				}
			 }
			if ($ordered) {
				$teams = $ordered;
			}
		} else {
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_NO_TEAM'));
			return false;
		}
		
		/* -- alternative method --
		 $teams = $this->getProjectTeams();
		 if(! $teams || ! count($teams))
		 {
		 $this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_NO_TEAM'));
		 return false;
		 }
		
		 // You can already indicate an ordering for the project teams in the
		 // project, so we can use that.
		 $teams = JArrayHelper::sortObjects($teams,"ordering");
		 return $teams;
		 */
			
		return $teams;
	}
	

	/**
	 * Get the match schedule using Round Robin scheme
	 *
	 * @param array ordered list of project teams
	 * @param int $scheduling use 0 for a single and 1 for a double Round Robin scheme
	 * @return the match schedule (array) if everything went ok, false otherwise
	 */
	private function getRoundRobinSchedule($teams,$scheduling)
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/rrobin.php';
		$helper = new RRobin();
		$result = $helper->create($teams);
		
		if(!$result)
		{
			return false;
		}
		$schedule = $helper->getSchedule($scheduling + 1);
		return $schedule;
	}

	/**
	 * Get the match schedule from a template
	 *
	 * @param array ordered list of project teams
	 * @param string $filename name of the template file that specifies the round matches
	 * @return array the match schedule if file could be read and is ok, false otherwise
	 */
	private function getScheduleFromTemplate($teams,$filename)
	{
		$path = JPath::clean(JPATH_ROOT . '/media/com_joomleague/database/round_populate_templates');
		$handle = fopen($path . '/' . $filename,'r');
		if(! $handle)
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(JText::_('COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_CANNOT_OPEN'),'error');
			$app->redirect('index.php?option=com_joomleague&view=rounds');
			return false;
		}
		$delimiter = ";";
		$row = 1;
		$schedule = array();
		while(($data = fgetcsv($handle,10000,$delimiter,'"')) !== FALSE)
		{
			$num = count($data);
			// if($num-1 < count($teams)/2) {
			// $this->setError(JText::_ (
			// 'COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_TOTAL_TEAMS_WRONG'
			// ));
			// return false;
			// }
			$games = array();
			for($c = 1;$c < $num;$c ++)
			{
				$pair = $data[$c];
				$order = explode("-",$pair);
				if(isset($teams[($order[0] - 1)]))
				{
					$home_team = $teams[($order[0] - 1)];
				}
				else
				{
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_CANNOT_FIND_ORDER_NUMBER') . ': ' . $order[0]);
					return false;
				}
				if(isset($teams[($order[1] - 1)]))
				{
					$away_team = $teams[($order[1] - 1)];
				}
				else
				{
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_CANNOT_FIND_ORDER_NUMBER') . ': ' . $order[1]);
					return false;
				}
				if($away_team == $home_team)
				{
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_TEAMS_ORDERING_WRONG'));
					return false;
				}
				// match pair
				$game = array();
				$game[0] = $home_team;
				$game[1] = $away_team;
				// all matches per round
				$games[] = $game;
			}
			// assign matches to round
			$schedule[] = $games;
			$row ++;
		}
		fclose($handle);
		if(count($schedule) == 0)
		{
			return false;
		}
		return $schedule;
	}

	/**
	 * Populate project with matchdays
	 *
	 * @param int $project_id project id
	 * @param string $time start time for games
	 * @param int $interval interval between rounds
	 * @param string $start start date for new roundsrounds
	 * @param string $roundname round name format (use %d for round number)
	 * @param int $matchnummer starting by number, increasing by 1
	 * @param array $schedule per round the matches to be played
	 * @return boolean true on success
	 */
	private function createRoundsAndGamesFromSchedule($project_id,$time,$interval,$start,$roundname,$iMatchnumber,$schedule)
	{
		if(! strtotime($start))
		{
			$start = strftime('%Y-%m-%d');
		}
		if(! preg_match("/^[0-9]+:[0-9]+$/",$time))
		{
			$time = '20:00';
		}

		// @todo
		// change, we only have to know the id of the round so
		// would be fine to just check that and not load all the data
		$rounds = $this->getItems();
		$rounds = $rounds ? $rounds : array();

		$current_date = null;
		$current_code = 0;
		foreach($schedule as $k=>$games)
		{
			if(isset($rounds[$k])) // round exists
			{
				$round_id = $rounds[$k]->id;
				$current_date = $rounds[$k]->round_date_first;
				$current_code = $rounds[$k]->roundcode;
			}
			else // create the round !
			{
				$tblRound = Table::getInstance('Round','Table');
				$tblRound->project_id = $project_id;
				$tblRound->round_date_first = strtotime($current_date) ? strftime('%Y-%m-%d',strtotime($current_date) + $interval * 24 * 3600) : $start;
				$tblRound->round_date_last = $tblRound->round_date_first;
				$tblRound->roundcode = $current_code ? $current_code + 1 : 1;
				$tblRound->name = sprintf($roundname,$tblRound->roundcode);
				if(! ($tblRound->check() && $tblRound->store()))
				{
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_CREATING_ROUND') . ': ' . $tblRound->getError());
					return false;
				}
				$current_date = $tblRound->round_date_first;
				$current_code = $tblRound->roundcode;
				$round_id = $tblRound->id;
			}

			// create games !
			// we need to convert game date+time to utc
			$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
			$project 	= $mdlProject->getItem($project_id);

			$project_tz = $project->timezone;
			$utc_tz = new DateTimeZone('UTC');
			$date = Factory::getDate($current_date . ' ' . $time,$project_tz)->setTimezone($utc_tz);
			$utc_sql_date = $date->toSql();
			foreach($games as $g)
			{
				if(! isset($g[0]) || ! isset($g[1]))
				{ // happens if number
				  // of team is odd ! one team
				  // gets a by
					continue;
				}
				$tblMatch = Table::getInstance('Match','Table');
				$tblMatch->round_id = $round_id;
				$tblMatch->projectteam1_id = $g[0]->projectteam_id;
				$tblMatch->projectteam2_id = $g[1]->projectteam_id;
				$tblMatch->match_date = $utc_sql_date;
				$tblMatch->published = 1;
				if($iMatchnumber > 0)
				{
					$tblMatch->match_number = $iMatchnumber ++;
				}
				if(! ($tblMatch->check() && $tblMatch->store()))
				{
					$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR_CREATING_GAME') . ': ' . $tblMatch->getError());
					return false;
				}
			}
		}
		return true;
	}


	/**
	 * Method to update checked rounds
	 *
	 * @access public
	 * @return boolean on success
	 *
	 */
	function storeshortAjax($name,$value,$pk)
	{
		$result = true;

		$tblRound = Table::getInstance('Round','Table');
		$tblRound->id = $pk;
		$tblRound->$name = $value;
		if(!$tblRound->check())
		{
			$this->setError($tblRound->getError());
			$result = false;
		}
		if(!$tblRound->store())
		{
			$this->setError($tblRound->getError());
			$result = false;
		}

		return $result;
	}

}
