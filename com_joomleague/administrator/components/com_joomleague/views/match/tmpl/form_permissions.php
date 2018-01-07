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

<div>
	<fieldset class="adminform">
		<legend><?php echo Text::_('JCONFIG_PERMISSIONS_LABEL'); ?></legend>
		<?php foreach ($this->form->getFieldset('Permissions') as $field): ?>
			<?php echo $field->label; ?>
			<div class="clr"></div>
			<?php echo $field->input; ?>
		<?php endforeach; ?>
	</fieldset>
</div>
