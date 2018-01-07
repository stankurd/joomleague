<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

jimport('cms.html.bootstrap');
defined('_JEXEC') or die;
$option = Factory::getApplication()->input->get('option');
?>
<style type="text/css">
	<!--
	fieldset.panelform label, fieldset.panelform div.paramrow label, fieldset.panelform span.faux-label {
		max-width: 255px;
		min-width: 255px;
		padding: 0 5px 0 0;
	}
	-->
</style>

<form action="index.php" method="post" id="adminForm">
	<div class="col50" id="settings">
    <?php
		$selector = 'settings';
		$i = 1;
		echo HTMLHelper::_('bootstrap.startTabSet', $selector, array('active'=>'panel'.$i)); 
		$fieldSets = $this->form->getFieldsets();
        foreach ($fieldSets as $name => $fieldSet) :
            $label = $fieldSet->name;
			echo HTMLHelper::_('bootstrap.addTab', $selector, 'panel'.$i++, Text::_($label));
			?>
			<fieldset class="form-vertical">
				<?php
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<fieldset class="form-horizontal">'.Text::_($fieldSet->description).'</fieldset>';
				endif;
				?>
				
				<?php foreach ($this->form->getFieldset($name) as $field): ?>
				<div class="control-group">	
				<?php if (!$field->hidden): ?>
					<div class="control-label"><?php echo $field->label; ?></div>
				<?php endif; ?>
					<div class="controls"><?php echo $field->input; ?></div>
				</div>
				<?php endforeach; ?>
			</fieldset><?php 
 			echo HTMLHelper::_('bootstrap.endTab');
 			?>
    	<div class="clr"></div>
    	<?php endforeach; ?>
    	<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
	</div>
	<div>	
	<input type="hidden" name="task" value="setting.display">
	<input type="hidden" name="option" value="<?php echo $option; ?>">
	<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
