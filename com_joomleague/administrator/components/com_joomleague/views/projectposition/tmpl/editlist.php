<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.core');
HTMLHelper::_('bootstrap.tooltip');
?>
<script>
jQuery(document).ready(function($) {
	jQuery('#multiselect').multiselect({
		sort: false
	});
});
</script>
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">
	<fieldset class="adminform">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_POSITION_EDIT_LEGEND','<i>'.$this->project->name.'</i>');?></legend>
		<div class="row">
		<div class="col-md-9">
		<div class="row">
			<div class="col-md-3">
				<b>
							<?php
							echo Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_EDIT_AVAILABLE');
							?>
						</b><br />
						<?php
						echo $this->lists['positions'];
						?>
				</div>
			<div class="col-md-2">
				<button type="button" id="multiselect_rightAll" class="btn btn-block">
					<i class="icon-forward"></i>
				</button>
				<button type="button" id="multiselect_rightSelected" class="btn btn-block">
					<i class="icon-arrow-right"></i>
				</button>
				<button type="button" id="multiselect_leftSelected" class="btn btn-block">
					<i class="icon-arrow-left"></i>
				</button>
				<button type="button" id="multiselect_leftAll" class="btn btn-block">
					<i class="icon-backward"></i>
				</button>
			</div>
			<div class="col-md-3 ">
				<b>
							<?php
							echo Text::_('COM_JOOMLEAGUE_ADMIN_P_POSITION_EDIT_ASSIGNED');
							?>
						</b><br />
						<?php
						echo $this->lists['project_positions'];
						?>
				</div>
		</div>
		</div>
	</fieldset>
	<!-- Input fields -->
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>