<?php use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;
?>
<form name='adminForm' id='adminForm' method='post'>
	<?php $dateformat='%d-%m-%Y'; ?>
	<table width='100%' border='0' cellpadding='0' cellspacing='0'>
		<tr>
			<td><?php
				echo HTMLHelper::calendar(JoomleagueHelper::convertDate($this->startdate,1),'startdate','startdate',$dateformat);
				echo ' - '.HTMLHelper::calendar(JoomleagueHelper::convertDate($this->enddate,1),'enddate','enddate',$dateformat);
				?><input type='submit' class='button' name='reload View' value='<?php echo JText::_('COM_JOOMLEAGUE_GLOBAL_FILTER'); ?>' /></td>
			<td><?php
			if($this->club)
			{
				echo JoomleagueHelper::getPictureThumb($this->club->logo_middle, $this->club->name, 50, 50, 2);
			}
			?></td>
		</tr>
	</table>
	<?php echo HTMLHelper::_('form.token'); ?>
</form><br />