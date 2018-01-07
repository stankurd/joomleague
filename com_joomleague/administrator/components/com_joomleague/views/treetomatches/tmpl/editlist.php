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

HTMLHelper::_('behavior.tooltip');

JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ASSIGN'));
JLToolBarHelper::save('treetomatches.save_matcheslist');
JLToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetonodes');
JLToolBarHelper::help('screen.joomleague',true);
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
		var mylist = document.getElementById('node_matcheslist');
		for(var i=0; i<mylist.length; i++)
		{
			  mylist[i].selected = true;
		}
		Joomla.submitform( pressbutton );
	}
</script>
<script>
jQuery(document).ready(function($) {
	$('#multiselect').multiselect();
});
</script>

<form action="<?php echo $this->request_url; ?>" method="post"
	id="adminForm" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ASSIGN_TITLE', '<i>' . $this->project->name . '</i>');?></legend>
		<div class="row">
			<div class="col-md-3">
				<b>
				<?php
				echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ASSIGN_AVAIL_MATCHES');
				?>
				</b><br />
						<?php
						echo $this->lists['matches'];
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
							echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETOMATCH_ASSIGN_NODE_MATCHES');
							?>
						</b><br />
						<?php
						echo $this->lists['node_matches'];
						?>
				</div>
		</div>
		
	</fieldset>

	<input type="hidden" name="matcheschanges_check" value="0"
		id="matcheschanges_check" /> <input type="hidden" name="option"
		value="com_joomleague" /> <input type="hidden" name="cid[]"
		value="<?php echo $this->node->id; ?>" /> <input type="hidden"
		name="task" value="treetomatches.save_matcheslist" />
</form>