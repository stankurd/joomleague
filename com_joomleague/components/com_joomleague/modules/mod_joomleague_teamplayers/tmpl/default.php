<?php
/**
 * Joomleague
 * @subpackage	Module-Teamplayers
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any players returned
$itemscount = count($list['roster']);

if (!$itemscount) {
	echo '<p class="modjlgteamplayers">' . JText::_('MOD_JOOMLEAGUE_TEAMPLAYERS_NOITEMS') . '</p>';
	return;
}?>

<div class="modjlgteamplayers"><?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>


<ul class="modjlgposition">
<h1>
<?php if ($params->get('show_team_name', 0)):?>
	<?php echo $list['project']->team_name; ?>
<?php endif; ?></div>
</h1>
<?php foreach (array_slice($list['roster'], 0, $params->get('limit', 24)) as $items) :  ?>
	<li>
		<ul class="modjlgplayer">
		<?php foreach (array_slice($items, 0, $params->get('limit', 24)) as $item) : ?>
			<li><?php 
			echo modJLGTeamPlayersHelper::getPlayerLink($item, $params);
			?></li>
			<?php	endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
</ul>
</div>