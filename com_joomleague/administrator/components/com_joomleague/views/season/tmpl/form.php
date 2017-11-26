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

JHtml::_('behavior.formvalidator');
Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "season.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
?>
<form action="<?php echo JRoute::_('index.php?option=com_joomleague&layout=form&id='.(int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate">
	<?php
	$p = 1;
	echo JHtml::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo JHtml::_('bootstrap.endTab');
	echo JHtml::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>