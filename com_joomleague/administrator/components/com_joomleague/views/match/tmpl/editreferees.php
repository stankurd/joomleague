<?php

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;


$app = Factory::getApplication();
$input = $app->input;

?>
<script>
jQuery(document).ready(function($) {
	jQuery('#multiselect').multiselect({
		sort: false
	});
});
</script>
	
<div id="lineup">
	<form id="adminForm" name="adminForm" method="post">
		<div class="clear"></div>
		<fieldset class="adminform">
			<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ER_DESCR'); ?></legend>
			<table class='adminlist'>
				<thead>
					<tr>
						<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ER_REFS'); ?>
					</th>
						<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_ER_ASSIGNED'); ?>
					</th>
					</tr>
				</thead>
				<tr>
					<td style="text-align: center;">
						<?php
						// echo select list of non assigned players from team roster
						echo $this->lists ['team_referees'];
						?>
					</td>
					<td style="text-align: center; vertical-align: top;">
						<table>
							<?php
							foreach ( $this->positions as $key => $pos ) {
								?>
									<tr>
								<td style='text-align: center; vertical-align: middle;'>
									<!-- left / right buttons --> <br /> 
									<div class="col-md-2">
			
			<button type="button" id="multiselect_rightSelected"
				class="btn btn-block">
				<i class="icon-arrow-right"></i>
			</button>
			<button type="button" id="multiselect_leftSelected"
				class="btn btn-block">
				<i class="icon-arrow-left"></i>
			</button>
		</div>
								</td>
								<td>
									<!-- player affected to this position --> <b><?php echo Text::_($pos->text); ?></b><br />
										<?php echo $this->lists['team_referees'.$key];?>
									</td>
								<td style='text-align: center; vertical-align: middle;'>
									<!-- up/down buttons --> <br /> 
									<div class="col-md-2">
			<button type="button" id="multiselect_moveUp" class="btn btn-block"
				onclick="moveOptionUp('multiselect_to
					');">
				<i class="icon-uparrow"></i>
			</button>
			<button type="button" id="multiselect_moveDown" class="btn btn-block"
				onclick="moveOptionDown('multiselect_to');">
				<i class="icon-downarrow"></i>
			</button>
		</div>
							</tr>
								<?php
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<br /> <br /> 
		<input type="hidden" name="task" value="" /> 
		<input type="hidden" name="cid[]" value="<?php echo $this->match->id; ?>" />
		<input type="hidden" name="project" value="<?php echo $this->project_id; ?>" />
		<input type="hidden" name="changes_check" value="0" id="changes_check" />
		<input type="hidden" name="option" value="com_joomleague" id="option" />
		<input type="hidden" name="positionscount" value="<?php echo count($this->positions); ?>" id="positioncount" />
		<input type="hidden" name="team_id" value="<?php echo $this->team_id; ?>" id="team" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
