<?php
/**
 * Joomleague Component script file to DELETE all tables of JoomLeague Prediction
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.5
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

$version		='1.5.5';
$updatefilename	= 'pred_delete_prediction_tables';
$updateFileDate	='2012-03-13';
$updateFileTime	='10:00';
$updateDescription ='<span style="color:red">DANGER!!! DELETES ALL JoomLeague Prediction tables inside your database without warning!!! Only if you really know what you do!!!</span>';

function DeleteTables()
{
	$db =& Factory::getDBO();
	echo '<p><strong>'.Text::_('DELETING ALL tables of JoomLeague Prediction to clean your database!').'</strong></p>';

	$query="SHOW TABLES LIKE '%_joomleague_prediction%'"; $db->setQuery($query); $results=$db->loadResultArray();
	foreach ($results as $result)
	{
		echo $result.' - <span style="color:';
		$query='DROP TABLE `'.$result.'`'; $db->setQuery($query);
		if ($db->query()) { echo 'green">'.Text::_('Success'); } else { echo 'red">'.Text::_('Failed'); }
		echo '</span><br />';
	}

	return '';
}

function getUpdatePart()
{
	$option='com_joomleague';
	$app = Factory::getApplication();
	$update_part=$app->getUserState($option.'update_part');
	#return 1;
	return $update_part;
}

function setUpdatePart($val=1)
{
	$option='com_joomleague';
	$app =& Factory::getApplication();
	$update_part=$app->getUserState($option.'update_part');
	if ($val != 0)
	{
		if ($update_part == '')
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
<hr>
<?php
	$output=Text::sprintf('JoomLeague v%1$s - %2$s - Filedate: %3$s / %4$s',$version,$updateDescription,$updateFileDate,$updateFileTime);
	JLToolBarHelper::title($output);

	$totalUpdateParts=2; //2
	setUpdatePart();

	if (getUpdatePart() < $totalUpdateParts)
	{
		echo '<p><b>';
			echo Text::sprintf('Please remember that this update routine has totally %1$s update steps at all!',$totalUpdateParts).'</b><br />';
			echo Text::_('So please go to the bottom of this page to check if there are errors and more update steps to do!');
		echo '</p>';
		echo '<p>';
			echo Text::_('<span style="color:red">DANGER!!!</span>') .'<br />';
			echo Text::_('<span style="color:red">This script DELETES ALL JoomLeague Prediction tables inside your database without warning!!!</span>') .'<br />';
			echo Text::_('<span style="color:red">Only if you really know what you do!!!</span>') .'<br />';
		echo '</p>';
		echo '<hr>';
	}

	if (getUpdatePart()==2)
	{
		echo DeleteTables().'<br />';
	}

	if (getUpdatePart()==$totalUpdateParts)
	{
		echo Text::_('Deleting of selected tables is done now. Script is finished!').'<br />';
		echo '<br />';
		echo '<hr>';
		setUpdatePart(0);
	}
	else
	{
		echo '<a href="javascript:location.reload(true)" ><b>';
			echo Text::sprintf('Click here to call step %1$s of %2$s steps to finish the update. Please really think about what you do by clicking here!',getUpdatePart()+1,$totalUpdateParts);
		echo '</b></a>';
	}
?>