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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

$version = '3.0.22.57ae969-b';
$updateFileDate = '2013-02-09';
$updateFileTime = '13:25';
$updatefilename = 'jl_upgrade-1-6_to_2_0';
$lastVersion = '1.6';
$JLtablePrefix = 'joomleague';
$updateDescription = '<span style="color:orange">Perform an update of existing old JoomLeague 1.6 tables inside the database to work with latest JoomLeague</span>';
$excludeFile = 'false';

jimport ( 'joomla.filesystem.folder' );
jimport ( 'joomla.filesystem.file' );
$maxImportTime = JComponentHelper::getParams ( 'com_joomleague' )->get ( 'max_import_time', 0 );
if (empty ( $maxImportTime )) {
	$maxImportTime = 480;
}
if (( int ) ini_get ( 'max_execution_time' ) < $maxImportTime) {
	@set_time_limit ( $maxImportTime );
}

$maxImportMemory = JComponentHelper::getParams ( 'com_joomleague' )->get ( 'max_import_memory', 0 );
if (empty ( $maxImportMemory )) {
	$maxImportMemory = '150M';
}
if (( int ) ini_get ( 'memory_limit' ) < ( int ) $maxImportMemory) {
	ini_set ( 'memory_limit', $maxImportMemory );
}

/**
 * obj2Array#
 * converts simpleXml object to array
 *
 * Variables: $o['obj']: simplexml object
 *
 * @return
 *
 *
 */
function obj2Array($obj) {
	$arr = ( array ) $obj;
	if (empty ( $arr )) {
		$arr = '';
	} else {
		foreach ( $arr as $key => $value ) {
			if (! is_scalar ( $value )) {
				$arr [$key] = obj2Array ( $value );
			}
		}
	}
	return $arr;
}

if(!function_exists('PrintStepResult')) {
function PrintStepResult($result) {
	if ($result) {
		$output = ' - <span style="color:green">' . JText::_ ( 'SUCCESS' ) . '</span>';
	} else {
		$output = ' - <span style="color:red">' . JText::_ ( 'FAILED' ) . '</span>';
	}
	
	return $output;
}}

function doQueries($queries) {
	$db = JFactory::getDbo ();
	
	/* execute modifications */
	if (count ( $queries )) {
		foreach ( $queries as $query ) {
			$db->setQuery ( $query [0] );
			$db->query ( $query [0] );
			$bla = $db->getErrorNum ();
			
			echo '<br />';
			if ($bla == 0) {
				echo '<img	align="top" src="components/com_joomleague/assets/images/ok.png"
						alt="' . JText::_ ( 'SUCCESS' ) . '" title"' . JText::_ ( 'SUCCESS' ) . '">';
				echo '&nbsp;';
				echo $query [1];
			} else {
				echo '<img	align="top" src="components/com_joomleague/assets/images/error.png"
						alt="' . JText::_ ( 'Error' ) . '" title"' . JText::_ ( 'Error' ) . '">';
				echo '&nbsp;';
				echo '<pre>' . $db->getErrorMsg () . "</pre>$query<br/>$query[0]";
			}
		}
		echo '<br />';
	} else {
		echo ' - <span style="color:green">' . JText::_ ( 'No update was needed' ) . '</span>';
	}
	return '';
}

function UpdateTemplateMasters() {
	/**
	 * ********************************
	 * ******* Update Script for xml template to store the non existing Variable
	 * *********************************
	 */
	
	/*
	 * developer: diddipoeler date: 13.01.2011 Bugtracker Backend Bug #579 change old textname in newtextname
	 */
	$convert = array (
			'JL_AGAINST' => 'AGAINST',
			'JL_AVG' => 'AVG',
			'JL_BONUS' => 'BONUS',
			'JL_DIFF' => 'DIFF',
			'JL_FOR' => 'FOR',
			'JL_GB' => 'GB',
			'JL_H2H' => 'H2H',
			'JL_H2H_AWAY' => 'H2H_AWAY',
			'JL_H2H_DIFF' => 'H2H_DIFF',
			'JL_H2H_FOR' => 'H2H_FOR',
			'JL_LEGS' => 'LEGS',
			'JL_LEGS_DIFF' => 'LEGS_DIFF',
			'JL_LEGS_RATIO' => 'LEGS_RATIO',
			'JL_LEGS_WIN' => 'LEGS_WIN',
			'JL_LOSSES' => 'LOSSES',
			'JL_NEGPOINTS' => 'NEGPOINTS',
			'JL_OLDNEGPOINTS' => 'OLDNEGPOINTS',
			'JL_PLAYED' => 'PLAYED',
			'JL_POINTS' => 'POINTS',
			'JL_POINTS_RATIO' => 'POINTS_RATIO',
			'JL_QUOT' => 'QUOT',
			'JL_RESULTS' => 'RESULTS',
			'JL_SCOREAGAINST' => 'SCOREAGAINST',
			'JL_SCOREFOR' => 'SCOREFOR',
			'JL_SCOREPCT' => 'SCOREPCT',
			'JL_START' => 'START',
			'JL_TIES' => 'TIES',
			'JL_WINPCT' => 'WINPCT',
			'JL_WINS' => 'WINS' 
	);
	
	echo '<b>' . JText::_ ( 'Updating new variables and templates for usage in the Master-Templates' ) . '</b><br />';
	$db = JFactory::getDbo ();
	
	$query = 'SELECT id,name,master_template FROM #__joomleague_project';
	$db->setQuery ( $query );
	if ($projects = $db->loadObjectList ()) 	// check that there are projects...
	{
		// echo '<br />';
		$xmldir = JPATH_SITE.'/components/com_joomleague/settings/default';
		
		if ($handle = JFolder::files ( $xmldir )) {
			// check that each xml template has a corresponding record in the
			// database for this project (except for projects using master templates).
			// If not,create the rows with default values
			// from the xml file
			
			foreach ( $handle as $file ) {
				if ((strtolower ( substr ( $file, - 3 ) ) == 'xml') && (substr ( $file, 0, (strlen ( $file ) - 4) ) != 'table') && (substr ( $file, 0, 10 ) != 'prediction')) {
					$defaultconfig = array ();
					$template = substr ( $file, 0, (strlen ( $file ) - 4) );
					$out = simplexml_load_file ( $xmldir .'/'. $file, 'SimpleXMLElement', LIBXML_NOCDATA );
					$temp = '';
					$arr = obj2Array ( $out );
					$outName = JText::_($out->name[0]);
					echo '<br />' . JText::sprintf ( 'Template: [%1$s] - Name: [%2$s]', "<b>$template</b>", "<b>$outName</b>" ) . '<br />';
					if (isset ( $arr ["fieldset"] ["field"] )) {
						foreach ( $arr ["fieldset"] ["field"] as $param ) {
							$temp .= $param ["@attributes"] ["name"] . "=" . $param ["@attributes"] ["default"] . "\n";
							$defaultconfig [$param ["@attributes"] ["name"]] = $param ["@attributes"] ["default"];
						}
					} else {
						foreach ( $arr ["fieldset"] as $paramsgroup ) {
							foreach ( $paramsgroup ["field"] as $param ) {
								
								if (! isset ( $param ["@attributes"] )) {
									if (isset ( $param ["name"] )) {
										$temp .= $param ["name"] . "=" . $param ["default"] . "\n";
										$defaultconfig [$param ["name"]] = $param ["default"];
									}
								} else if (isset ( $param ["name"] )) {
									/*
									 * developer: diddipoeler date: change on 13.01.2011 Bugtracker Backend Bug #579 error message string to object example template teamstats
									 */
									// $temp .= $param["@attributes"]["name"]."=".$param["@attributes"]["default"]."\n";
									$temp .= $param ["name"] . "=" . $param ["default"] . "\n";
									// $defaultconfig[$param["@attributes"]["name"]]=$param["@attributes"]["default"];
									$defaultconfig [$param ["name"]] = $param ["default"];
								}
							}
						}
					}
					$changeNeeded = false;
					foreach ( $projects as $proj ) {
						$count_diff = 0;
						$configvalues = array ();
						
						$tblTemplate_Config = JTable::getInstance('TemplateConfig', 'Table');
						$loaded = $tblTemplate_Config->load(array('template' => $template, 'project_id'=>$proj->id));
						if ($loaded) {
							// template present in db for this project
							$string = '';
							$templateTitle = '';
							$params = explode("\n", trim ($tblTemplate_Config->params));
							foreach ( $params as $param ) {
								$row = explode ( "=", $param );
								if (isset ( $row [1] )) {
									list ( $name, $value ) = $row;
									$configvalues [$name] = $value;
								}
							}
							
							foreach ( $defaultconfig as $key => $value ) {
								if (! array_key_exists ( $key, $configvalues )) {
									$count_diff ++;
									$configvalues [$key] = $value;
								}
							}
							
							if ($count_diff || $template == 'ranking' || $template == 'overall' || $template == 'player') {
								foreach ( $configvalues as $key => $value ) {
									if (preg_match ( "/%H:%m/i", $value )) {
										// change text
										$value = 'H:i';
									} else {
										// text ok
									}
									if (preg_match ( "/%A %B %d/i", $value )) {
										// change text
										$value = 'l, d.m.Y';
									} else {
										// text ok
									}
									
									/*
									 * developer: diddipoeler date: change on 13.01.2011 Bugtracker Backend Bug #579 change old textname in newtextname
									 */
									if (preg_match ( "/JL_/i", $value )) {
										// change text
										$value = str_replace ( array_keys ( $convert ), array_values ( $convert ), $value );
									} else {
										// text ok
									}
									
									$value = trim ( $value );
									$string .= "$key=$value\n";
								}
								echo JText::sprintf ( 'Difference found for project [%1$s]', '<b>' . $proj->name . '</b>' ) . ' - ';
								$changeNeeded = true;
								$tblTemplate_Config = JTable::getInstance('TemplateConfig', 'Table');
								$tblTemplate_Config->load(array('template' => $template, 'project_id'=>$proj->id));
								$tblTemplate_Config->title = $out->name [0];
								$tblTemplate_Config->params = $string;
								
								if($tblTemplate_Config->store()) {
									echo '<span style="color:red">';
									echo JText::sprintf ( 'Problems while saving config for [%1$s] with project-ID [%2$s]!', '<b>' . $template . '</b>', '<b>' . $proj->id . '</b>, <b>'. $string .'</b>' );
									echo '</span>' . '<br />';
									echo $db->getErrorMsg () . '<br />';
								} else {
									echo JText::sprintf ( 'Updated template [%1$s] with project-ID [%2$s]', '<span style="color:green;"><b>' . $template . '</b></span>', '<span style="color:green"><b>' . $proj->id . '</b></span>' ) . PrintStepResult ( true ) . '<br />';
								}
							}
						} elseif ($proj->master_template == 0) { // check that project has own templates
						  // or if template not present,create a row with default values
							echo '<br /><span style="color:orange;">' . JText::sprintf ( 'Need to insert into project with project-ID [%1$s] - Project name is [%2$s]', '<b>' . $proj->id . '</b>', '<b>' . $proj->name . '</b>' ) . '</span><br />';
							$changeNeeded = true;
							$temp = trim ( $temp );

							$tblTemplate_Config = JTable::getInstance('TemplateConfig', 'Table');
							$tblTemplate_Config->title = $out->name[0];
							$tblTemplate_Config->params = $temp;
							$tblTemplate_Config->project_id = $proj->id;
							$tblTemplate_Config->template = $template;
								
							// echo error,allows to check if there is a mistake in the template file
							
							if (!$tblTemplate_Config->store()) {
								echo '<span style="color:red; font-weight:bold; ">' . JText::sprintf ( 'Error with [%s]:', $template ) . '</span><br />';
								echo $db->getErrorMsg () . '<br/>';
							} else {
								echo JText::sprintf ( 'Inserted %1$s into project with ID %2$s', '<b>' . $template . '</b>', '<b>' . $proj->id . '</b>, ' . $temp ) . PrintStepResult ( true ) . '<br />';
							}
						}
					}
					if (! $changeNeeded) {
						echo '<span style="color:green">' . JText::_ ( 'No changes needed for this template' ) . '</span>' . '<br />';
					}
				}
			}
		} else {
			echo ' - <span style="color:red">' . JText::_ ( 'No templates found' ) . '</span>';
		}
	} else {
		echo ' - <span style="color:green">' . JText::_ ( 'No update was needed' ) . '</span>';
	}
	
	return '';
}

function tableExists($tableName) {
	$db = JFactory::getDbo ();
	$query = 'SELECT * FROM #__' . $tableName;
	$db->setQuery ( $query );
	$result = $db->query ();
	if ((! $result) || ($db->getNumRows () == 0)) 	// check that table exists...
	{
		echo '<span style="color:red">' . JText::sprintf ( 'Failed checking existance of table [#__%s]', $tableName ) . '</span><br />';
		echo JText::_ ( 'DO NOT WORRY... Surely you make a clean install of JoomLeague 1.5 or the table in your DB was empty!!!' );
		echo '<br /><br />';
		return false;
	}
	return true;
}

function getVersion() {
	$db = JFactory::getDbo ();
	$query = 'SELECT * FROM #__joomleague_version ORDER BY date DESC ';
	$db->setQuery ( $query );
	$result = $db->loadObject ();
	if (! $result) {
		return '';
	}
	return $result->version;
}

function getUpdatePart() {
	$option = 'com_joomleague';
	
	$app = JFactory::getApplication ();
	$update_part = $app->getUserState ( $option . 'update_part' );
	if ($update_part == '') {
		$update_part = 1;
	}
	// eturn 1;
	return $update_part;
}

function setUpdatePart($val = 1) {
	$option = 'com_joomleague';
	$app = JFactory::getApplication ();
	$update_part = $app->getUserState ( $option . 'update_part' );
	if ($val != 0) {
		if ($update_part == '') {
			$update_part = 1; // 1;
		} else {
			$update_part ++;
		}
	} else {
		$update_part = 0;
	}
	$app->setUserState ( $option . 'update_part', $update_part );
}

// ------------------------------------------------------------------------------------------------------------------------

?>
<hr>
<?php
$mtime = microtime ();
$mtime = explode ( " ", $mtime );
$mtime = $mtime [1] + $mtime [0];
$starttime = $mtime;

$totalUpdateParts = 4;
setUpdatePart ();

$output1 = JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE' );

$output2 = '<span style="color:green; ">';
$output2 .= JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_TITLE', $lastVersion, $version, $updateFileDate, $updateFileTime );
$output2 .= '</span>';
JToolBarHelper::title ( $output1 );
echo '<p><h2 style="text-align:center; ">' . $output2 . '</h2></p>';

echo '<p><h3 style="text-align:center; color:red; ">';
echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_VERIFY_TEXT' );
echo '</h3></p>';

echo '<p style="text-align:center; ">' . JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_TOTALSTEPS', '<b>' . $totalUpdateParts . '</b>' ) . '</p>';
echo '<p style="text-align:center; ">' . JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_STEP_OF_STEP', '<b>' . getUpdatePart () . '</b>', '<b>' . $totalUpdateParts . '</b>' ) . '</p>';

if (getUpdatePart () < $totalUpdateParts) {
	// Add here a color transformation for <a> so it is easier to see that a new step has to be confirmed
	echo '<table align="center" width="80%" border="0"><tr><td>';
	$outStr = '<h3 style="text-align:center; ">';
	$outStr .= '<a href="javascript:location.reload(true)" >';
	$outStr .= '<strong>';
	$outStr .= JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_CLICK_HERE', getUpdatePart () + 1, $totalUpdateParts );
	$outStr .= '</strong>';
	$outStr .= '</a>';
	$outStr .= '</h3>';
	if (getUpdatePart () % 2 == 1) {
		echo $outStr . '</td><td>&nbsp;';
	} else {
		echo $outStr;
	}
	echo '</td></tr>';
	echo '</table>';
	
	echo '<p style="text-align:center; ">';
	echo '<b>';
	echo JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_REMEMBER_TOTAL_STEPS_COUNT', $totalUpdateParts );
	echo '</b>';
	echo '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_SCROLL_DOWN' );
	echo '</p>';
	echo '<p style="text-align:center; ">';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_INFO_UNKNOWN_ETC' ) . '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_INFO_JUST_INFOTEXT' ) . '<br />';
	echo '</p>';
}
echo '<hr>';

if (getUpdatePart () == 1) {
	echo '<p>';
	echo '<h3>';
	echo '<span style="color:orange">';
	echo JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_DELETE_WARNING', '</span><b><i><a href="index.php?option=com_user&task=logout">', '</i></b></a><span style="color:orange">' );
	echo '</span>';
	echo '</h3>';
	echo '</p>';
	$JLTablesVersion = getVersion ();
	if (($JLTablesVersion != '') && ($JLTablesVersion < '0.93')) {
		echo '<span style="color:red">';
		echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_ATTENTION' );
		echo '<br /><br />';
		echo JText::_ ( 'You are updating from an older release of JoomLeague than 0.93!' );
		echo '<br />';
		echo JText::sprintf ( 'Actually your JoomLeague-MYSQL-Tables are ready for JoomLeague v%1$s', '<b>' . $JLTablesVersion . '</b>' );
		echo '<br />';
		echo JText::_ ( 'Update may not be completely sucessfull as we require JoomLeague-MYSQL-tables according to the release 0.93!' );
		echo '</span><br />';
		echo '<span style="color:green">';
		echo JText::sprintf ( 'It would be better to update your JoomLeague installation to v0.93 before you update to JoomLeague %1$s!', '<b>' . $version . '</b>' );
		echo '</span><br /><br />';
		echo '<span style="color:red">' . JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_DANGER' ) . '</span><br /><br />';
		echo '<span style="color:red">' . JText::_ ( 'PLEASE use this script ONLY IF you REALLY know what you do!!!' ) . '</span><br />';
	}
}

if (getUpdatePart () == 2) {
	echo UpdateTemplateMasters () . '<br />';
	echo '<hr>';
}
if (getUpdatePart () == 3) {
	require_once (JPATH_COMPONENT.'/models/databasetools.php');	
	JoomleagueModelDatabaseTools::migratePicturePath();
	echo '<hr>';
}

if (getUpdatePart () == 4) {
	echo '<p>';
	echo '<h3>';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_MIGRATE_ASSETS');
	echo '</h3>';
	echo '</p>';
	require_once ('jl_migrateAssets.php');
}

if (getUpdatePart () == $totalUpdateParts) {
	echo '<br />';
	echo '<hr>';
	
	echo '<p><h1 style="text-align:center; color:green; ">';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_CONGRATULATIONS' );
	echo '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_ALL_STEPS_FINISHED' );
	echo '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_USE_NOW' );
	echo '</h1></p>';
	
	setUpdatePart ( 0 );
} else {
	echo '<h3 style="text-align:center; ">';
	echo '<a href="javascript:location.reload(true)">';
	echo '<strong>';
	echo JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_CLICK_HERE', getUpdatePart () + 1, $totalUpdateParts ) . '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_MAY_NEED_TIME' ) . '<br />';
	echo '</strong>';
	echo '</a>';
	echo '</h3>';
	echo '<p style="text-align:center; ">';
	echo JText::sprintf ( 'COM_JOOMLEAGUE_DB_UPDATE_TIME_MEMORY_SET', $maxImportTime, $maxImportMemory ) . '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_INFO_TIMEOUT_ERROR' ) . '<br />';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_INFO_LOCAL_UPDATE' ) . '<br />';
	echo '</p>';
	echo '<h2 style="text-align:center; color:orange; ">';
	echo JText::_ ( 'COM_JOOMLEAGUE_DB_UPDATE_BE_PATIENT' );
	echo '</h2>';
}
if (JComponentHelper::getParams ( 'com_joomleague' )->get ( 'show_debug_info', 0 )) {
	echo '<center><hr>';
	echo JText::sprintf ( 'Memory Limit is %1$s', ini_get ( 'memory_limit' ) ) . '<br />';
	echo JText::sprintf ( 'Memory Peak Usage was %1$s Bytes', number_format ( memory_get_peak_usage ( true ), 0, '', '.' ) ) . '<br />';
	echo JText::sprintf ( 'Time Limit is %1$s seconds', ini_get ( 'max_execution_time' ) ) . '<br />';
	$mtime = microtime ();
	$mtime = explode ( " ", $mtime );
	$mtime = $mtime [1] + $mtime [0];
	$endtime = $mtime;
	$totaltime = ($endtime - $starttime);
	echo JText::sprintf ( 'This page was created in %1$s seconds', $totaltime );
	echo '<hr></center>';
}

?>