<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die( 'Restricted access' );

if (	( isset($this->teamPlayer->injury) && $this->teamPlayer->injury > 0 ) ||
		( isset($this->teamPlayer->suspension) && $this->teamPlayer->suspension > 0 ) ||
		( isset($this->teamPlayer->away) && $this->teamPlayer->away > 0 ) )
{
	$today = HTMLHelper::date('now' .' UTC',
						Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
						JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
	?>
	<h2><?php echo Text::_('COM_JOOMLEAGUE_PERSON_STATUS');	?></h2>

	<table class="status">
		<?php
		if ($this->teamPlayer->injury > 0)
		{
			$injury_date = "";
			$injury_end  = "";

			$injury_date = HTMLHelper::date($this->teamPlayer->injury_date .' UTC',
										Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
										JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->rinjury_from))
			$injury_date .= " - ".$this->teamPlayer->rinjury_from;

			//injury end
			$injury_end = HTMLHelper::date($this->teamPlayer->injury_end .' UTC',
										Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
										JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->rinjury_to))
			$injury_end .= " - ".$this->teamPlayer->rinjury_to;

			if ($this->teamPlayer->injury_date == $this->teamPlayer->injury_end)
			{
				?>
				<tr>
					<td class="label">
							<?php
							$imageTitle = Text::_( 'COM_JOOMLEAGUE_PERSON_INJURED' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
																$imageTitle,
																array( 'title' => $imageTitle,
																	   'style' => 'padding-right: 10px; vertical-align: middle;' ) );
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_INJURED' );
							?>
					</td>
					<td class="data">
						<?php
						if ($injury_end != $today)
						{
							echo $injury_end;
						}
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="label" colspan="2">
							<?php
							$imageTitle = Text::_( 'COM_JOOMLEAGUE_PERSON_INJURED' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
					</td>
				</tr>
				<tr>
					<td class="label">
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_INJURY_DATE' );
							?>
					</td>
					<td class="data">
						<?php
						echo $injury_date;
						?>
					</td>
				</tr>
				<?php
				if ($injury_end != $today)
				{
				?>
					<tr>
						<td class="label">
								<?php
								echo Text::_( 'COM_JOOMLEAGUE_PERSON_INJURY_END' );
								?>
						</td>
						<td class="data">
							<?php
								echo $injury_end;
							?>
						</td>
					</tr>
				<?php
				}
			}

			if (!empty($this->teamPlayer->injury_detail))
			{
			?>
			<tr>
				<td class="label">
						<?php
						echo Text::_( 'COM_JOOMLEAGUE_PERSON_INJURY_TYPE' );
						?>
				</td>
				<td class="data">
					<?php
					printf( "%s", htmlspecialchars( $this->teamPlayer->injury_detail ) );
					?>
				</td>
			</tr>
			<?php
			}
		}

		if ($this->teamPlayer->suspension > 0)
		{
			$suspension_date = "";
			$suspension_end  = "";

			//suspension start
			$suspension_date = HTMLHelper::date($this->teamPlayer->suspension_date .' UTC',
											Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
											JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->rsusp_from))
			$suspension_date .= " - ".$this->teamPlayer->rsusp_from;

			$suspension_end = HTMLHelper::date($this->teamPlayer->suspension_end .' UTC',
											Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
											JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->rsusp_to))
			$suspension_end .= " - ".$this->teamPlayer->rsusp_to;


			if ($this->teamPlayer->suspension_date == $this->teamPlayer->suspension_end)
			{
				?>
				<tr>
					<td class="label">
							<?php
							$imageTitle = Text::_( 'Suspended' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
																$imageTitle,
																array( 'title' => $imageTitle,
																	   'style' => 'padding-right: 10px; vertical-align: middle;' ) );
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_SUSPENDED' );
							?>
					</td>
					<td class="data">
						<?php
						if ($suspension_end != $today)
						{
							echo $suspension_end;
						}
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="label" colspan="2">
							<?php
							$imageTitle = Text::_( 'Suspended' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
					</td>
				</tr>
				<tr>
					<td class="label">
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_SUSPENSION_DATE' );
							?>
					</td>
					<td class="data">
						<?php
						echo $suspension_date;
						?>
					</td>
				</tr>
				<?php
				if ($suspension_end != $today)
				{
				?>
					<tr>
						<td class="label">
								<?php
								echo Text::_( 'COM_JOOMLEAGUE_PERSON_SUSPENSION_END' );
								?>
						</td>
						<td class="data">
							<?php
							echo $suspension_end;
							?>
						</td>
					</tr>
				<?php
				}
			}

			if (!empty($this->teamPlayer->suspension_detail))
			{
			?>
				<tr>
					<td class="label">
						<b>
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_SUSPENSION_REASON' );
							?>
						</b>
					</td>
					<td class="data">
						<?php
						printf( "%s", htmlspecialchars( $this->teamPlayer->suspension_detail ) );
						?>
					</td>
				</tr>
			<?php
			}
		}

		if ($this->teamPlayer->away > 0)
		{
			$away_date = "";
			$away_end  = "";

			//suspension start
			$away_date = HTMLHelper::date($this->teamPlayer->away_date .' UTC',
										Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
										JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->raway_from))
			$away_date .= " - ".$this->teamPlayer->raway_from;

			$away_end = HTMLHelper::date($this->teamPlayer->away_end .' UTC',
									Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
									JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
			if(isset($this->teamPlayer->raway_to))
			$away_end .= " - ".$this->teamPlayer->raway_to;

			if ($this->teamPlayer->away_date == $this->teamPlayer->away_end)
			{
				?>
				<tr>
					<td class="label">
							<?php
							$imageTitle = Text::_( 'Away' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image('images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
																$imageTitle,
																array( 'title' => $imageTitle,
																	   'style' => 'padding-right: 10px; vertical-align: middle;' ) );
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_AWAY' );
							?>
					</td>
					<td class="data">
						<?php
						if ($away_end != $today)
						{
							echo $away_end;
						}
						?>
					</td>
				</tr>
				<?php
			}
			else
			{
				?>
				<tr>
					<td class="label" colspan="2">
							<?php
							$imageTitle = Text::_( 'Away' );
							echo "&nbsp;&nbsp;" . HTMLHelper::image(	'images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
																$imageTitle,
																array( 'title' => $imageTitle ) );
							?>
					</td>
				</tr>
				<tr>
					<td class="label">
						<b>
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_AWAY_DATE' );
							?>
						</b>
					</td>
					<td class="data">
						<?php
						echo $away_date;
						?>
					</td>
				</tr>
				<?php
				if ($away_end != $today)
				{
				?>
				<tr>
					<td class="label">
							<?php
							echo Text::_( 'COM_JOOMLEAGUE_PERSON_AWAY_END' );
							?>
					</td>
					<td class="data">
						<?php
						echo $away_end;
						?>
					</td>
				</tr>
				<?php
				}
			}


			if (!empty($this->teamPlayer->away_detail))
			{
			?>
			<tr>
				<td class="label">
						<?php
						echo Text::_( 'COM_JOOMLEAGUE_PERSON_AWAY_REASON' );
						?>
				</td>
				<td class="data">
					<?php
					printf( "%s", htmlspecialchars( $this->teamPlayer->away_detail ) );
					?>
				</td>
			</tr>
			<?php
			}
		}
		?>
	</table>

	<?php
}
?>