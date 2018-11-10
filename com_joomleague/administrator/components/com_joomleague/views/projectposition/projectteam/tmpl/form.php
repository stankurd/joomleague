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

defined('_JEXEC') or die;

HTMLHelper::_('behavior.formvalidator');
Factory::getDocument()->addScriptDeclaration('
Joomla = window.Joomla || {};

(function() {
	Joomla.submitbutton = function(task)
	{
		if (task == "projectteam.cancel" || document.formvalidator.isValid(document.getElementById("adminForm")))
		{
			Joomla.submitform(task, document.getElementById("adminForm"));
		}
	};
})();
');
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&layout=form&id=' . (int) $this->item->id); ?>" method="post" id="adminForm" name="adminForm" class="form-validate">
	<?php
	$p = 1;
	echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_PICTURE',true));
	echo $this->loadTemplate('picture');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DESCRIPTION',true));
	echo $this->loadTemplate('description');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_TRAINING',true));
	echo $this->loadTemplate('training');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_EXTENDED',true));
	echo $this->loadTemplate('extended');
	echo HTMLHelper::_('bootstrap.endTab');

	if(Factory::getUser()->authorise('core.admin','com_joomleague') ||
			 Factory::getUser()->authorise('core.admin','com_joomleague.project.' . $this->project->id))
	{
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('JCONFIG_PERMISSIONS_LABEL',true));
		echo $this->loadTemplate('permissions');
		echo HTMLHelper::_('bootstrap.endTab');
	}

	echo HTMLHelper::_('bootstrap.endTabSet');
	?>
	<div class="clearfix"></div>
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="task" value="" id='task' />
	<input type="hidden" name="return" value="<?php echo Factory::getApplication()->input->getCmd('return'); ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
