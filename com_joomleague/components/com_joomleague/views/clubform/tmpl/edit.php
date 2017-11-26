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

JHtml::_('behavior.tabstate');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.calendar');
JHtml::_('behavior.formvalidator');

Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "clubform.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_joomleague&a_id='.(int)$this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate form-horizontal">
	
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('clubform.save')">
				<span class="icon-ok"></span><?php echo JText::_('JSAVE') ?>
			</button>
		</div>
		<div class="btn-group">
			<button type="button" class="btn" onclick="Joomla.submitbutton('clubform.cancel')">
				<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</div>
	
	<?php
	$p = 1;
	echo JHtml::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DESCRIPTION',true));
	echo $this->loadTemplate('description');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_EXTENDED',true));
	echo $this->loadTemplate('extended');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
