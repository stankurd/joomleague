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
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_DESCR');?></legend>
	<table class="admintable">
		<tr>
			<td colspan="2">
				<div class="button2-left" style="display: inline">
					<div class="readmore">
						<?php
						// create the button code to use in form while selecting
						// a project and team to assign a new person to
						$button = '<a class="modal-button modal btn" title="Select" ';
						$button .= 'href="index.php?option=com_joomleague&view=person&task=persons.personassign" ';
						$button .= 'rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . Text::_('Select') . '</a>';
						echo $button;
						?>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			<td class="key"><label for="project_id"> <?php
			echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_PID');
			?>
			</label></td>
			<td><input onblur="document.getElementById('project_name').value=''" type="text"
				name="project_id" id="project_id" value="" size="5" maxlength="6" />
				<input type="text" readonly name="project_name" id="project_name"
				value="" size="50" /></td>
		
		
		<tr>
			<td class="key"><label for="team"> <?php
			echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_ASSIGN_TID');
			?>
			</label></td>
			<td><input onblur="document.getElementById('team_name').value=''" type="text"
				name="team_id" id="team_id" value="" size="5" maxlength="6" /> <input
				type="text" readonly name="team_name" id="team_name" value=""
				size="50" /></td>
		</tr>
	</table>
</fieldset>
