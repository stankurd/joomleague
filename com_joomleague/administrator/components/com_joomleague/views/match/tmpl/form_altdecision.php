<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ();
?>

<script type="text/javascript">
<!--
(function() {
	// altered decision fields management
	toggle_altdecision();
	jQuery('#jform_alt_decision').change(toggle_altdecision);
});

$function toggle_altdecision() {
	if (document.getElementById('jform_alt_decision').value == 0) {
		document.getElementById'alt_decision_enter').style.display='none';
		document.getElementById('team1_result_decision').disabled=true;
		document.getElementById('team2_result_decision').disabled=true;
		document.getElementById('decision_info').disabled=true;
	} else {
		document.getElementById('alt_decision_enter').style.display='block';
		document.getElementById('team1_result_decision').disabled=false;
		document.getElementById('team2_result_decision').disabled=false;
		document.getElementById('decision_info').disabled=false;
	}
}

//-->
</script>
<!-- Alt decision table START -->
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_AD');?></legend>			
	<?php
	echo $this->form->renderField ( 'count_result' );
	echo $this->form->renderField ( 'alt_decision' );
	?>
	<div id="alt_decision_enter" style="display:<?php echo ($this->item->alt_decision == 0) ? 'none' : 'block'; ?>">
		<div class="control-group">
			<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_AD_NEW_SCORE' ).' ' .$this->item->hometeam; ?></div>
			<div class="controls">
				<input type="text" class="inputbox" id="team1_result_decision"
					name="team1_result_decision" size="3"
					value="<?php if ($this->item->alt_decision == 1) if (isset($this->item->team1_result_decision)) echo $this->item->team1_result_decision; else echo 'X'; ?>"
					<?php if ($this->item->alt_decision == 0) echo 'DISABLED '; ?> />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_AD_NEW_SCORE' ).' ' .$this->item->awayteam;?></div>
			<div class="controls">
				<input type="text" class="inputbox" id="team2_result_decision"
					name="team2_result_decision" size="3"
					value="<?php
					if ($this->item->alt_decision == 1)
						if (isset ( $this->item->team2_result_decision ))
							echo $this->item->team2_result_decision;
						else
							echo 'X';
					?>"
					<?php
					if ($this->item->alt_decision == 0)
						echo 'DISABLED ';
					?> />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label">
				<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_AD_REASON_NEW_SCORE');?>
				<?php
				if (is_null ( $this->item->team1_result ) || ($this->item->alt_decision == 0)) {
					$disinfo = 'DISABLED ';
				}
				?>
			</div>
			<div class="controls">
				<input type="text" class="inputbox" id="decision_info"
					name="decision_info" size="30"
					value="<?php if ($this->item->alt_decision == 1 ){echo $this->item->decision_info;}?>"
					<?php
					if ($this->item->alt_decision == 0) {
						echo 'DISABLED ';
					}
					?> />
			</div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_AD_TEAM_WON');?></div>
			<div class="controls"><?php echo $this->lists['team_won']; ?></div>
		</div>
	</div>
</fieldset>