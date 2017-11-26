<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined ( '_JEXEC' ) or die ();
?>
<fieldset class="form-horizontal">
	<legend><?php echo JText::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_MD'); ?></legend>
	<?php
	echo $this->form->renderField ( 'cancel' );
	echo $this->form->renderField ( 'cancel_reason' );
	echo $this->form->renderField ( 'playground_id' );
	echo $this->form->renderField ( 'id' );
	?>
</fieldset>
