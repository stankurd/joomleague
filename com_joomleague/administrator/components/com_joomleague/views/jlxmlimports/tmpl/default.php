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
?>
<div id="editcell">
	<form enctype='multipart/form-data' action='<?php echo $this->request_url; ?>' method='post' id='adminForm'>
	<div id="j-main-container" class="j-main-container">
		<table class='adminlist'>
			<thead><tr><th><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TABLE_TITLE_1', $this->config->get('upload_maxsize') ); ?></th></tr></thead>
			<tfoot><tr><td><?php
				echo '<p>';
				echo '<b>'.Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_EXTENSION_INFO').'</b>';
				echo '</p>';
				echo '<p>';
				echo Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_HINT1').'<br>';
				echo '</p>';
				?></td></tr></tfoot>
			<tbody><tr><td><fieldset style='text-align: center; '>
				<input class='input_box' id='import_package' name='import_package' type='file' size='57' />
				<input class='button' type='submit' value='<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_UPLOAD_BUTTON'); ?>' />
				</fieldset></td></tr></tbody>
		</table>
		<input type='hidden' name='sent' value='1' />
		<input type='hidden' name='MAX_FILE_SIZE' value='<?php echo $this->config->get('upload_maxsize'); ?>' />
		<input type='hidden' name='task' value='jlxmlimport.save' />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
</div>