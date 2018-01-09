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

defined('_JEXEC') or die;
?>
<div id="j-main-container" class="j-main-container">
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<?php
	$p = 1;
	echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS'));
	echo $this->loadTemplate('details');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_PICTURE'));
	echo $this->loadTemplate('picture');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DESCRIPTION'));
	echo $this->loadTemplate('description');
	echo HTMLHelper::_('bootstrap.endTab');

	echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_EXTENDED'));
	echo $this->loadTemplate('extended');
	echo HTMLHelper::_('bootstrap.endTab');

	if(Factory::getUser()->authorise('core.admin','com_joomleague') ||
			 Factory::getUser()->authorise('core.admin','com_joomleague.project.'.$this->project->id))
	{
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('JCONFIG_PERMISSIONS_LABEL'));
		echo $this->loadTemplate('permissions');
		echo HTMLHelper::_('bootstrap.endTab');
	}
	echo HTMLHelper::_('bootstrap.endTabSet');
	?>
	<!-- input fields -->
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</div>