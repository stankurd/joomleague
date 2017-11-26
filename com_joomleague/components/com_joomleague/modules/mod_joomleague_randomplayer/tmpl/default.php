<?php
/**
 * Joomleague
 * @subpackage	Module-Randomplayer
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any player returned
$items = count($list['player']);
if (!$items) {
	echo '<p class="modjlgrandomplayer">' . JText::_('MOD_JOOMLEAGUE_RANDOMPLAYER_NOITEMS') . '</p>';
	return;
}?>

<div class="modjlgrandomplayer">
<ul>
<?php if ($params->get('show_project_name')):?>
<li class="projectname"><?php echo $list['project']->name; ?></li>
<?php endif; ?> <?php
$person=$list['player'];
$link = JoomleagueHelperRoute::getPlayerRoute( $list['project']->slug, 
												$list['infoteam']->team_id, 
												$person->slug );
?>

<li class="modjlgrandomplayer"><?php
$picturetext=JText::_( 'JL_PERSON_PICTURE' );
$text = JoomleagueHelper::formatName(null, $person->firstname, 
												$person->nickname, 
												$person->lastname, 
												$params->get("name_format"));
	
$imgTitle = JText::sprintf( $picturetext .' %1$s', $text);
if(isset($list['inprojectinfo']->picture)) {
	$picture = $list['inprojectinfo']->picture;
	$pic = JoomleagueHelper::getPictureThumb($picture, $imgTitle, $params->get('picture_width'), $params->get('picture_heigth'));
	echo '<a href="'.$link.'">'.$pic.'</a>' ;
}
?></li>
<li class="playerlink">
<?php 
	if($params->get('show_player_flag')) {
		echo Countries::getCountryFlag($person->country)." ";
	}
	if ($params->get('show_player_link'))
	{
		$link = JoomleagueHelperRoute::getPlayerRoute($list['project']->slug, 
														$list['infoteam']->team_id, 
														$person->slug );
		echo JHtml::link($link, $text);
	}
	else
	{
		echo JText::sprintf( '%1$s', $text);
	}
?>
</li>
<?php if ($params->get('show_team_name')):?>
<li class="teamname">
<?php 
	echo JoomleagueHelper::getPictureThumb($list['infoteam']->team_picture,
											$list['infoteam']->name,
											$params->get('team_picture_width',21),
											$params->get('team_picture_height',0),
											1)." ";
	$text = $list['infoteam']->name;
	if ($params->get('show_team_link'))
	{
		$link = JoomleagueHelperRoute::getTeamInfoRoute($list['project']->slug, 
														$list['infoteam']->team_id);
		echo JHtml::link($link, $text);
	}
	else
	{
		echo JText::sprintf( '%1$s', $text);
	}
?>
</li>
<?php endif; ?>
<?php if ($params->get('show_position_name') && isset($list['inprojectinfo']->position_name)):?>
<li class="positionname"><?php 
	$positionName = $list['inprojectinfo']->position_name;
	echo JText::_($positionName);?>
</li>
<?php endif; ?>
</ul>
</div>
