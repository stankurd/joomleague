<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('behavior.tooltip');HTMLHelper::_('behavior.modal');

// Set toolbar items for the page
$edit=Factory::getApplication()->input->getVar('edit',true);
$text=!$edit ? Text::_('Add new settings') : Text::_('Edit Prediction-Game settings');
ToolbarHelper::title(Text::_($text));
ToolBarHelper::save('predictiongame.save');

if (!$edit)
{
	JLToolBarHelper::divider();
	JLToolBarHelper::cancel('predictiongame.cancel');
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JLToolBarHelper::apply('predictiongame.apply');
	JLToolBarHelper::divider();

	JLToolBarHelper::cancel('predictiongame.cancel',Text::_('JL_GLOBAL_CLOSE'));
}
JLToolBarHelper::divider();
?>
<form method="post" name="adminForm" id="adminForm">
	<div class="col50">
		<?php
		$p = 1;
		echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p++,Text::_('JL_TABS_DETAILS',true));
		echo $this->loadTemplate('details');
		echo HTMLHelper::_('bootstrap.endTab');
		echo HTMLHelper::_('bootstrap.endTabSet');
		?>
		<div class="clr"></div>
		<input type="hidden" name="option" value="com_joomleague" />
		
		<input type="hidden" name="user_id" value="0" />
		<input type="hidden" name="project_id" value="0" />
		<input type="hidden" name="prediction_id" value="<?php echo $this->prediction->id; ?>" />
		<input type="hidden" name="cid[]" value="<?php echo $this->prediction->id; ?>" />
		<input type="hidden" name="task" value="" />
	</div>
<?php echo HTMLHelper::_('form.token')."\n"; ?>
</form>