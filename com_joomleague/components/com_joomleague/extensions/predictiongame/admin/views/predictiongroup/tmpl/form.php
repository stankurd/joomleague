<?php defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

?>
<form action="index.php" method="post" id="adminForm">
<fieldset class="adminform">
			<legend>
      <?php 
      echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PREDICTIONGROUP_LEGEND_DESC','<i>'.$this->season->name.'</i>'); 
      ?>
      </legend>
	<div class="col50">
<?php
$p = 1;
		echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
		echo $this->loadTemplate('details');
		echo HTMLHelper::_('bootstrap.endTab');
		echo HTMLHelper::_('bootstrap.endTabSet');
?>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="option" value="com_joomleague" />
	<input type="hidden" name="cid[]" value="<?php echo $this->season->id; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token')."\n"; ?>
</fieldset>		
</form>