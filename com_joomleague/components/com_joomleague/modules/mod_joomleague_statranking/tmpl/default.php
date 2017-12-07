<?php
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 * @subpackage	Module-Statranking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>

<div class="modjlgstat">
<?php
$header = ""; 
if ($params->get('show_project_name', 0))
{
	$header .= $list['project']->name;
}
if ($params->get('show_division_name', 0))
{
	$division = $list['model']->getDivision();
	if (property_exists($division->name) && length($division->name) > 0)
	{
		if (length($header) > 0)
		{
			$header .= " - ";
		}
		$header .= $list['model']->getDivision()->name;
	}
}
$showPicture = $params->get('show_picture', 0);
$pictureHeight = $params->get('picture_height', 40);
$pictureWidth = $params->get('picture_width', 40);
$showTeam = $params->get('show_team', 1);
$showLogo = $params->get('show_logo', 0);
$teamLink = $params->get('teamlink', '');
$teamnametype = $params->get('teamnametype', 'short_name');
?>
<p class="projectname"><?php echo $header; ?></p>
<?php
if (count($list['stattypes']) > 0)
{
?>
<table class="statsrankinglist">
	<tbody>
	<?php
	foreach ($list['stattypes'] as $stattype)
	{
		if (array_key_exists($stattype->id, $list['ranking']))
		{
			$rankingforstat = $list['ranking'][$stattype->id]->ranking;
			?>
		<tr class="sectiontableheader">
			<td class="stattype"><?php echo JText::_($stattype->name); ?></td>
		</tr>
		<tr>
			<td>
			<?php
			if (count($rankingforstat) > 0)
			{
				?>
				<table class="statranking">
					<thead>
						<tr class="sectiontableheader">
							<th class="rank"><?php echo JText::_('MOD_JOOMLEAGUE_STATRANKING_COL_RANK')?></th>
							<?php if ($showPicture == 1) : ?>
							<th class="picture"><?php echo JText::_('MOD_JOOMLEAGUE_STATRANKING_COL_PIC');?></th>
							<?php endif; ?>
							<th class="personname"><?php echo JText::_('MOD_JOOMLEAGUE_STATRANKING_COL_TEAM')?></th>
							<?php if ($showTeam == 1) : ?>
							<th class="team"><?php echo JText::_('MOD_JOOMLEAGUE_STATRANKING_COL_NAME');?></th>
							<?php endif; ?>
							<th class="td_c">
							<?php if ($params->get('show_event_icon', 1)) : ?>
								<?php echo modJLGStatHelper::getStatIcon($stattype); ?>
							<?php else : ?>
								<?php echo JText::_($stattype->name); ?>
							<?php endif; ?>
							</th>
						</tr>
					</thead>
					<tbody>
				<?php 
				$lastRank = 0;
				$k = 0;
				foreach (array_slice($rankingforstat, 0, $params->get('limit', 5)) as $item)
				{
					$team = $list['teams'][$item->team_id];
					$style_class = ( $k == 0 ) ? 'style_class1' : 'style_class2';
					$class = $params->get($style_class, 0);
					?>
						<tr class="<?php echo $class; ?>">
							<td class="rank">
					<?php
					$rank = ($item->rank == $lastRank) ? "-" : $item->rank;
					$lastRank = $item->rank;
					echo $rank;
					?>
							</td>
					<?php
					if ($showPicture == 1)
					{
						$picture = isset($item->teamplayerpic) ? $item->teamplayerpic : null;
						if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ))
						{
							$picture = $item->picture;
						}
						if ( !file_exists( $picture ) )
						{
							$picture = JoomleagueHelper::getDefaultPlaceholder("player");
						}			
						$name = JoomleagueHelper::formatName(null, $item->firstname, $item->nickname, $item->lastname, $params->get("name_format"));
						?>
						<td class="picture">
							<?php echo JoomleagueHelper::getPictureThumb($picture, $name, $pictureHeight, $pictureWidth).'&nbsp;';?>
						</td>
						<?php
					}
					?>
						<td class="personname">
							<?php echo modJLGStatHelper::printName($item, $team, $params, $list['project']); ?>
						</td>
					<?php
					if ($showTeam == 1)
					{
						?>
						<td class="team">
						<?php
						if ($showLogo > 0)
						{
							echo modJLGStatHelper::getLogo($team, $showLogo);
						}
						if ($teamLink)
						{
							echo HTMLHelper::link(modJLGStatHelper::getTeamLink($team, $params, $list['project']), $team->$teamnametype);
						}
						else
						{
							echo $team->$teamnametype;
						}
						?>
						</td>
						<?php
					}
					?>
						<td class="td_c"><?php echo $item->total; ?></td>
					</tr>
					<?php
					$k=(1-$k);
				}
				?>
				</tbody>
			</table>
				<?php
			}
			else
			{
				?>
				<p class="modjlgstat"><?php echo JText::_('MOD_JOOMLEAGUE_STATRANKING_NOITEMS');?></p>
				<?php
			}
			?>
			</td>
		</tr>
		<?php
		}
	}
	?>
	</tbody>
</table>
<?php
}
else
{
?>
<p class="modjlgstat"><?php echo JText::_("MOD_JOOMLEAGUE_STATRANKING_NOEVENTSSELECTED"); ?></p>
<?php
}
?>
<?php if ($params->get('show_full_link', 1)):?>
<p class="fulltablelink">
<?php 
//	$divisionid = explode(':', $params->get('division_id', 0));
//	$divisionid = $divisionid[0];
//	$teamid = (int)$params->get('tid', 0);
	echo HTMLHelper::link(JoomleagueHelperRoute::getStatsRankingRoute($list['project']->slug, $params->get('divisionid',0), $params->get('tid',0), $params->get('sid',0), $params->get('ranking_order')), JText::_('VIEW FULL TABLE')); ?>
</p>
<?php endif; ?>
</div>
