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
?>
<script type="text/javascript">
(function() {
	$('csv-file-upload-submit').addEvent('click', function(){
		$('task').value = 'import.csv<?php echo $this->table; ?>import';
		$('adminForm').submit();
	});
});

</script>
<style type="text/css">
	<!--
	fieldset.panelform label, fieldset.panelform div.paramrow label, fieldset.panelform span.faux-label {
		max-width: 255px;
		min-width: 255px;
		padding: 0 5px 0 0;
	}
	-->
</style>
<div id="j-main-container" class="span10">
<form method="post" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
		<legend>
			<?php echo Text::sprintf('CSV-IMPORT [%1$s]', '<i>' . $this->table . '</i>'); ?>
		</legend>
		<?php echo '<strong>' . Text::_('CSV-IMPORT INSTRUCTIONS') . '</strong>'; ?>
		<ul>
			<li>
			<?php echo Text::sprintf('IMPORT %1$s COLUMN NAMES', $this->table); ?>
			</li>
			<li>
			<?php echo Text::sprintf('IMPORT %1$s CSV FORMAT', $this->table); ?>
			</li>
			<li><?php echo Text::sprintf(	'CSV FORMAT AS DEFINED IN %1$s',
											'<a target="_blank" href="http://tools.ietf.org/html/rfc4180">rfc 4180</a>'); ?>
			</li>
			<li>
			<?php echo Text::sprintf(	'IMPORT %1$s POSSIBLE COLUMNS: %2$s',
										$this->table,
										'<strong>' . implode(", ",$this->tablefields) . '</strong>'); ?>
			</li>
		</ul>
		<fieldset class="radio">
		<table>
			<tr>
				<td>
				<label for="file">
					<?php echo Text::_('IMPORT SELECT-CSV:'); ?>
				</label>
				</td>
				<td>
						<input type="file" id="csv-file-upload" accept="text/*" name="FileCSV" />
						<input type="submit" id="csv-file-upload-submit" value="<?php echo Text::_('START CSV-IMPORT'); ?>" />
						<span id="upload-clear"></span>
				</td>
			</tr>
			<tr>
				<td>
				<label for="replace">
					<?php echo Text::_('IMPORT REPLACE IF EXISTS:'); ?>
				</label>
				</td>
				<td>
				<?php
				echo HTMLHelper::_('select.booleanlist', 'csv-replace', '', 0);
				?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="replace">
						<?php echo Text::_('Enter delimiter:'); ?>
					</label>
				</td>
				<td>
					<input type="text" id="csv-delimiter" name="csvdelimiter" value=";" size="2" />
				</td>
			</tr>
		</table>
		</fieldset>
	</fieldset>
	<input type="hidden" name="option"		value="com_joomleague" />
	<input type="hidden" name="task" id="task" value="import.import" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>