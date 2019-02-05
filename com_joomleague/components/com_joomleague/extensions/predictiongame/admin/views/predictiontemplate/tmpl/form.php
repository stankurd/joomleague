<?php use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.tooltip');HTMLHelper::_('behavior.modal');

//$component_text = 'COM_JOOMLEAGUE_';

$i    = 1;
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
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm">
	
	<?php
	
	?>
	<fieldset class="adminform">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TEMPLATE_LEGEND', '<i>' . Text::_('COM_JOOMLEAGUE_FES_' . strtoupper($this->form->getName()) . '_NAME') . '</i>', '<i>' . $this->predictionGame->name . '</i>'); ?></legend>
		<fieldset class="adminform">
			<?php
			echo Text::_('COM_JOOMLEAGUE_FES_' . strtoupper($this->form->getName()) . '_DESCR');
			?>
		</fieldset>

		<?php
		/**echo HTMLHelper::_('tabs.start','tabs', array('useCookie'=>1));
        $fieldSets = $this->form->getFieldsets();
        foreach ($fieldSets as $name => $fieldSet) :
            $label = $fieldSet->name;
            echo HTMLHelper::_('tabs.panel',Text::_($label), 'panel'.$i++);
			?>
			<fieldset class="panelform">
				<?php
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<fieldset class="adminform">'.Text::_($fieldSet->description).'</fieldset>';
				endif;
				?>
				<ul class="config-option-list">
				<?php foreach ($this->form->getFieldset($name) as $field): ?>
					<li>
					<?php if (!$field->hidden) : ?>
					<?php echo $field->label; ?>
					<?php endif; ?>
					<?php echo $field->input; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
 
    <div class="clr"></div>
    <?php endforeach; ?>
    <?php echo HTMLHelper::_('tabs.end'); ?>*/
		$selector = 'template';
		$i = 1;
		echo HTMLHelper::_('bootstrap.startTabSet', $selector, array('active'=>'tab'.$i));
        $fieldSets = $this->form->getFieldsets();
        foreach ($fieldSets as $name => $fieldSet) :
            $label = $fieldSet->name;
			echo HTMLHelper::_('bootstrap.addTab', $selector, "tab".$i, Text::_($label));
        	$i++
			?>
			<fieldset class="form-vertical">
				<?php
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<fieldset class="form-horizontal">'.Text::_($fieldSet->description).'</fieldset>';
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
			<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
    		<div class="clearfix"></div>
    	<?php endforeach; ?>
    	<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
	<div>		
		<input type='hidden' name='user_id' value='<?php echo $this->user->id; ?>'/>
		<input type="hidden" name="cid[]" value="<?php echo $this->template->id; ?>"/>
		<input type="hidden" name="task" value=""/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
