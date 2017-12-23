<?php
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.tooltip');
?>
<script>
	function Joomla.submitbutton(pressbutton)
	{
		var form = $('adminForm');
		if (pressbutton == 'cancel')
		{
			Joomla.submitform( pressbutton );
			return;
		}
		var mylist = document.getElementById('project_teamslist');
		for(var i=0; i<mylist.length; i++)
		{
			  mylist[i].selected = true;
		}
		Joomla.submitform( pressbutton );
	}
</script>
<script>
jQuery(document).ready(function($) {
	jQuery('#multiselect').multiselect();
});
</script>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="form-horizontal">
		<legend>
				<?php
				echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN_TITLE','<i>' . $this->project->name . '</i>');
				?>
			</legend>
		<div class="row">
			<div class="col-md-3">
				<b>
							<?php
							echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN_AVAIL_TEAMS');
							?>
						</b><br />
						<?php
						echo $this->lists['teams'];
						?>
				</div>
			<div class="col-md-2">
				<button type="button" id="multiselect_rightAll"
					class="btn btn-block">
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
				<b>
							<?php
							echo JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_ASSIGN_PROJ_TEAMS');
							?>
						</b><br />
						<?php
						echo $this->lists['project_teams'];
						?>
				</div>
		</div>
	</fieldset>
	<div class="clearfix"></div>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="task" value="projectteam.save_matcheslist" />
		<?php echo HTMLHelper::_('form.token'); ?>
</form>
