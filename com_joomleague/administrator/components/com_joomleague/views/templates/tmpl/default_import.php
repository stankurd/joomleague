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
<script type="text/javascript">
<!--
jQuery('domready', function()
{
	jQuery('#templateid').change(function()
	{		
		if (this.value)
		{		
			jQuery('#importform').submit();
		}
	});
});
//-->
</script>
<div id="masterimport">
	<form action="<?php echo $this->request_url; ?>" method="post" id="importform">
		<p class='helpText'><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATES_MASTER_HINT_01','<span class="masterName" >'.$this->master.'</span>'); ?></p>
		<p class='helpText'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TEMPLATES_MASTER_HINT_02'); ?></p>
		<?php echo $this->lists['mastertemplates']; ?>
		<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="task" value="template.masterimport" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>