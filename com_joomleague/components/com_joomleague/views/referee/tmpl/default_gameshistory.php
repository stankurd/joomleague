<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<!-- Player stats History START -->
<?php if (count($this->games)): ?>
<h2><?php echo Text::_('COM_JOOMLEAGUE_PERSON_GAMES_HISTORY'); ?></h2>
<table class='table'>
	<tr>
		<td><br />
			<table class='gameshistory'>
				<thead>
					<tr class='sectiontableheader'>
						<th colspan='6'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_GAMES'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$k = 0;
				foreach ($this->games as $game)
				{
					$report_link = JoomleagueHelperRoute::getMatchReportRoute($this->project->slug,$game->id);
					?>
					<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
						<td>
							<?php echo HTMLHelper::link($report_link, JoomleagueHelper::getMatchDate($game, $this->config['games_date_format'])); ?>
						</td>
						<td class='td_r'><?php echo $this->teams[$game->projectteam1_id]->name; ?></td>
						<td class='td_r'><?php echo $game->team1_result; ?></td>
						<td class='td_c'><?php echo $this->overallconfig['seperator']; ?></td>
						<td class='td_l'><?php echo $game->team2_result; ?></td>
						<td class='td_l'><?php echo $this->teams[$game->projectteam2_id]->name; ?></td>
					</tr>
					<?php
					$k = 1 - $k;
				}
				?>
				</tbody>
			</table>
		</td>
	</tr>
</table>
<?php endif; ?>