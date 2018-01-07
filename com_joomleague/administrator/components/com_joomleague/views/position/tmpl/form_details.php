<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @todo
 * - create form field for Parent_id
 */
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_DETAILS_LEGEND'); ?></legend>
	<?php
	echo $this->form->renderField('name');
	echo $this->form->renderField('alias');
	echo $this->form->renderField('sports_type_id');
	echo $this->form->renderField('published');
	echo $this->form->renderField('persontype');
	echo $this->form->renderField('ordering');
	echo $this->form->renderField('id');
	?>
	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_P_POSITION'); ?></div>
		<div class="controls"><?php echo $this->lists['parents']; ?></div>
	</div>
</fieldset>
