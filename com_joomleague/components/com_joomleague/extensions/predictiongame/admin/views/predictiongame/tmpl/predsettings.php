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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die('Restricted access');

HTMLHelper::_('behavior.tooltip');

// Set toolbar items for the page
$app = Factory::getApplication();
$edit=$app->input->getVar('edit',true);
$text=!$edit ? 'Add new settings' : 'Change settings of Prediction-Project';
ToolbarHelper::title(Text::_($text));

//ToolBarHelper::save('save_project_settings');

if (!$edit)
{
	ToolBarHelper::divider();
	ToolBarHelper::cancel();
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	ToolBarHelper::save('predictiongame.save_project_settings',Text::_('Save'));
	//JToolBarHelper::apply('predictiongame.apply_project_settings',Text::_('COM_JOOMLEAGUE_GLOBAL_APPLY'));
	ToolBarHelper::divider();
	ToolBarHelper::cancel('predictiongame.cancel',Text::_('COM_JOOMLEAGUE_GLOBAL_CLOSE'));
}

?>
<script type="text/javascript">

function change_published () {
  if (document.adminForm.published0.checked == true) {
    var deaktiviert=true;
  } else {
    var deaktiviert=false;
  }
  document.adminForm.mode.disabled=deaktiviert;
  document.adminForm.overview.disabled=deaktiviert;
  document.adminForm.joker0.disabled=deaktiviert;
  document.adminForm.joker1.disabled=deaktiviert;
  document.adminForm.joker_limit_select0.disabled=deaktiviert;
  document.adminForm.joker_limit_select1.disabled=deaktiviert;
  document.adminForm.champ0.disabled=deaktiviert;
  document.adminForm.champ1.disabled=deaktiviert;

  document.adminForm.points_correct_result.disabled=deaktiviert;
    document.adminForm.points_correct_result_joker.disabled=deaktiviert;
  document.adminForm.points_correct_diff.disabled=deaktiviert;
    document.adminForm.points_correct_diff_joker.disabled=deaktiviert;
  document.adminForm.points_correct_draw.disabled=deaktiviert;
    document.adminForm.points_correct_draw_joker.disabled=deaktiviert;
  document.adminForm.points_correct_tendence.disabled=deaktiviert;
    document.adminForm.points_correct_tendence_joker.disabled=deaktiviert;
  document.adminForm.points_tipp.disabled=deaktiviert;
    document.adminForm.points_tipp_joker.disabled=deaktiviert;

    document.adminForm.joker_limit.disabled=deaktiviert;

    document.adminForm.points_tipp_champ.disabled=deaktiviert;

  if (deaktiviert == false){
  change_joker();
  change_jokerlimit();
  change_champ();
}
}

function change_joker () {
  if (document.adminForm.joker0.checked == true) {
    var deaktiviert=true;
  } else {
    var deaktiviert=false;
  }
  document.adminForm.points_correct_result_joker.disabled=deaktiviert;
  document.adminForm.points_correct_diff_joker.disabled=deaktiviert;
  document.adminForm.points_correct_draw_joker.disabled=deaktiviert;
  document.adminForm.points_correct_tendence_joker.disabled=deaktiviert;
  document.adminForm.points_tipp_joker.disabled=deaktiviert;
}

function change_jokerlimit () {
  if (document.adminForm.joker_limit_select0.checked == true) {
    var deaktiviert=true;
  } else {
    var deaktiviert=false;
  }
  document.adminForm.joker_limit.disabled=deaktiviert;
}

function change_champ () {
  if (document.adminForm.champ0.checked == true) {
    var deaktiviert=true;
  } else {
    var deaktiviert=false;
  }
  document.adminForm.points_tipp_champ.disabled=deaktiviert;
  document.adminForm.league_champ.disabled=deaktiviert;
}

</script>

<form method='post' name='adminForm' id='adminForm'>
	<div class='col50'>
		<?php
				$p = 1;
		echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
		echo $this->loadTemplate('details');
		echo HTMLHelper::_('bootstrap.endTab');
		
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p++,Text::_('COM_JOOMLEAGUE_TABS_HELP',true));
		echo $this->loadTemplate('help');
		echo HTMLHelper::_('bootstrap.endTab');
		
		echo HTMLHelper::_('bootstrap.endTabSet');
		?>

		<div class='clr'></div>
		<input type='hidden' name='old_points_tipp_champ'				value='<?php echo $this->pred_project->points_tipp_champ; ?>' />

		<input type='hidden' name='old_points_tipp_joker'				value='<?php echo $this->pred_project->points_tipp_joker; ?>' />
		<input type='hidden' name='old_points_correct_result_joker'		value='<?php echo $this->pred_project->points_correct_result_joker; ?>' />
		<input type='hidden' name='old_points_correct_diff_joker'		value='<?php echo $this->pred_project->points_correct_diff_joker; ?>' />
		<input type='hidden' name='old_points_correct_draw_joker'		value='<?php echo $this->pred_project->points_correct_draw_joker; ?>' />
		<input type='hidden' name='old_points_correct_tendence_joker'	value='<?php echo $this->pred_project->points_correct_tendence_joker; ?>' />


		<input type='hidden' name='prediction_id'			value='<?php echo $this->pred_project->prediction_id; ?>' />
		<input type='hidden' name='project_id'				value='<?php echo $this->pred_project->project_id; ?>' />
		<input type='hidden' name='option'					value='com_joomleague' />
		<input type='hidden' name='controller'				value='predictiongame' />
		<input type='hidden' name='cid[]'					value='<?php echo $this->pred_project->id; ?>' />
		<input type='hidden' name='task'					value='' />
		<input type='hidden' name='psapply'					value='1' />
	</div>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
<script type="text/javascript">change_published();</script>