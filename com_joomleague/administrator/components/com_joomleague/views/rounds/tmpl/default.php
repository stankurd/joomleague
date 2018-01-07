<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;
HTMLHelper::_('behavior.tooltip');

$inplaceEditing = $this->params->get('inplaceEditing',0);
$inplaceEditing = 1;

?>
<script>
jQuery(document).ready(function() {
    jQuery.fn.editable.defaults.mode = 'popup';

    jQuery('[id^="roundcode_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=rounds.saveshortAjax',
    	    ajaxOptions: {
    	        type: 'post',
    	        dataType: 'json'
    	    },
            success: function(response, newValue)
            {
             	if(response.status == 'error') {
                 	return response.msg; //msg will be shown in editable form
             	}
			},
			highlight: '#DEDEDE',
		    emptytext: '<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_EMPTY'); ?>',
		    params: function(params) {
		        // originally params contain pk, name and value
		        params.token = '<?php echo Session::getFormToken();?>';
		        params.tokenvalue = '1';
		        return params;
		    }
    	});
    });
    jQuery('[id^="name_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=rounds.saveshortAjax',
    	    ajaxOptions: {
    	        type: 'post',
    	        dataType: 'json'
    	    },
            success: function(response, newValue)
            {
             	if(response.status == 'error') {
                 	return response.msg; //msg will be shown in editable form
             	}
			},
			highlight: '#DEDEDE',
		    emptytext: '<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_EMPTY'); ?>',
		    params: function(params) {
		        // originally params contain pk, name and value
		        params.token = '<?php echo Session::getFormToken();?>';
		        params.tokenvalue = '1';
		        return params;
		    }
    	});
    });
});
</script>
<style>
.icon-ok::before {
	color: white;
}
</style>
<?php
if($this->project->project_type == 'DIVISIONS_LEAGUE')
{
	?>
<div id='populate_enter_division' style='display:<?php echo ($this->populate == 0) ? 'none' : 'block'; ?>'>
	<fieldset class='adminform'>
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
		<form action="<?php echo $this->request_url; ?>" method="post" style="display: inline" id="enterdivision">
			<table class='admintable'>
				<tbody>
					<tr>
						<td class='key' nowrap='nowrap'><?php echo Text::_('COM_JOOMLEAGUE_P_MENU_DIVISIONS'); ?></td>
						<td>
				<?php
		echo HTMLHelper::_('select.genericlist',$this->lists['divisions'],'division_id','class="inputbox" size="1"','value','text',0);
	?>

						<td>
							<input type='submit' class='button' value='<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_BUTTON'); ?>' onclick='this.form.submit();' />
						</td>
					</tr>
				</tbody>
			</table>
			<input type='hidden' name='project_id' value='<?php echo $this->project->id; ?>' />
			<input type='hidden' name='task' value='rounds.populate' />
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</fieldset>
</div>
<?php
}
?>
<div id='alt_massadd_enter' style='display:<?php echo ($this->massadd) ? 'block' : 'none'; ?>'>
	<form id='copyform' method='post' style='display: inline'>
		<fieldset class='form-horizontal'>
			<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_DESC'); ?>
			<br>
			<legend>
				<?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_LEGEND','<i>'.$this->project->name.'</i>'); ?>
			</legend>
			<div class='control-group'>
				<div class='control-label'>
					<label for='mass_add_method'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_SCHEDULE'); ?></label>
				</div>
				<div class='controls'>
					<?php echo $this->lists['roundscheduling']; ?>
				</div>
			</div>
			<div id='interval_method' style='display: block;'>
				<div class='control-group'>
					<div class='control-label'>
						<label for='add_round_count'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_COUNT'); ?></label>
					</div>
					<div class='controls'>
						<input type='number' id='add_round_count' name='add_round_count'
							value='0' size='3' style='width: 50px;' class='inputbox'>
					</div>
				</div>
				<div class='control-group'>
					<div class='control-label'>
						<label for='start_date'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_START_DATE'); ?></label>
					</div>
					<div class='controls'>
						<?php
						$now = new DateTime('now',new DateTimeZone('UTC'));
						echo HTMLHelper::calendar($now->format('d-m-Y'),'start_date','start_date','%d-%m-%Y',
								'size="10" style="width:80px;" class="center" ');
						?>
					</div>
				</div>
				<div class='control-group'>
					<div class='control-label'>
						<label for='interval'><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_INTERVAL'); ?></label>
					</div>
					<div class='controls'>
						<input type='number' id='interval' name='interval' value='7'
							style='width: 50px'>
					</div>
				</div>
			</div>
			<input type='submit' class='button' value='<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_MASSADD_SUBMIT_BUTTON'); ?>' onclick='this.form.submit();' />
			<input type='hidden' name='project_id' value='<?php echo $this->project->id; ?>' />
			<input type='hidden' name='task' value='rounds.startmassadd' />
		</fieldset>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="adminform">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
			<table class="table table-striped" id="roundslist">
				<thead>
					<tr>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('grid.checkall'); ?>
						</th>
						<th width="20">&nbsp;</th>
						<th width="1%">
							<?php echo Text::_('#'); ?></th>
						<th>
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_ROUND_TITLE'); ?></th>
						<th width="10%">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_STARTDATE'); ?></th>
						<th width="1%">&nbsp;</th>
						<th width="10%">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_ENDDATE'); ?></th>
						<th width="10%">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_EDIT_MATCHES'); ?></th>
						<th width="20">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_PUBLISHED_CHECK'); ?></th>
						<th width="20">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_RESULT_CHECK'); ?></th>
						<th width="1%">
							<?php echo HTMLHelper::_('grid.sort', 'COM_JOOMLEAGUE_GLOBAL_ID', 'a.id',$this->lists['order_Dir'],$this->lists['order']); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$n = count($this->items);
					foreach($this->items as $i=>$row) :
						$link1 = Route::_('index.php?option=com_joomleague&task=round.edit&id='.$row->id);
						$link2 = Route::_('index.php?option=com_joomleague&view=matches&rid[]='.$row->id);
						$checked = HTMLHelper::_('grid.checkedout',$row,$i);
						?>
						<tr class="row<?php echo $i % 2; ?>">
						<td class="center"><?php echo $checked; ?></td>
						<td class="center"><?php
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_EDIT_DETAILS');
						$imageFile = 'administrator/components/com_joomleague/assets/images/edit.png';
						$imageParams = "title='$imageTitle'";
						echo HTMLHelper::link($link1,HTMLHelper::image($imageFile,$imageTitle,$imageParams));
						?></td>
						<td class="center">
							<?php 
							if ($inplaceEditing == '0') {
							?>
							<input tabindex="1" type="text" style="text-align: center" size="5" class="inputbox" 
							name="roundcode<?php echo $row->id; ?>" value="<?php echo $row->roundcode; ?>" 
							onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" />
							<?php
							} elseif ($inplaceEditing == '1') {
							?>	
							<a href="javascript:void(0);" id="roundcode_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="roundcode<?php echo $row->id; ?>">
								<?php echo stripslashes(htmlspecialchars($row->roundcode)); ?>
							</a>
							<?php	
							} 
							?>
						</td>
						<td>
							<?php 
							if ($inplaceEditing == '0') {
							?>
							<input tabindex="2" type="text" size="30" maxlength="64" class="inputbox" 
							name="name<?php echo $row->id; ?>" value="<?php echo $row->name; ?>" 
							onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" />
							<?php
							} elseif ($inplaceEditing == '1') {
							?>
							<a href="javascript:void(0);" id="name_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="name<?php echo $row->id; ?>">
								<?php echo stripslashes(htmlspecialchars($row->name)); ?>
							</a>
							<?php	
							} 
							?>
						</td>
						<td class="center">
								<?php
						$date1 = JoomleagueHelper::convertDate($row->round_date_first,1);
						$append = '';
						if(($date1 == '00-00-0000') || ($date1 == ''))
						{
							$append = ' style="background-color:#FFCCCC;" ';
						}
						echo HTMLHelper::calendar($date1,'round_date_first' . $row->id,'round_date_first' . $row->id,'%d-%m-%Y',
								'size="10" ' . $append . 'tabindex="3" ' . 'class="input-small center" ' . 'onchange="document.getElementById(\'cb' . $i .
										 '\').checked=true"');
						?>
							</td>
						<td class="center">&nbsp;-&nbsp;</td>
						<td class="center"><?php
						$date2 = JoomleagueHelper::convertDate($row->round_date_last,1);
						$append = '';
						if(($date2 == '00-00-0000') || ($date2 == ''))
						{
							$append = ' style="background-color:#FFCCCC;"';
						}
						echo HTMLHelper::calendar($date2,'round_date_last' . $row->id,'round_date_last' . $row->id,'%d-%m-%Y',
								'size="10" ' . $append . 'tabindex="3" ' . 'class="input-small center" ' . 'onchange="document.getElementById(\'cb' . $i .
										 '\').checked=true"');
						?></td>
						<td><?php
						if($this->countProjectTeams > 0)
						{
							$link2Title = Text::plural('COM_JOOMLEAGUE_ADMIN_ROUNDS_EDIT_MATCHES_LINK',$row->countMatches);
							echo HTMLHelper::link($link2,$link2Title);
						}
						else
						{
							echo '<a href="index.php?option=com_joomleague&view=projectteams">' . Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_NO_TEAMS') .
									 '</a>';
						}
						?></td>
						<td class="center"><?php
						if(($row->countUnPublished == 0) && ($row->countMatches > 0))
						{
							$imageTitle = Text::plural('COM_JOOMLEAGUE_ADMIN_ROUNDS_ALL_PUBLISHED',$row->countMatches);
							$imageFile = 'administrator/components/com_joomleague/assets/images/ok.png';
							$imageParams = "title='$imageTitle'";
							echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
						}
						else
						{
							if($row->countMatches == 0)
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_ANY_MATCHES');
							}
							else
							{
								$imageTitle = Text::plural('COM_JOOMLEAGUE_ADMIN_ROUNDS_PUBLISHED_NR',$row->countUnPublished);
							}
							$imageFile = 'administrator/components/com_joomleague/assets/images/error.png';
							$imageParams = "title='$imageTitle'";
							echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
						}
						?></td>
						<td class="center nowrap"><?php
						if(($row->countNoResults == 0) && ($row->countMatches > 0))
						{
							$imageTitle = Text::plural('COM_JOOMLEAGUE_ADMIN_ROUNDS_ALL_RESULTS',$row->countMatches);
							$imageFile = 'administrator/components/com_joomleague/assets/images/ok.png';
							$imageParams = "title='$imageTitle'";
							echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
						}
						else
						{
							if($row->countMatches == 0)
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_ANY_MATCHES');
							}
							else
							{
								$imageTitle = Text::plural('COM_JOOMLEAGUE_ADMIN_ROUNDS_RESULTS_MISSING',$row->countNoResults);
							}
							$imageFile = 'administrator/components/com_joomleague/assets/images/error.png';
							$imageParams = "title='$imageTitle'";
							echo HTMLHelper::image($imageFile,$imageTitle,$imageParams);
						}
						?></td>
						<td class="center"><?php echo $row->id; ?></td>
					</tr>
						<?php endforeach; ?>
				</tbody>
			</table>
		</fieldset>
	<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
	<input type="hidden" name="inplaceEditing" value="<?php echo $inplaceEditing;?>" />
	<input type="hidden" name="next_roundcode" value="<?php echo count($this->items) + 1; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>