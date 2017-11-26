<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('script', 'administrator/components/com_joomleague/assets/js/template.js');

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
<form action="<?php echo JRoute::_('index.php?option=com_joomleague&view=template') ?>" method="post" id="adminForm">
	<div class="col50" id="template">
		<div style='text-align: right;'>
			<?php echo $this->lists['templates']; ?>
		</div>
	<?php
	if ($this->project->id != $this->template->project_id) {
	    Factory::getApplication()->enqueueMessage(JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_MASTER_WARNING'), 'notice');
		?><input type="hidden" name="master_id" value="<?php echo $this->template->project_id; ?>"/><?php
	}
	?>
		<fieldset class="form-horizontal">
			<legend>
				<?php echo JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_LEGEND', 
						'<i>' . JText::_('COM_JOOMLEAGUE_FES_' . strtoupper($this->form->getName()) . '_NAME') . '</i>',
						'<i>' . $this->project->name . '</i>'); ?>
			</legend>
			<fieldset class="form-horizontal">
				<?php echo JText::_('COM_JOOMLEAGUE_FES_' . strtoupper($this->form->getName()) . '_DESCR'); ?>
			</fieldset>
	
		<?php
		$selector = 'template';
		$i = 1;
		echo JHtml::_('bootstrap.startTabSet', $selector, array('active'=>'tab'.$i));
        $fieldSets = $this->form->getFieldsets();
        foreach ($fieldSets as $name => $fieldSet) :
            $label = $fieldSet->name;
			echo JHtml::_('bootstrap.addTab', $selector, "tab".$i, JText::_($label));
        	$i++
			?>
			<fieldset class="form-vertical">
				<?php
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<fieldset class="form-horizontal">'.JText::_($fieldSet->description).'</fieldset>';
					echo '<br />';
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
				
			</fieldset>
			<?php echo JHtml::_('bootstrap.endTab'); ?>
    		<div class="clearfix"></div>
    	<?php endforeach; ?>
    	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
		</fieldset>
    </div>
    <div>		
		<input type="hidden" name="boxchecked" value="1" />
		<input type='hidden' name='user_id' value='<?php echo $this->user->id; ?>'/>
		<input type="hidden" name="cid[]" value="<?php echo $this->template->id; ?>"/>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
