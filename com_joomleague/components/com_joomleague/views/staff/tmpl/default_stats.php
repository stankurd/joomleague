<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<!-- Player stats History START -->
<h2><?php	echo Text::_( 'COM_JOOMLEAGUE_PERSON_PERSONAL_STATISTICS' ); ?></h2>
<table width='96%' align='center' border='0' cellpadding='0' cellspacing='0'>
	<tr>
		<td>
			<br/>
			<table id='stats_history' width='96%' align='center' cellspacing='0' cellpadding='0' border='0'>
				<tr class='sectiontableheader'>
					<th class='td_l' class='nowrap'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_COMPETITION'); ?></th>
					<th class='td_l' class='nowrap'><?php echo Text::_('COM_JOOMLEAGUE_PERSON_TEAM'); ?></th>
					<th class='td_c'>
						<?php echo $this->getEventIconHtml('played.png', 'COM_JOOMLEAGUE_PERSON_PLAYED',
							array(' title' => Text::_('COM_JOOMLEAGUE_PERSON_PLAYED'), ' width' => 20, ' height' => 20)); ?>
					</th>
					<?php if ($this->config['show_careerstats'] && !empty($this->stats)):
						foreach ($this->stats as $stat):
							if (!empty($stat)): ?>
					<th class='td_c'><?php echo $stat->getImage(); ?></th>
							<?php
							endif;
						endforeach;
					endif; ?>
				</tr>
				<?php
				$k = 0;
				$career = array();
				$career['played'] = 0;
				if (count($this->history) > 0)
				{
					foreach ($this->history as $player_hist)
					{
						$model = $this->getModel();
						$present = $model->getPresenceStats($player_hist->project_id, $player_hist->ptid, $player_hist->pid);
						?>
						<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
							<td class='td_l' nowrap='nowrap'><?php echo $player_hist->project_name; ?></td>
							<td class='td_l' class='nowrap'><?php echo $player_hist->team_name; ?></td>
							<!-- Player stats History - played start -->
							<td class='td_c'><?php $career['played'] += $present;
								echo ($present > 0) ? $present : '-'; ?>
							</td>
							<!-- Player stats History - allevents start -->
							<?php if ($this->config['show_careerstats'] && !empty($this->staffStats)):
								foreach ($this->stats as $stat):
									if (!empty($stat)): ?>
							<td class='td_c'>
								<?php echo isset($this->staffStats[$stat->id][$player_hist->project_id])
									? $this->staffStats[$stat->id][$player_hist->project_id]
									: '-'; ?>
							</td>
									<?php
									endif;
								endforeach;
							endif; ?>
							<!-- Player stats History - allevents end -->
						</tr>
						<?php
						$k = 1 - $k;
					}
				}
				?>
				<tr class='career_stats_total'>
					<td class='td_r' colspan='2'><b><?php echo Text::_('COM_JOOMLEAGUE_PERSON_CAREER_TOTAL'); ?></b></td>
					<td class='td_c'><?php echo ($career['played'] > 0) ? $career['played'] : '-'; ?></td>
					<?php // stats per project
					if ($this->config['show_careerstats'] && !empty($this->careerStats)):
						foreach ($this->stats as $stat):
							if (!empty($stat)): ?>
					<td class='td_c'>
						<?php echo isset($this->careerStats[$stat->id]) ? $this->careerStats[$stat->id] : '-'; ?>
					</td>
							<?php
							endif;
						endforeach;
					endif; ?>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!-- staff stats History END -->
