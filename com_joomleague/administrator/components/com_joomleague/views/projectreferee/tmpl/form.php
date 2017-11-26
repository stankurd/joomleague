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
?>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<?php
	$p = 1;
	echo JHtml::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DETAILS'));
	echo $this->loadTemplate('details');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_PICTURE'));
	echo $this->loadTemplate('picture');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_DESCRIPTION'));
	echo $this->loadTemplate('description');
	echo JHtml::_('bootstrap.endTab');

	echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('COM_JOOMLEAGUE_TABS_EXTENDED'));
	//echo $this->loadTemplate('extended');
	echo JHtml::_('bootstrap.endTab');

	if(Factory::getUser()->authorise('core.admin','com_joomleague') ||
			 Factory::getUser()->authorise('core.admin','com_joomleague.project.'.$this->project->id))
	{
		echo JHtml::_('bootstrap.addTab','tabs','panel' . $p ++,JText::_('JCONFIG_PERMISSIONS_LABEL'));
		echo $this->loadTemplate('permissions');
		echo JHtml::_('bootstrap.endTab');
	}
	echo JHtml::_('bootstrap.endTabSet');
	?>
	<!-- input fields -->
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>