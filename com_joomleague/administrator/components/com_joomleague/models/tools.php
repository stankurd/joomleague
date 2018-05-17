<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/list.php';


function add_apostroph($str) {
	return sprintf("`%s`", $str);
}

function add_quotes($str) {
	return sprintf("'%s'", $str);
}


/**
 * Tools Model
 */
class JoomleagueModelTools extends JoomleagueModelList
{
	
	private $jltables = null;
	
	public function __construct($config = array())
	{
		$this->jltables = $this->getTables();
		
		return parent::__construct();
	}
	
	/**
	 * Function to retrieve JL Tables
	 */
	function getTables() {
		
		$tables = array(
				'joomleague_club',
				'joomleague_division',
				'joomleague_eventtype',
				'joomleague_league',
				'joomleague_match',
				'joomleague_match_event',
				'joomleague_match_player',
				'joomleague_match_referee',
				'joomleague_match_staff',
				'joomleague_match_staff_statistic',
				'joomleague_match_statistic',
				'joomleague_person',
				'joomleague_playground',
				'joomleague_position',
				'joomleague_position_eventtype',
				'joomleague_position_statistic',
				'joomleague_project',
				'joomleague_project_position',
				'joomleague_project_referee',
				'joomleague_project_team',
				'joomleague_round',
				'joomleague_season',
				'joomleague_sports_type',
				'joomleague_statistic',
				'joomleague_team',
				'joomleague_team_player',
				'joomleague_team_staff',
				'joomleague_team_trainingdata',
				'joomleague_template_config',
				'joomleague_treeto',
				'joomleague_treeto_match',
				'joomleague_treeto_node',
				'joomleague_version'
		);
	
		
		$db 		= Factory::getDbo();
		$tableList	= $db->getTableList();
		$prefix 	= $db->getPrefix();
		
		$data = array();
		foreach ($tableList As $row) {
			
			$row = str_replace($prefix, "", $row);	
			if (in_array($row, $tables)) {
				$data[] = $row;	
			}
		}
		
		return $data;
	}
	
	
	/**
	 * Returns a CSV file with Table data
	 */
	public function getTableDataCsv($table)
	{
		// start
		$csv = fopen('php://output', 'w');
		fputs($csv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
		$db 	= Factory::getDBO();
		$query = $db->getQuery(true);
	
		// header
		$header = array();
		$header = array_keys($db->getTableColumns('#__'.$table));
		fputcsv($csv, $header, ';');
	
		// content
		$items = $db->setQuery($this->getListQueryTableData($table))->loadObjectList();
		foreach ($items as $lines) {
			fputcsv($csv, (array) $lines, ';', '"');
		}
	
		// close
		return fclose($csv);
	}
	
	
	/**
	 * Build an query to load the Table data.
	 */
	protected function getListQueryTableData($table)
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
	
		// Select the required fields from the table.
		$query->select('*');
		$query->from('#__'.$table);
	
		return $query;
	}
	
	
	/**
	 * Returns a SQL file with data
	 * @return boolean
	 */
	public function getTableDataSQL($table)
	{
		# start output
		$sql	= fopen('php://output', 'w');
		$db		= $this->getDbo();
		$query = $db->getQuery(true);
	
		if (is_array($table)) {
			$tables	= $table;
			foreach ($tables as $table) {
	
				$query = $this->getListQueryTableDataSQL($table);
				$rows = $this->_getList($query);
	
				$result	= count($rows);
				if ($result == 0) {
					continue;
				}
	
				# retrieve columns
				$columns = array();
				$columns = array_keys($db->getTableColumns('#__jem_'.$table));
				$columns =  implode(',', array_map('add_apostroph', $columns));
	
				$data = '';
				$start = "INSERT INTO `".$db->getPrefix().$table."` (".$columns.") VALUES";
				$start .= "\r\n";
	
				fwrite($sql,$start);
	
				foreach ($rows as $row) {
					$values = get_object_vars($row);
					$values = implode(',',array_map('add_quotes',$values));
	
					$data.= '('.$values.')';
					$data.=",";
					$data.= "\r\n";
				}
	
				$data = substr_replace($data ,"",-3);
	
				fwrite($sql,$data);
	
				$end = ";\n\n\n";
				fwrite($sql,$end);
			}
	
		} else {
			# retrieve columns
			$columns = array();
			$columns = array_keys($db->getTableColumns('#__'.$table));
			$columns =  implode(',', array_map('add_apostroph', $columns));
	
			$data = '';
			$start = "INSERT INTO `".$db->getPrefix().$table."` (".$columns.") VALUES";
			$start .= "\r\n";
	
			fwrite($sql,$start);
	
			$query = $this->getListQueryTableDataSQL($table);
			$rows = $this->_getList($query);
	
			foreach ($rows as $row) {
				$values = get_object_vars($row);
				$values = implode(',',array_map('add_quotes',$values));
	
				$data.= '('.$values.')';
				$data.=",";
				$data.= "\r\n";
			}
	
			$data = substr_replace($data ,"",-3);
	
			fwrite($sql,$data);
	
			$end = ";\n";
			fwrite($sql,$end);
		}
	
		# return output
		return fclose($sql);
	}
	
	
	/**
	 * Build a query to load the Table data.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQueryTableDataSQL($table)
	{
		# Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
	
		# retrieve data
		$query->select('*');
		$query->from('#__'.$table);
	
		return $query;
	}	
	
	
	/**
	 * Truncate Table
	 */
	public function truncateTable($table) {
		
		$db = Factory::getDbo();
		$db->truncateTable("#__".$table);

		if(!$db->execute()) {
			return false;
		}
		
		// Removal of Assets
		$text = 'com_joomleague.'.substr($table, strpos($table, "_") + 1);    
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__assets');
		$query->where('LOWER(name) LIKE '.$db->Quote('%'.$text.'%'));
		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $i => $row) {
			$query = $db->getQuery(true);
			$query->delete('#__assets');
			$query->where('id = '.$row->id);
			$query = $db->setQuery($query);
			$db->execute();
		}
		
		return true;
	}
	
	
	
	/**
	 * Truncate Tables
	 * @todo: improve
	 */
	public function truncateTables($tables) 
	{
		$result = array();	
		$error = array();
		$truncated = array();
		
		foreach ($tables AS $table) 
		{
			$db = Factory::getDbo();
			$db->truncateTable("#__".$table);
			try
			{
				$truncated[] = $db->execute();
			}
			catch(Exception $e)
			{
				$error[] = $e->getErrorMsg();
			}
			
			// Removal of Assets
			$text = 'com_joomleague.'.substr($table, strpos($table, "_") + 1);
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from('#__assets');
			$query->where('LOWER(name) LIKE '.$db->Quote('%'.$text.'%'));
			$db->setQuery($query);
			$items = $db->loadObjectList();
			
			if ($items) {
				foreach ($items as $i => $row) {
					$query = $db->getQuery(true);
					$query->delete('#__assets');
					$query->where('id = '.$row->id);
					$query = $db->setQuery($query);
					try
					{
						$db->execute();
					}
					catch(Exception $e)
					{
						$e->getErrorMsg();
					}
				}
			}
		}	
		
		return true;
	}

	
	/**
	 * Optimize
	 */
	public function optimize()
	{
		$tables = $this->jltables;
		if ($tables) {
			$db = Factory::getDbo();
			$errors = array();
			foreach ($tables as $table)
			{
				$query='OPTIMIZE TABLE `'.$table.'`'; $db->setQuery($query);
				if (!$db->execute())
				{
					$errors[] = $this->setError($db->getErrorMsg());
					continue;
				}
			}
			
			if ($errors) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * Repair
	 */
	public function repair()
	{
		$tables = $this->jltables;
		
		if ($tables) {
			$db = Factory::getDbo();
			$errors = array();
			
			foreach ($tables as $table)
			{
				$query='REPAIR TABLE `'.$table.'`'; $db->setQuery($query);
				if (!$db->execute())
				{
					$errors[] = $this->setError($db->getErrorMsg());
					continue;
				}
			}
			
			if ($errors) {
				return false;
			}
		}
		
		return true;
	}
	
	
	/**
	 * Custom clean the cache of com_joomleague
	 *
	 * @param   string   $group      The cache group
	 * @param   integer  $client_id  The ID of the client
	 */
	function cleanCache($group = null, $client_id = 0)
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
				
		$folders = Folder::folders(JPATH_SITE.'/cache');
		
		foreach ($folders AS $folder) 
		{
			if (strpos($folder,'joomleague') !== false) {
				parent::cleanCache($folder);
			}
		}
		
		parent::cleanCache('com_joomleague');
	}
	
	
	/**
	 * Clear UserState variables
	 */
	function clearUserState() 
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$app->setUserState('com_joomleagueproject', null);
		$app->setUserState('com_joomleagueround', null);
		$app->setUserState('com_joomleagueround_id', null);
		$app->setUserState('com_joomleaguesportstypes', null);
		$app->setUserState('com_joomleagueseasonnav', null);
		$app->setUserState('com_joomleagueproject_team_id', null);
		
		return true;
	}
	
	
	/**
	 * remove language files of Joomleague from Joomla language folder
	 */
	function removeLanguageFiles()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
	
		// SITE
		$folders = Folder::folders(JPATH_SITE.'/language',false,true,true);
				
		foreach ($folders AS $folder)
		{
			$files = Folder::files($folder,'joomleague',true,true);
			if ($files) {
				foreach ($files AS $file) {
					File::delete($file);
				}
			}	
		
			// check if folder is empty
			if(count(scandir($folder)) == 2) {
				Folder::delete($folder);
			} 	
		}
		
		// BACKEND - LANGUAGETAG FOLDERS
		$folders = Folder::folders(JPATH_SITE.'/administrator/language',false,true,true);
		
		foreach ($folders AS $folder)
		{
			$files = Folder::files($folder,'joomleague',true,true);
			if ($files) {
				foreach ($files AS $file) {
					File::delete($file);
				}
			}
		
			// check if folder is empty
			if(count(scandir($folder)) == 2) {
				Folder::delete($folder);
			}
		}
		
		// BACKEND - LANGUAGEFOLDER ROOT
		$files = Folder::files(JPATH_SITE.'/administrator/language','joomleague',true,true);
		if ($files) {
			foreach ($files AS $file) {
				File::delete($file);
			}
		}
		
	}
}
