<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<script>
jQuery(document).ready(function($) {
	jQuery('#multiselect2').multiselect({		
		right: '#multiselect2_to',
		rightSelected: '#multiselect2_rightSelected',
		leftSelected: '#multiselect2_leftSelected',
		rightAll: '#multiselect2_rightAll',
		leftAll: '#multiselect2_leftAll',
		sort: false
	});
});
</script>
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_STATISTICS_LEGEND'); ?></legend>
	<div class="row">
		<div class="col-md-3">
			<b><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_EXISTING_STATISTICS'); ?></b>
			<br /><?php echo $this->lists['statistic']; ?>
		</div>
		<div class="col-md-2">
			<button type="button" id="multiselect2_rightAll"
				class="btn btn-block">
				<i class="icon-forward"></i>
			</button>
			<button type="button" id="multiselect2_rightSelected"
				class="btn btn-block">
				<i class="icon-arrow-right"></i>
			</button>
			<button type="button" id="multiselect2_leftSelected"
				class="btn btn-block">
				<i class="icon-arrow-left"></i>
			</button>
			<button type="button" id="multiselect2_leftAll" class="btn btn-block">
				<i class="icon-backward"></i>
			</button>
		</div>
		<div class="col-md-3">
			<b><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_ASSIGNED_STATS_TO_POS'); ?></b>
			<br /><?php echo $this->lists['position_statistic']; ?>
		</div>
		<div class="span2">
			<button type="button" id="multiselect_moveUp" class="btn btn-block"
				onclick="moveOptionUp('multiselect2_to');">
				<i class="icon-uparrow"></i>
			</button>
			<button type="button" id="multiselect_moveDown" class="btn btn-block"
				onclick="moveOptionDown('multiselect2_to');">
				<i class="icon-downarrow"></i>
			</button>
		</div>
	</div>

	<div class="clearfix"></div>
</fieldset>