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
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_LEAGUE_LEGEND'); ?></legend>
	<?php
	echo $this->form->renderField('name');
	echo $this->form->renderField('middle_name');
	echo $this->form->renderField('short_name');
	echo $this->form->renderField('alias');
	echo $this->form->renderField('admin');
	?>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('country'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('country'); ?>
		&nbsp;
		<?php 
		echo Countries::getCountryFlag($this->form->getValue('country'));
		if ($this->form->getValue('country')) {
			echo '('.$this->form->getValue('country').')';
		}
		?>
		</div>
	</div>
	<?php
	echo $this->form->renderField('ordering');
	echo $this->form->renderField('id');
	?>
</fieldset>