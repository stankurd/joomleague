<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

/**
 * Joomleague
 * @subpackage	Module-Ranking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any results returned
$items = count($list['ranking']);
if (!$items) {
   echo '<p class="modjlgranking">' . Text::_('NO ITEMS') . '</p>';
   return;
}

$columns     = explode(',', $params->get('columns', 'JL_PLAYED, JL_POINTS'));
$column_names = explode(',', $params->get('column_names', 'MP, PTS'));

if (count($columns) != count($column_names)) {
	$msg = Text::_('MOD_JOOMLEAGUE_RANKING_COLUMN_NAMES_COUNT_MISMATCH');
	Factory::getApplication()->enqueueMessage($msg, 'error');
	$columns     = array();
	$column_name = array();
}

$nametype = $params->get('nametype', 'short_name');
$colors = $list['colors'];
?>

<div class="modjlgranking">

<?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>

<table class="ranking">
	<thead>
		<tr class="sectiontableheader">
			<?php if($params->get('showRankColumn') == 1) { ?>
			<th class="rank"><?php echo Text::_('MOD_JOOMLEAGUE_RANKING_COLUMN_RANK')?></th>
			<?php } ?>
			<th class="team"><?php echo Text::_('MOD_JOOMLEAGUE_RANKING_COLUMN_TEAM')?></th>
			<?php foreach ($column_names as $col): ?>
			<th class="rankcolval"><?php echo Text::_(trim($col)); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
	<?php
		$k = 0;
		$exParam=explode(':',$params->get('visible_team'));
		$favTeamId=$exParam[0];
		$projfavTeamId = $list['project']->fav_team;
		$favEntireRow = $params->get('fav_team_highlight_type', 0);

		$i = 0;
	?>
	<?php foreach (array_slice($list['ranking'], 0, $params->get('limit', 5)) as $item) :  ?>
		<?php
			$class = $params->get('style_class2', 0);
			if ( $k == 0 ) { $class = $params->get('style_class1', 0); }
			$i++;
			$color = "";
			if ($params->get('show_rank_colors', 0))
			{
			  foreach ($colors as $colorItem) {
			    if ($i >= $colorItem['from'] && $i <= $colorItem['to']) {
						$color = $colorItem['color'];
					}
				}
			}
			if (!$favTeamId) $favTeamId = $projfavTeamId;
			$rowStyle = ' style="';
			$spanStyle = '';
			if ( $item->team->id == $favTeamId)
			{
				if( trim( $list['project']->fav_team_color ) != "" )
				{
					if ($favEntireRow == 1) {
						$color = $list['project']->fav_team_color;
					}
				}
				if ($favEntireRow) {
				  $rowStyle .= ($params->get('fav_team_bold', 0) != 0) ? 'font-weight:bold;' : '';
				  $rowStyle .= ($list['project']->fav_team_text_color != '') ? 'color:' . $list['project']->fav_team_text_color . ';' : '';
				}
				$spanStyle = '<span style="padding:2px;';
				$spanStyle .= ($params->get('fav_team_bold', 0) != 0) ? 'font-weight:bold;' : '';
				$spanStyle .= ($list['project']->fav_team_text_color != '') ? 'color:'.$list['project']->fav_team_text_color.';' : '';
				$spanStyle .= ($list['project']->fav_team_color != '') ? 'background-color:'.$list['project']->fav_team_color.';' : '';
				$spanStyle .= '">';

			}
			$rowStyle .= 'background-color:' . $color . ';';
			$rowStyle .= '"';

		?>
		<tr class="<?php echo $class; ?>">
			<?php if($params->get('showRankColumn') == 1) { ?>
			<td class="rank"<?php if ($color != '') echo $rowStyle; ?>><?php echo $item->rank; ?></td>
			<?php } ?>
			<td class="team"<?php if ($color != '') echo $rowStyle; ?>>
				<?php if ($params->get('show_logo', 0)): ?>
				<?php echo modJLGRankingHelper::getLogo($item, $params->get('show_logo', 0)); ?>
				<?php endif; ?>
				<?php if ($spanStyle != '') echo $spanStyle; ?>
				<?php if ($params->get('teamlink', 'none') != 'none'): ?>
				<?php echo HTMLHelper::link(modJLGRankingHelper::getTeamLink($item, $params, $list['project']), $item->team->$nametype); ?>
				<?php else: ?>
				<?php echo $item->team->$nametype; ?>
				<?php endif; ?>
				<?php if ($spanStyle != '') echo '</span>'; ?>
			</td>
			<?php foreach ($columns as $col): ?>
			<td class="rankcolval"<?php if ($color != '') echo $rowStyle; ?>>
			<?php echo modJLGRankingHelper::getColValue(trim($col), $item); ?>
			</td>
			<?php endforeach; ?>
		</tr>
	<?php $k = 1 - $k; ?>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if ($params->get('show_ranking_link', 1)):?>
<p class="fulltablelink"><?php 
	$divisionid = explode(':', $params->get('division_id', 0));
	$divisionid = $divisionid[0];
	echo HTMLHelper::link(JoomleagueHelperRoute::getRankingRoute($list['project']->slug, null, null, null, null, $divisionid), 
			         Text::_('MOD_JOOMLEAGUE_RANKING_VIEW_FULL_TABLE')); ?></p>
<?php endif; ?>
</div>