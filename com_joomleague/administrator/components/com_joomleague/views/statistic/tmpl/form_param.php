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
<?php foreach ($this->form->getFieldsets('params') as $fieldset): ?>

<fieldset class="adminform">
	<legend><?php echo Text::_($fieldset->label);?></legend>
	<?php if ($fieldset->description): ?>
		<div class="fs-description">
			<?php echo Text::_($fieldset->description); ?>
		</div>
	<?php endif; ?>
	<?php foreach($this->form->getFieldset($fieldset->name) as $field): ?>
	<div class="control-group">
		<div class="control-label"><?php echo $field->label;?></div>
		<div class="controls"><?php echo $field->input;?></div>
	</div>
	<?php endforeach; ?>
</fieldset>
<?php endforeach; ?>