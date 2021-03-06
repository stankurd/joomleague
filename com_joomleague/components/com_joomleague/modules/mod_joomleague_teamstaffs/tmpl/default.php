<?php
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 * @subpackage	Module-Teamstaffs
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any players returned
$itemscount = count($list['staffs']);
if (!$itemscount) {
	echo '<p class="modjlgteamplayers">' . Text::_('MOD_JOOMLEAGUE_TEAMSTAFFS_NOITEMS') . '</p>';
	return;
}?>

<div class="modjlgteamstaffs"><?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>


<ul class="modjlgposition">
<h1>
<?php if ($params->get('show_team_name', 0)):?>
	<?php echo $list['project']->team_name; ?>
<?php endif; ?></div>
</h1>
<?php foreach (array_slice($list['staffs'], 0, $params->get('limit', 24)) as $items) :  ?>
	<li>
		<ul class="modjlgstaff">
		<?php foreach (array_slice($items, 0, $params->get('limit', 24)) as $item) : ?>
			<li><?php 
			echo modJLGTeamStaffsHelper::getStaffLink($item, $params);
			?></li>
			<?php	endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
</ul>
