<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die; ?>

<?php if ($this->games): ?>
<!-- Playground next games -->
<div id='jlg_plgndnextgames'>
	<h2><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_NEXT_GAMES'); ?></h2>
	<?php if($this->config['show_ical_link']): ?>
	<table width='96%' align='center' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td class='contentheading' style='text-align: right;'>
			<?php
				$link = JoomleagueHelperRoute::getIcalRoute(0,null,$this->playground->id);
				$text = HTMLHelper::image('administrator/components/com_joomleague/assets/images/calendar.png',
					Text::_('COM_JOOMLEAGUE_PLAYGROUND_ICAL_EXPORT'));
				$attribs = array('title' => Text::_('COM_JOOMLEAGUE_PLAYGROUND_ICAL_EXPORT'));
				echo HTMLHelper::_('link',$link,$text,$attribs);
			?>
			</td>
		</tr>
	</table>
	<?php endif; ?>
	<div class='venuecontent map'>
		<table width='96%' align='center' border='0' cellpadding='0' cellspacing='0'>
		<?php
		//sort games by dates
		$gamesByDate = array();
		foreach ($this->games as $game)
		{
			$gameDate = JoomleagueHelper::getMatchDate($game);
			$gamesByDate[$gameDate][] = $game;
		}

		$colspan = 3;
		if ($this->config['show_logo'] == 1)
		{
			$colspan++;
			$colspan++;
		}
		if ($this->config['show_match_number'] == 1)
		{
			$colspan++;
		}
		if ($this->config['show_referee'] == 1)
		{
			$colspan++;
		}

		foreach ($gamesByDate as $date => $games)
		{
			?>
			<tr>
				<?php if ($this->config['show_match_number']): ?>
				<td class='sectiontableheader'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_MATCH_NUMBER'); ?></td>
				<?php endif; ?>
				<td align='left' colspan='<?php echo $colspan; ?>' class='sectiontableheader'>
					<?php echo JoomleagueHelper::getMatchDate($games[0], Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE')); ?>
				</td>
				<?php if ($this->config['show_referee']): ?>
				<td class='sectiontableheader'><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_REFEREE'); ?></td>
				<?php endif; ?>
			</tr>
			<?php
			foreach ($games as $game)
			{
				$home = $this->gamesteams[$game->team1];
				$away = $this->gamesteams[$game->team2];
				?>
			<tr class='sectiontableentry1'>
				<?php if ($this->config['show_match_number'] == 1): ?>
				<td class='nowrap'><?php echo $game->match_number; ?></td>
				<?php endif; ?>
				<td><?php echo JoomleagueHelper::getMatchTime($game); ?></td>
				<td class='nowrap'><?php echo $game->project_name; ?></td>
				<?php if ($this->config['show_logo'] == 1): ?>
				<td align='right' valign='top' class='nowrap'>
					<?php
					$model = $this->getModel();
					$home_logo = $model->getTeamLogo($home->id);
					$away_logo = $model->getTeamLogo($away->id);
					echo JoomleagueModelProject::getClubIconHtml($home_logo[0], 1);
					?>
				</td>
				<?php endif; ?>
				<td class='nowrap'><?php echo $home->name; ?></td>
				<td class='nowrap'>-</td>
				<?php if ($this->config['show_logo'] == 1): ?>
				<td align='right' valign='top' class='nowrap'>
					<?php echo JoomleagueModelProject::getClubIconHtml($away_logo[0], 1); ?>
				</td>
				<?php endif; ?>
				<td class='nowrap'><?php echo $away->name; ?></td>
				<?php
				if ($this->config['show_referee'])
				{
					?>
				<td>
				<?php
				if (isset($game->referees) && count($game->referees) > 0)
				{
					if ($this->project->teams_as_referees)
					{
						$output='';
						$toolTipTitle=Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
						$toolTipText='';

						for ($i = 0; $i < count($game->referees); $i++)
						{
							if ($game->referees[$i]->referee_name != '')
							{
								$output .= $game->referees[$i]->referee_name;
								$toolTipText .= $game->referees[$i]->referee_name.'&lt;br /&gt;';
							}
							else
							{
								$output .= '-';
								$toolTipText .= '-&lt;br /&gt;';
							}
						}
						if ($this->config['show_referee'] == 1)
						{
							echo $output;
						}
						elseif ($this->config['show_referee'] == 2)
						{
							?>
							<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
								<img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
							</span>
							<?php
						}
					}
					else
					{
						$output='';
						$toolTipTitle=Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_TOOLTIP');
						$toolTipText='';

						for ($i = 0; $i < count($game->referees); $i++)
						{
							if ($game->referees[$i]->referee_lastname != '' && $game->referees[$i]->referee_firstname)
							{
								$output .= '<span class="hasTip" title="' . Text::_('COM_JOOMLEAGUE_TEAMPLAN_REF_FUNCTION') .
									'::' . $game->referees[$i]->referee_position_name . '">';
								$ref = $game->referees[$i]->referee_lastname . ','. $game->referees[$i]->referee_firstname;
								$toolTipText .= $ref . ' (' . $game->referees[$i]->referee_position_name . ')' . '&lt;br /&gt;';
								if ($this->config['show_referee_link'])
								{
									$link = JoomleagueHelperRoute::getRefereeRoute($this->project->slug, $game->referees[$i]->referee_id, 3);
									$ref = HTMLHelper::link($link, $ref);
								}
								$output .= $ref;
								$output .= '</span>';

								if (($i + 1) < count($game->referees))
								{
									$output .= ' - ';
								}
							}
							else
							{
								$output .= '-';
							}
						}

						if ($this->config['show_referee'] == 1)
						{
							echo $output;
						}
						elseif ($this->config['show_referee'] == 2)
						{
							?>
							<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
								<img src='<?php echo Uri::root(); ?>media/com_joomleague/jl_images/icon-16-Referees.png' alt='' title='' />
							</span>
							<?php
						}
					}
				}
				else
				{
					echo '-';
				}
				?>
				</td>
			<?php
			}
			?>
			</tr>
			<?php
			}
		}
		?>
		</table>
	</div>
</div>
<!-- End of playground next games -->
<?php endif; ?>