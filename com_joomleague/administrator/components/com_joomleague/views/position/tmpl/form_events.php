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

?>
<script>
jQuery(document).ready(function($) {
	jQuery('#multiselect').multiselect({
		sort: false
	});
});
</script>
	
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_EVENTTYPES_LEGEND'); ?></legend>
	
	<div class="row">
		<div class="col-md-3">
			<b><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_EXISTING_EVENTTYPES'); ?></b>
			<br /><?php echo $this->lists['events']; ?>
		</div>
		<div class="col-md-2">
			<button type="button" id="multiselect_rightAll" class="btn btn-block">
				<i class="icon-forward"></i>
			</button>
			<button type="button" id="multiselect_rightSelected"
				class="btn btn-block">
				<i class="icon-arrow-right"></i>
			</button>
			<button type="button" id="multiselect_leftSelected"
				class="btn btn-block">
				<i class="icon-arrow-left"></i>
			</button>
			<button type="button" id="multiselect_leftAll" class="btn btn-block">
				<i class="icon-backward"></i>
			</button>
		</div>
		<div class="col-md-3">
			<b><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_ASSIGNED_EVENTTYPES_TO_POS'); ?></b>
			<br /><?php echo $this->lists['position_events']; ?>
		</div>
		<div class="col-md-2">
			<button type="button" id="multiselect_moveUp" class="btn btn-block"
				onclick="moveOptionUp('multiselect_to');">
				<i class="icon-uparrow"></i>
			</button>
			<button type="button" id="multiselect_moveDown" class="btn btn-block"
				onclick="moveOptionDown('multiselect_to');">
				<i class="icon-downarrow"></i>
			</button>
		</div>
	</div>
	<div class="row">

	<div class="clearfix"></div>
	
	<fieldset class="form-horizontal">
		<br>
		<p><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_EVENTTYPES_HINT'); ?></p>
	</fieldset>
</fieldset>
