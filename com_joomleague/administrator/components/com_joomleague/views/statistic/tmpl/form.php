<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

JHtml::_('behavior.formvalidator');
Factory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "statistic.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
');
$isNew = $this->isNew;
?>
<form action="<?php echo JRoute::_('index.php?option=com_joomleague&layout=form&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php
	$p = 1;
	echo JHtml::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_PICTURE',true));
	echo $this->loadTemplate('picture');
	echo JHtml::_('bootstrap.endTab');

	if (!$isNew) {
		echo JHtml::_('bootstrap.addTab','tabs','panel3',JText::_('COM_JOOMLEAGUE_TABS_PARAMETERS',true));
		echo $this->loadTemplate('param');
		echo JHtml::_('bootstrap.endTab');

		echo JHtml::_('bootstrap.addTab','tabs','panel4',JText::_('COM_JOOMLEAGUE_TABS_GENERAL_PARAMETERS',true));
		echo $this->loadTemplate('gparam');
		echo JHtml::_('bootstrap.endTab');
	}

	echo JHtml::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<?php if (!$isNew): ?>
		<input type="hidden" name="calculated" value="<?php echo $this->calculated; ?>" />
	<?php endif; ?>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->form->getValue('id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
