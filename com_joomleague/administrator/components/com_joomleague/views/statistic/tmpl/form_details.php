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
	<legend><?php echo Text::_( 'COM_JOOMLEAGUE_ADMIN_STAT_STAT' ); ?></legend>
	<?php
	echo $this->form->renderField('name');
	echo $this->form->renderField('sports_type_id');
	echo $this->form->renderField('short');
	echo $this->form->renderField('alias');
	echo $this->form->renderField('class');
	echo $this->form->renderField('published');
	echo $this->form->renderField('ordering');
	echo $this->form->renderField('id');
	?>
	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_STAT_NOTE'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('note'); ?></div>
	</div>
</fieldset>