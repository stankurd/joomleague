<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * @todo:
 * fix adding newLeague/newSeason
 */
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

?>
<script>
/*
jQuery(document).ready(function() {

    jQuery('#jform_newLeagueCheck').val(jQuery(this).is(':checked'));
    jQuery('#jform_newSeasonCheck').val(jQuery(this).is(':checked'));

    jQuery('#jform_newLeagueCheck').change(function() {
        if(jQuery(this).is(":checked")) {
            var returnVal = confirm("Are you sure that you want to create a new league?");
            jQuery(this).attr("checked", returnVal);
        }
        jQuery('#jform_newLeagueCheck').val(jQuery(this).is(':checked'));
    });
    jQuery('#jform_newSeasonCheck').change(function() {
        if(jQuery(this).is(":checked")) {
            var returnVal = confirm("Are you sure that you want to create a new season");
            jQuery(this).attr("checked", returnVal);
        }
        jQuery('#jform_newSeasonCheck').val(jQuery(this).is(':checked'));
    });
});
*/
</script>

<div class="row-fluid">
	<div class="span5">
		<fieldset class="form-horizontal">
			<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECT_LEGEND_DETAILS','<i>'.$this->form->getValue('name').'</i>'); ?></legend>
			<?php
			echo $this->form->renderField('name');
			echo $this->form->renderField('alias');
			echo $this->form->renderField('published');
			echo $this->form->renderField('sports_type_id');
			?>
	<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('league_id'); ?></div>
				<div class="controls">
		<?php echo $this->form->getInput('league_id'); ?>
		<?php echo $this->form->renderField('newLeagueCheck'); ?>
		</div>
			</div>


			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('leagueNew'); ?></div>
				<div class="controls">
			<?php echo $this->form->getInput('leagueNew'); ?>
		</div>
			</div>


			<div class="control-group" id="leagueNew">
				<div class="control-label"><?php echo $this->form->getLabel('season_id'); ?></div>
				<div class="controls">
		<?php echo $this->form->getInput('season_id'); ?>
		<?php echo $this->form->renderField('newSeasonCheck'); ?>
		</div>
			</div>

			<div class="control-group" id="seasonNew">
				<div class="control-label"><?php echo $this->form->getLabel('seasonNew'); ?></div>
				<div class="controls">
			<?php echo $this->form->getInput('seasonNew'); ?>
		</div>
			</div>

	<?php
	echo $this->form->renderField('project_type');
	echo $this->form->renderField('master_template');
	echo $this->form->renderField('extension');
	echo $this->form->renderField('id');
	echo $this->form->renderField('ordering');
	?>
</fieldset>
	</div>
	<div class="span2"></div>
	<div class="span5">
		<fieldset class="form-vertical">
			<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECT_DATE_PARAMS'); ?></legend>
		<?php
		echo $this->form->renderField('start_date');
		echo $this->form->renderField('start_time');
		echo $this->form->renderField('timezone');
		echo $this->form->renderField('is_utc_converted');
		?>
		</fieldset>
	</div>
</div>