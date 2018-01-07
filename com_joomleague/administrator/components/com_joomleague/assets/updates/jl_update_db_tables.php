<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Kurt Norgaz
 * @author		And_One <andone@mfga.at>
 * 
 * script file to CREATE/UPDATE all tables of JoomLeague
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$version			= '3.0.22.57ae969-b';
$updateFileDate		= '2012-09-13';
$updateFileTime		= '00:05';
$updateDescription	='<span style="color:orange">Update all tables using the current install sql-file.</span>';
$excludeFile		='false';

$maxImportTime=ComponentHelper::getParams('com_joomleague')->get('max_import_time',0);
if (empty($maxImportTime))
{
	$maxImportTime=880;
}
if ((int)ini_get('max_execution_time') < $maxImportTime){@set_time_limit($maxImportTime);}

$maxImportMemory=ComponentHelper::getParams('com_joomleague')->get('max_import_memory',0);
if (empty($maxImportMemory))
{
	$maxImportMemory='150M';
}
if ((int)ini_get('memory_limit') < (int)$maxImportMemory){ini_set('memory_limit',$maxImportMemory);}

function getUpdatePart()
{
	$app = Factory::getApplication();
	$option = $app->input->get('option');
	$update_part=$app->getUserState($option.'update_part');
	return $update_part;
}

function setUpdatePart($val=1)
{
	$app = Factory::getApplication();
	$option = $app->input->get('option');
	$update_part=$app->getUserState($option.'update_part');
	if ($val!=0)
	{
		if ($update_part=='')
		{
			$update_part=1;
		}
		else
		{
			$update_part++;
		}
	}
	else
	{
		$update_part=0;
	}
	$app->setUserState($option.'update_part',$update_part);
}

?>
<hr />
<?php
	$mtime=microtime();
	$mtime=explode(" ",$mtime);
	$mtime=$mtime[1] + $mtime[0];
	$starttime=$mtime;

	JLToolBarHelper::title(Text::_('JoomLeague - Database update process'));
	echo '<h2>'.Text::sprintf(	'JoomLeague v%1$s - %2$s - Filedate: %3$s / %4$s',
								$version,$updateDescription,$updateFileDate,$updateFileTime).'</h2>';
	$totalUpdateParts = 2;
	setUpdatePart();

	if (getUpdatePart() < $totalUpdateParts)
	{
		echo '<p><b>';
		echo Text::sprintf('Please remember that this update routine has totally %1$s update steps!',$totalUpdateParts).'</b><br />';
		echo Text::_('So please go to the bottom of this page to check if there are errors and more update steps to do!');
		echo '</p>';
		echo '<p style="color:red; font-weight:bold; ">';
		echo Text::_('We recommend a database backup before the update!!!').'<br />';
		echo '</p>';
		echo '<hr>';
	}

	if (getUpdatePart()==$totalUpdateParts)
	{
		echo '<hr />';
		require_once JPATH_ADMINISTRATOR.'/components/com_joomleague/models/databasetools.php';
		echo JoomleagueModelDatabaseTools::ImportTables();
		echo '<br /><center><hr />';
			echo Text::sprintf('Memory Limit is %1$s',ini_get('memory_limit')).'<br />';
			echo Text::sprintf('Memory Peak Usage was %1$s Bytes',number_format(memory_get_peak_usage(true),0,'','.')).'<br />';
			echo Text::sprintf('Time Limit is %1$s seconds',ini_get('max_execution_time')).'<br />';
			$mtime=microtime();
			$mtime=explode(" ",$mtime);
			$mtime=$mtime[1] + $mtime[0];
			$endtime=$mtime;
			$totaltime=($endtime - $starttime);
			echo Text::sprintf('This page was created in %1$s seconds',$totaltime);
		echo '<hr /></center>';
		setUpdatePart(0);
	}
	else
	{
		echo '<input type="button" onclick="document.body.innerHTML=\'please wait...\';location.reload(true)" value="';
		echo Text::sprintf('Click here to do step %1$s of %2$s steps to finish the update.',getUpdatePart()+1,$totalUpdateParts);
		echo '" />';
	}
?>
