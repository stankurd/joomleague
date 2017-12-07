<?php
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 * @subpackage	Module-TeamstatsRanking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any results returned
$items = count($list['ranking']);
if (!$items) {
   echo '<p class="modjlgteamstat">' . JText::_('MOD_JOOMLEAGUE_TEAMSTATS_RANKING_NOITEMS') . '</p>';
   return;
}

$teamnametype = $params->get('teamnametype', 'short_name');
?>

<div class="modjlgteamstat">

<?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>

<table class="statranking">
	<thead>
		<tr class="sectiontableheader">
			<th class="rank"><?php echo JText::_('MOD_JOOMLEAGUE_TEAMSTATS_RANKING_COL_RANK')?></th>
			<th class="teamlogo"></th>
			<th class="team"><?php echo JText::_('MOD_JOOMLEAGUE_TEAMSTATS_RANKING_COL_TEAM')?></th>
			<th class="td_c">
			<?php
			if ($params->get('show_event_icon', 1))
			{
				echo modJLGTeamStatHelper::getStatIcon($list['stat']);
			}
			else
			{
				echo JText::_($list['stat']->name);
			}
			?>
			</th>
		</tr>
	</thead>
	<tbody>
	<?php 
	$lastRank = 0;
	$k = 0;
	foreach (array_slice($list['ranking'], 0, $params->get('limit', 5)) as $item) :  ?>
		<?php $team = $list['teams'][$item->team_id]; ?>
		<?php
			$class = $params->get('style_class2', 0);;
			if ( $k == 0 ) { $class = $params->get('style_class1', 0);}
		?>	
		<tr class="<?php echo $class; ?>">
			<td class="rank">
			<?php 
				$rank = ($item->rank == $lastRank) ? "-" : $item->rank;
				$lastRank = $item->rank;
				echo $rank; 
			?>
			</td>
			<td class="teamlogo">
				<?php if ($params->get('show_logo', 0)): ?>
				<?php echo modJLGTeamStatHelper::getLogo($team, $params->get('show_logo', 0)); ?>
				<?php endif; ?>			
			</td>
			<td class="team">
				<?php if ($params->get('teamlink', '')): ?>
				<?php echo HTMLHelper::link(modJLGTeamStatHelper::getTeamLink($team, $params, $list['project']), $team->$teamnametype); ?>
				<?php else: ?>
				<?php echo $team->$nametype; ?>
				<?php endif; ?>
			</td>
			<td class="td_c"><?php echo $item->total; ?></td>
		</tr>
	<?php $k=(1-$k); ?>	
	<?php endforeach; ?>
	</tbody>
</table>

</div>