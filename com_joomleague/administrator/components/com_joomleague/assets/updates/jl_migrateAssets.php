<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// ToDo:
// - Remove old checks for already existing records in different functions as it was done with matches table
// - check ranking class changes in tables or templates etc...
// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

$version			= '3.0.22.57ae969-b';
$updateFileDate		= '2014-01-02';
$updateFileTime		= '23:25';
$updatefilename		= 'migrateAssets';
$lastVersion		= '2.0';
$JLtablePrefix		= 'joomleague';
$updateDescription	= '<span style="color:orange">Perform an update of existing old JL 1.6 tables inside the database to work with latest JoomLeague Assets System (ACL)</span>';
$excludeFile		= 'false';

if(!function_exists('PrintStepResult')) {
function PrintStepResult($result)
{
	if ($result)
	{
		$output=' - <span style="color:green">'.Text::_('SUCCESS').'</span>';
	}
	else
	{
		$output=' - <span style="color:red">'.Text::_('FAILED').'</span>';
	}

	return $output;
}}

function migrateAssets()
{
	$maxImportTime=ComponentHelper::getParams('com_joomleague')->get('max_import_time',0);
	if (empty($maxImportTime))
	{
		$maxImportTime=9000;
	}
	if ((int)ini_get('max_execution_time') < $maxImportTime){
		@set_time_limit($maxImportTime);
	}
	
	$query="SHOW TABLES LIKE '%_joomleague%'";
	$db = Factory::getDbo();
	$tables = array();
	$db->setQuery($query);
	$results = $db->loadColumn();
	foreach ($results as $tablename)
	{
		$fields = $db->getTableColumns($tablename);
		foreach($fields as $field)
		{
			if(in_array('asset_id', array_keys ($field))) {
				$tables[] = $tablename;
			}
		}
	}
	for ($i=0; $i < count($tables); $i++) {
		$table = $tables[$i];
		$query='SELECT id FROM '.$table;
		$db->setQuery($query);
		if ($items=$db->loadObjectList()) {
			$table = str_replace($db->getPrefix().'joomleague_','', $table);
			foreach($items as $item) {
				// $type is case sensitive now
				$tableType = str_replace(' ', '', ucwords(str_replace('_', ' ', $table)));
				$tbl = Table::getInstance($tableType, "Table");
				if ($tbl->load($item->id));
				{
					$tbl->store(true);
				}
			}
		}
		echo '<br>Migrated ' . count($items) . ' records in ' . $tables[$i] . ' ';
		echo PrintStepResult(true);
	}
	return '';
}

migrateAssets();