<?php defined('_JEXEC') or die; ?>
<div class='no-column'>
	<div class='contentpaneopen'>
		<div class='contentheading'>
			<?php echo JText::_('COM_JOOMLEAGUE_CLUBINFO_TEAMS'); ?>
		</div>
	</div>
	<div class='left-column-teamlist'>
	<?php
		foreach ($this->teams as $team)
		{
			if ($team->team_name)
			{
				$link = JoomleagueHelperRoute::getTeamInfoRoute($team->pid, $team->id);
				?>
				<span class='clubinfo_team_item'>
					<?php
						echo JHtml::link($link, $team->team_name);
						echo '&nbsp;';
						if ($team->team_shortcut)
						{
							echo '(' . $team->team_shortcut . ')';
						}
					?>
				</span>
				<span class='clubinfo_team_value'>
					<?php echo $team->team_description ? $team->team_description : '&nbsp;'; ?>
				</span>
				<?php
			}
		}
	?>
	</div>
</div>