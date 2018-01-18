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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.formvalidator');
Factory::getDocument()->addScriptDeclaration('
Joomla = window.Joomla || {};

(function() {
	Joomla.submitbutton = function(task)
	{
		if (task == "statistic.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
})();
');
$isNew = $this->isNew;
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&layout=form&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
	<?php
	$p = 1;
	echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_PICTURE',true));
	echo $this->loadTemplate('picture');
	echo HTMLHelper::_('bootstrap.endTab');

	if (!$isNew) {
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel3',Text::_('COM_JOOMLEAGUE_TABS_PARAMETERS',true));
		echo $this->loadTemplate('param');
		echo HTMLHelper::_('bootstrap.endTab');

		echo HTMLHelper::_('bootstrap.addTab','tabs','panel4',Text::_('COM_JOOMLEAGUE_TABS_GENERAL_PARAMETERS',true));
		echo $this->loadTemplate('gparam');
		echo HTMLHelper::_('bootstrap.endTab');
	}

	echo HTMLHelper::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<?php if (!$isNew): ?>
		<input type="hidden" name="calculated" value="<?php echo $this->calculated; ?>" />
	<?php endif; ?>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->form->getValue('id'); ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
