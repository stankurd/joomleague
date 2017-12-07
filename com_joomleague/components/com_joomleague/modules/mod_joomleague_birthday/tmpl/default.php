<?php
/**
 * Joomleague
 * @subpackage	Module-Birthday
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die; 


// Prevent that result is null when either $players or $crew is null by casting each to an array.
$persons = array_merge((array)$players, (array)$crew);
if(count($persons)>1)   $persons = jl_birthday_sort($persons, array("n+days_to_birthday", "n".$params->get('sort_order')."age"), false);

$k=0;
$counter=0;
?>
<table class="birthday">
<?php
if(count($persons) > 0) {
	foreach ($persons AS $person) {
		if (($params->get('limit')> 0) && ($counter == intval($params->get('limit')))) break;
		$class = ($k == 0)? $params->get('sectiontableentry1') : $params->get('sectiontableentry2');

		$thispic = "";
		$flag = $params->get('show_player_flag')? Countries::getCountryFlag($person['country']) . "&nbsp;" : "";
		$text = htmlspecialchars(JoomleagueHelper::formatName(null, $person['firstname'], 
													$person['nickname'], 
													$person['lastname'], 
													$params->get("name_format")), ENT_QUOTES, 'UTF-8');
		$usedname = $flag.$text;
		
		$person_link = "";
		$person_type = $person['type'];
		if($person_type==1) {
			$person_link = JoomleagueHelperRoute::getPlayerRoute($person['project_id'],
																$person['team_id'],
																$person['id']);
		} else if($person_type==2) {
			$person_link = JoomleagueHelperRoute::getStaffRoute($person['project_id'],
																$person['team_id'],
																$person['id']);
		} else if($person_type==3) {
			$person_link = JoomleagueHelperRoute::getRefereeRoute($person['project_id'],
																$person['team_id'],
																$person['id']);
		}
		$showname = HTMLHelper::link( $person_link, $usedname );
		?>
	<tr class="<?php echo $params->get('heading_style');?>">
		<td class="birthday"><?php echo $showname;?></td>
	</tr>
	<tr class="<?php echo $class;?>">
		<td class="birthday">
		<?php
		if ($params->get('show_picture')==1) {
			if (file_exists(JPATH_BASE.'/'.$person['picture'])&&$person['picture']!='') {
				$thispic = $person['picture'];
			}
			elseif (file_exists(JPATH_BASE.'/'.$person['default_picture'])&&$person['default_picture']!='') {
				$thispic = $person['default_picture'];
			}
			echo '<img src="'.Uri::base().$thispic.'" alt="'.$text.'" title="'.$text.'"';
			if ($params->get('picture_width') != '') echo ' width="'.$params->get('picture_width').'"';
			echo ' /><br />';

		}
		switch ($person['days_to_birthday']) {
			case 0: $whenmessage = JText::_($params->get('todaymessage'));break;
			case 1: $whenmessage = JText::_($params->get('tomorrowmessage'));break;
			default: $whenmessage = str_replace('%DAYS_TO%', $person['days_to_birthday'], trim(JText::_($params->get('futuremessage'))));break;
		}
		$birthdaytext = htmlentities(trim(JText::_($params->get('birthdaytext'))), ENT_COMPAT , 'UTF-8');
		$dayformat = htmlentities(trim($params->get('dayformat')));
		$birthdayformat = htmlentities(trim($params->get('birthdayformat')));
		$birthdaytext = str_replace('%WHEN%', $whenmessage, $birthdaytext);
		$birthdaytext = str_replace('%AGE%', $person['age'], $birthdaytext);
		$birthdaytext = str_replace('%DATE%', HTMLHelper::_('date', $person['year'].'-'.$person['daymonth'], $dayformat, $params->get('time_zone')), $birthdaytext);
		$birthdaytext = str_replace('%DATE_OF_BIRTH%', HTMLHelper::_('date', $person['date_of_birth'], $birthdayformat, $params->get('time_zone')), $birthdaytext);
		$birthdaytext = str_replace('%BR%', '<br />', $birthdaytext);
		$birthdaytext = str_replace('%BOLD%', '<b>', $birthdaytext);
		$birthdaytext = str_replace('%BOLDEND%', '</b>', $birthdaytext);
			
		echo $birthdaytext;
		?></td>
	</tr>
	<?php
	$k = 1 - $k;
	$counter++;
	}
}
else {
?>
<tr>
	<td class="birthday"><?php echo''.str_replace('%DAYS%', $params->get('maxdays'), htmlentities(trim($params->get('not_found_text')))).''; ?></td>
</tr>
<?php } ?>
</table>




