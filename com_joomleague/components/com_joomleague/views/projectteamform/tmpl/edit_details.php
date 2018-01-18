<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>
<fieldset class="form-vertical">
	<legend></legend>
	<?php
	echo $this->form->renderField('admin');
	?>

	<!-- only with DivisionLeague -->
	<?php if ($this->project->project_type == 'DIVISIONS_LEAGUE') :?>

	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_DIV');	?></div>
		<div class="controls">
			<?php
		$inputappend = '';
		if($this->item->division_id == 0)
		{
			$inputappend = ' style="background-color:#bbffff"';
		}
		echo HTMLHelper::_('select.genericlist',$this->lists['divisions'],'division_id',$inputappend . 'class="inputbox" size="1"','value','text',
				$this->item->division_id);
		?>
		</div>
	</div>
	<?php endif;?>

	<?php
	echo $this->form->renderField('standard_playground');
	echo $this->form->renderField('is_in_score');
	?>
	<fieldset class="form-horizontal">
		<?php
		echo $this->form->renderField('start_points');
		echo $this->form->renderField('reason');
		?>
	</fieldset>
	<fieldset class="form-horizontal">
		<?php
		echo $this->form->renderField('use_finally');
		echo $this->form->renderField('matches_finally');
		echo $this->form->renderField('points_finally');
		echo $this->form->renderField('neg_points_finally');
		echo $this->form->renderField('won_finally');
		echo $this->form->renderField('draws_finally');
		echo $this->form->renderField('lost_finally');
		echo $this->form->renderField('homegoals_finally');
		echo $this->form->renderField('guestgoals_finally');
		echo $this->form->renderField('diffgoals_finally');
		?>
	</fieldset>
</fieldset>