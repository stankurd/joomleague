<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author	Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

/**
 * Tools Controller
 */
class JoomleagueControllerTools extends JoomleagueController
{
	protected $view_list = 'tools';
	
	
	public function __construct()
	{
		parent::__construct();
		
		$jinput 	= Factory::getApplication()->input;
		$task 		= $jinput->getCmd('task');
		
		if ($task == 'exporttablecsv') {
			$this->registerTask($task, 'exportTableCsv');
		}
		
		if ($task == 'exporttablesql') {
			$this->registerTask($task, 'exportTableSql');
		}
		
		$this->registerTask('repair','repair');
		$this->registerTask('optimize','optimize');
		$this->registerTask('truncate','truncate');
	}
	
	
	public function display($cachable = false, $urlparams = false)
	{

		parent::display();
	}
	

	
	public function exportTableCsv() 
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$tables	= $jinput->get('cid', array(), 'array');
		$table	= $tables[0];
		$this->sendHeaders($table.'_'.date('Ymd') .'_' . date('Hi').".csv", "text/csv");
		
		$model = $this->getModel('tools');
		$model->getTableDataCsv($table);
		jexit();
	}

	public function exportTableSql() 
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		$tables	= $jinput->get('cid', array(), 'array');
		$table	= $tables[0];
		$this->sendHeaders($table.'_'.date('Ymd') .'_' . date('Hi').".sql", "text/plain");
		
		$model = $this->getModel('tools');
		$model->getTableDataSql($table);
		jexit();
	}
	
	
	public function truncate() 
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		
		$tables	= $jinput->get('cid', array(), 'array');
		$total  = count($tables);
		$model = $this->getModel('tools');
		
		if ($total == 1) {
			$table	= $tables[0];	
			if ($model->truncateTable($table)) {
				$app->enqueueMessage(Text::_('Table '.$table.' has been truncated'));
			} else {
				$app->enqueueMessage(Text::_('Table '.$table.' was not truncated'),'warning');
			}
		} else {
			// we did select multiple tables
			$result = $model->truncateTables($tables);
			if ($result) {
				$app->enqueueMessage(Text::_('Tables have been truncated'));
			} else {
				$app->enqueueMessage(Text::_('Tables were not truncated'),'warning');
			}
		}
		$this->setRedirect('index.php?option=com_joomleague&view=tools');
	}
	
	
	private function sendHeaders($filename = 'export.csv', $contentType = 'text/csv') 
	{
		header("Content-type: ".$contentType);
		header("Content-Disposition: attachment; filename=" . $filename);
		header("Pragma: no-cache");
		header("Expires: 0");
	}
	
	
	public function back() 
	{

		$link = 'index.php?option=com_joomleague';
		$this->setRedirect($link);	
	}
	
	public function optimize()
	{
		$app = Factory::getApplication();
		$model = $this->getModel('tools');
		if ($model->optimize())
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_OPTIMIZE'));
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_OPTIMIZE').$model->getError(),'error');
		}
		$link='index.php?option=com_joomleague&view=tools';
		$this->setRedirect($link);
	}
	
	public function repair()
	{
		$app = Factory::getApplication();
		$model = $this->getModel('tools');
		if ($model->repair())
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_REPAIR'));
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_REPAIR').$model->getError(),'error');
		}
		$link='index.php?option=com_joomleague&view=tools';
		$this->setRedirect($link);
	}
	
	
	public function cleanCache()
	{

		$app = Factory::getApplication();
		
		$model = $this->getModel('tools');
		$model->cleanCache();
		
		$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_CTRL_TOOLS_CLEANCACHE'));
		
		$link='index.php?option=com_joomleague&view=tools';
		$this->setRedirect($link);
	}
	
	
	public function clearUserState()
	{
		$app = Factory::getApplication();
	
		$model = $this->getModel('tools');
		$model->clearUserState();
	
		$app->enqueueMessage(Text::_('Userstate variables of Joomleague have been cleared'));
	
		$link='index.php?option=com_joomleague&view=tools';
		$this->setRedirect($link);
	}
	
	
	public function removeLanguageFiles() 
	{
		$app = Factory::getApplication();
		
		$model = $this->getModel('tools');
		$model->removeLanguageFiles();
		
		$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_CTRL_TOOLS_LANGUAGEFILES_REMOVED'));
		
		$link='index.php?option=com_joomleague&view=tools';
		$this->setRedirect($link);
	}
}
