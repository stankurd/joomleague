<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (count($this->career) > 0): ?>
<h2><?php echo Text::_($this->careerTitle); ?></h2>
<table class='table'>
	<tr>
		<td>
			<table class='gameshistory'>
				<tr class='sectiontableheader'>
					<th class='td_l'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_COMPETITION'); ?></th>
					<th class='td_l'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_SEASON'); ?></th>
					<?php if (isset($this->career[0]->team_name)): ?>
					<th class='td_l'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_TEAM'); ?></th>
					<?php endif; ?>
					<th class='td_l'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_POSITION'); ?></th>
				</tr>
				<?php
				$k = 0;
				foreach ($this->career AS $job): ?>
				<tr class="<?php echo $k == 0 ? $this->config['style_class1'] : $this->config['style_class2']; ?>">
					<td class='td_l'><?php echo $job->project_link; ?></td>
					<td class='td_l'><?php echo $job->season_name; ?></td>
					<?php if (isset($job->team_link)): ?>
					<th class='td_l'><?php echo $job->team_link; ?></th>
					<?php endif; ?>
					<td class='td_l'><?php echo $job->position_name ? Text::_($job->position_name) : ''; ?></td>
				</tr>
					<?php
					$k = 1 - $k;
				endforeach; ?>
			</table>
		</td>
	</tr>
</table>
<?php endif; ?>