<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
jimport('joomla.filesystem.file');

$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$inplaceEditing = $this->params->get('inplaceEditing',0);
$inplaceEditing = 1;

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == - 2 ? true : false;
$saveOrder = $listOrder == 'a.ordering';
if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomleague&task=persons.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable','personList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
?>
<style>
.icon-ok::before {
	color: white;
}
</style>
<script>
jQuery(document).ready(function() {
    jQuery.fn.editable.defaults.mode = 'popup';

    jQuery('[id^="firstname_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=persons.saveshortAjax',
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

    jQuery('[id^="nickname_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=persons.saveshortAjax',
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

    jQuery('[id^="lastname_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=persons.saveshortAjax',
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
		    validate: function(value) {
		        if(jQuery.trim(value) == '') {
		            return 'This field is required';
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
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=persons'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">
	<?php
	// Search tools bar
	echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
	?>
	<div class="btn-wrapper pull-right">
		<div style="max-width: 700px; overflow: auto; float: right">
		<?php
		$startRange = hexdec($this->component_params->get('character_filter_start_hex','0041'));
		$endRange = hexdec($this->component_params->get('character_filter_end_hex','005A'));
		for($i = $startRange;$i <= $endRange;$i ++)
		{
			printf("<a href=\"javascript:searchPerson('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
		}
		?>
		</div>
	</div>
	</div>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
			<div id="j-main-container" class="j-main-container">
	<!-- Rows -->
	<table class="table table-striped persons" id="personList">
		<thead>
			<tr>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
				</th>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_F_NAME','a.firstname',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_N_NAME','a.nickname',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_L_NAME','a.lastname',$listDirn, $listOrder);?>
				</th>
				<th class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_IMAGE'); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_BIRTHDAY','a.birthday',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_NATIONALITY','a.country',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_POSITION','a.position_id',$listDirn, $listOrder);?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED');?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder);?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$n = count($this->items);
		foreach($this->items as $i=>$row) :
		if(($row->firstname != '!Unknown') && ($row->lastname != '!Player')) // Ghostplayer for match-events
		{
			$link = Route::_('index.php?option=com_joomleague&task=person.edit&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$is_checked = JLTable::_isCheckedOut($user->get('id'),$row->checked_out);
			$published = HTMLHelper::_('jgrid.published',$row->published,$i,'persons.');

			$canEdit = $user->authorise('core.edit','com_joomleague.person.'.$row->id);
			$canCheckin = $user->authorise('core.manage','com_checkin') || $row->checked_out == $userId || $row->checked_out == 0;
			//$canEditOwn = $user->authorise('core.edit.own','com_joomleague.person.'.$row->id) && $row->created_by == $userId;
			$canChange = $user->authorise('core.edit.state','com_joomleague.person.'.$row->id) && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
				<?php
					$iconClass = '';
				if (!$canChange) {
					$iconClass = ' inactive';
				} elseif (!$saveOrder) {
					$iconClass = ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText ('JORDERINGDISABLED');
				}
				?>
					<span class="sortable-handler<?php echo $iconClass ?>"><span class="icon-menu"></span></span>
				<?php if ($canChange && $saveOrder) : ?>
					<input type="text" style="display: none" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-20 text-area-order " />
				<?php endif; ?>
				</td>
				<td class="center"><?php echo $checked; ?></td>
				<?php
				if($is_checked)
				{
					$inputappend = ' disabled="disabled" ';
				?>
				<td class="center">&nbsp;</td>
				<?php
				}
				else
				{
					$inputappend = '';
				?>
				<td class="center">
					<a href="<?php echo $link; ?>">
				<?php
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_EDIT_DETAILS');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
				?>
					</a>
				</td>
				<?php
				}
				?>
				<td>
					<?php 
					if ($inplaceEditing == '0') {
					?>
					<input	<?php echo $inputappend; ?> type="text" size="15" class="inputbox" name="firstname<?php echo $row->id; ?>"
					value="<?php echo stripslashes(htmlspecialchars($row->firstname)); ?>"
					onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" />
						<?php
					} elseif ($inplaceEditing == '1') {
					?>
					<a href="javascript:void(0);" id="firstname_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="firstname<?php echo $row->id; ?>">
						<?php echo $row->firstname; ?>
					</a>
					<?php	
					} 
					?>
				</td>
				<td>
					<?php 
					if ($inplaceEditing == '0') {
					?>
					<input	<?php echo $inputappend; ?> type="text" size="15" class="inputbox" name="nickname<?php echo $row->id; ?>"
					value="<?php echo stripslashes(htmlspecialchars($row->nickname)); ?>"
					onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" />
					<?php 
					} elseif ($inplaceEditing == '1') {
					?>
					<a href="javascript:void(0);" id="nickname_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="nickname<?php echo $row->id; ?>">
						<?php echo stripslashes(htmlspecialchars($row->nickname)); ?>
					</a>
					<?php 
					}
					?>
				</td>
				<td>
					<?php 
					if ($inplaceEditing == '0') {
					?>
					<input	<?php echo $inputappend; ?> type="text" size="15" class="inputbox" name="lastname<?php echo $row->id; ?>"
					value="<?php echo stripslashes(htmlspecialchars($row->lastname)); ?>"
					onchange="document.getElementById('cb<?php echo $i; ?>').checked=true" />
					<?php 
					} elseif ($inplaceEditing == '1') {
					?>
					<a href="javascript:void(0);" id="lastname_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="lastname<?php echo $row->id; ?>">
						<?php echo stripslashes(htmlspecialchars($row->lastname)); ?>
					</a>
					<?php 
					}
					?>
				</td>
				<td class="center image">
				<?php
				if(empty($row->picture) || ! File::exists(JPATH_SITE.'/'.$row->picture))
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_NO_IMAGE').$row->picture;
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "'.$imageTitle.'"');
				}
				elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player"))
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_DEFAULT_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "'.$imageTitle.'"');
				}
				else
				{
					if(File::exists(JPATH_SITE.'/'.$row->picture))
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TEAMS_CUSTOM_IMAGE');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/ok.png',$imageTitle,'title= "'.$imageTitle.'"');

								/*
								 * $playerName =
								 * JoomleagueHelper::formatName(null
								 * ,$row->firstname, $row->nickname,
								 * $row->lastname, 0);
								 * echo
								 * JoomleagueHelper::getPictureThumb($row->picture,
								 * $playerName, 0, 21, 4);
								 */
					}
				}
				?>
				</td>
				<td class="nowrap" class="center">
				<?php
				/*
				$append = 'style="float: left; margin: 5px 5px 5px 0;"';
				if($row->birthday == '0000-00-00')
				{
					$date = '';
					$append = 'style="background-color:#FFCCCC; float: left; margin: 5px 5px 5px 0;"';
				}
				else
				{
					$date = HTMLHelper::date($row->birthday,'Y-m-d',true);
				}
				if($is_checked)
				{
					echo $row->birthday;
				}
				else
				{
					echo $this->calendar($date,'birthday'.$row->id,'birthday'.$row->id,'%Y-%m-%d','size="10" '.$append.' cb="cb'.$i.'"','onupdatebirthday',$i);
				}
				*/

				if($row->birthday == '0000-00-00' || $row->birthday == '1970-01-01')
				{
					$date = '';
				}
				else
				{
					$date = HTMLHelper::date($row->birthday,'Y-m-d',true);
				}
				echo $date;
				?>
				</td>
				<td class="nowrap" class="center">
				<?php
					$append = '';
				if(empty($row->country))
				{
					$append = ' background-color:#FFCCCC;';
				}
				echo HTMLHelper::_('select.genericlist', $this->lists['nation'],'country'.$row->id,
					$inputappend . ' class="inputbox" style="width:140px; '.$append.'" onchange="document.getElementById(\'cb'.$i.'\').checked=true"','value','text',$row->country);
						?>
							</td>
				<td class="nowrap" class="center">
				<?php
					$append = '';
					if(empty($row->position_id))
					{
						$append = ' background-color:#FFCCCC;';
					}
					echo HTMLHelper::_('select.genericlist', $this->lists['positions'],'position'.$row->id,
					$inputappend . 'class="inputbox" style="width:140px; '.$append.'" onchange="document.getElementById(\'cb'.$i.'\').checked=true"','value','text',$row->position_id);
				?>
				</td>
				<td class="center"><?php echo $published; ?></td>
				<td class="center"><?php echo $row->id; ?></td>
			</tr>
			<?php
			}
			endforeach ;
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='12'></td>
			</tr>
		</tfoot>
	</table>
	<?php endif; ?>
	<?php echo $this->pagination->getListFooter(); ?>
	</div>
	<!-- Input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="inplaceEditing" value="<?php echo $inplaceEditing;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
