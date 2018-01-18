<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>
<!-- START of match events -->

<h2><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_EVENTS'); ?></h2>		

<table class="matchreport" border="0">
			<?php
			foreach ( $this->eventtypes as $event )
			{
				?>
				<tr>
					<td colspan="2" class="eventid">
						<?php echo HTMLHelper::image($event->icon, Text::_($event->icon ), NULL ) . Text::_($event->name); ?>
					</td>
				</tr>
				<tr>
					<td class="list">
						<dl>
							<?php echo $this->showEvents( $event->id, $this->match->projectteam1_id ); ?>
						</dl>
					</td>
					<td class="list">
						<dl>
							<?php echo $this->showEvents( $event->id, $this->match->projectteam2_id ); ?>
						</dl>
					</td>
				</tr>
				<?php
			}
			?>
</table>
<!-- END of match events -->
<br />
