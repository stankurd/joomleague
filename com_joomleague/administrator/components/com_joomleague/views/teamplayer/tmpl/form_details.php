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
<fieldset class="adminform">
	<legend>
	<?php
	$name = JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,
	JoomleagueHelper::defaultNameFormat());
	echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMPLAYER_DETAILS_TITLE',$name,'<i>'.$this->projectteam->name.'</i>',
	'<i>'.$this->project->name.'</i>');
	?>
	</legend>
	<fieldset class="form-horizontal">
		<div class="control-group">
			<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYER_POS'); ?></div>
			<div class="controls"><?php echo $this->lists['projectpositions']; ?></div>
		</div>
		<?php 
		echo $this->form->renderField('jerseynumber');
		echo $this->form->renderField('injury');
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('injury_date'); ?></div>
			<div class="controls"><?php echo $this->lists['injury_date']; ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('injury_end'); ?></div>
			<div class="controls"><?php echo $this->lists['injury_end']; ?></div>
		</div>
		<?php
		echo $this->form->renderField('injury_detail');
		echo $this->form->renderField('suspension');
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('suspension_date'); ?></div>
			<div class="controls"><?php echo $this->lists['suspension_date']; ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('suspension_end'); ?></div>
			<div class="controls"><?php echo $this->lists['suspension_end']; ?></div>
		</div>
		<?php
		echo $this->form->renderField('suspension_detail');
		echo $this->form->renderField('away');
		?>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('away_date'); ?></div>
			<div class="controls"><?php echo $this->lists['away_date']; ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('away_end'); ?></div>
			<div class="controls"><?php echo $this->lists['away_end']; ?></div>
		</div>
		<?php
		echo $this->form->renderField('away_detail');
		echo $this->form->renderField('id'); 
		?>
	</fieldset>
</fieldset>