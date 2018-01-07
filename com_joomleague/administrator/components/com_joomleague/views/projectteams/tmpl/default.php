<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * A piece of com_finder was taken for the quickadd
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('jquery.framework');


$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$currentDivision = $this->escape($this->state->get('filter.division'));

$saveOrder = $listOrder == 'a.ordering';
if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomleague&task=projectteams.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable','projectteamList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
// load navigation menu
// $this->addTemplatePath(JPATH_COMPONENT . '/views/joomleague');
// $saveOrder = ($this->lists['order'] == 'a.ordering');
?>
<?php
$script = "
jQuery(document).ready(function() {
	var value, searchword = jQuery('#quickadd');

		// Set the input value if not already set.
		if (!searchword.val())
		{
			searchword.val('" . Text::_('Search',true) . "');
		}

		// Get the current value.
		value = searchword.val();

		// If the current value equals the default value, clear it.
		searchword.on('focus', function()
		{	var el = jQuery(this);
			if (el.val() === '" . Text::_('Search',true) . "')
			{
				el.val('');
			}
		});

		// If the current value is empty, set the previous value.
		searchword.on('blur', function()
		{	var el = jQuery(this);
			if (!el.val())
			{
				el.val(value);
			}
		});

		jQuery('#quickaddForm').on('submit', function(e){
			e.stopPropagation();
		});";

/*
 * @todo Change text // 24-07-2015
 * At the moment only passing a name is showing results
 * This segment of code sets up the autocompleter.
 */
HTMLHelper::_('script','media/com_joomleague/autocomplete/jquery.autocomplete.min.js',false,false,false,false,true);

$script .= "
	var suggest = jQuery('#quickadd').autocomplete({
		serviceUrl: '".Route::_('index.php?option=com_joomleague&task=quickadd.searchteam&project_id='.$this->project->id,false)."',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";

$script .= "});";

Factory::getDocument()->addScriptDeclaration($script);
?>
<script>
jQuery(document).ready(function() {
    // toggle mode: popup/inline
    jQuery.fn.editable.defaults.mode = 'popup';

    jQuery('[id^="start_points"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="matches_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="points_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="neg_points_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="won_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="draws_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="lost_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="homegoals_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="guestgoals_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
    jQuery('[id^="diffgoals_finally"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=projectteams.saveshortAjax',
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
    	});
    });
});
</script>
<style>
.icon-ok::before {
	color: white;
}
</style>
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_QUICKADD_TEAM');?></legend>
	<form id="quickaddForm" action="<?php echo Route::_(Uri::root().'administrator/index.php?option=com_joomleague&task=quickadd.addteam'); ?>" method="post">
		<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_QUICKADD_DESCR'); ?>
		<div id="j-main-container" class="j-main-container">
		<div class="btn-wrapper input-append pull-left">
			<input type="text" name="p" id="quickadd" size="50" value="<?php htmlspecialchars(Factory::getApplication()->input->getString('q',false)); ?>" />
			<input class="btn" type="submit" name="submit" id="submit" value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ADD');?>" />
		</div>
		<input type="hidden" name="project_id" id="project_id" value="<?php echo $this->project->id; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</fieldset>
<br/>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projectteams'); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="form-horizontal">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_LEGEND','<i>'.$this->project->name.'</i>'); ?></legend>
			<?php
			// Search tools bar
			echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
			?>
			<?php $cell_count=22; ?>
			<div id="j-main-container" class="j-main-container">
			<table class="table table-striped" id="projectteamList">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_TEAMNAME','t.name',$listDirn, $listOrder); ?>
					</th>
					<th colspan="2" class="center">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_MANAGE_PERSONNEL'); ?>
					</th>
					<?php
					if($this->project->project_type == 'DIVISIONS_LEAGUE')
					{
						$cell_count ++;
					?>
					<th>
					<?php
						echo $this->lists['divisions'];
					?>
					</th>
					<?php
					}
					?>
					<th class="center">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_PICTURE'); ?>
					</th>
					<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_INITIAL_POINTS'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_MA'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_PLUS_P'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_MINUS_P'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_W'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_D'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_L'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_HG'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_GG'); ?></th>
					<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_DG'); ?></th>
					<th width="1%">
						<?php echo HTMLHelper::_('searchtools.sort','TID','team_id',$listDirn, $listOrder); ?>
					</th>
					<th width="1%">
						<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="<?php echo $cell_count; ?>"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$n = count($this->items);
			foreach($this->items as $i=>$row) :
				$link1 = Route::_('index.php?option=com_joomleague&task=projectteam.edit&id='.$row->id);
				$link2 = Route::_('index.php?option=com_joomleague&task=teamplayers.select&project_team_id='.$row->id."&team_id=".$row->team_id.'&pid='.$this->project->id);
				$link3 = Route::_('index.php?option=com_joomleague&task=teamstaffs.select&project_team_id='.$row->id."&team_id=".$row->team_id.'&pid='.$this->project->id);
				$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center"><?php echo $checked;?></td>
					<?php
					if(JLTable::_isCheckedOut($user->get('id'),$row->checked_out))
					{
						$inputappend = ' disabled="disabled"';
					?>
					<td class="center">&nbsp;</td>
					<?php
					}
					else
					{
						$inputappend = '';
					?>
					<td class="center">
					<?php
						$imageFile = 'administrator/components/com_joomleague/assets/images/edit.png';
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_EDIT_DETAILS');
						$imageParams = 'title= "' . $imageTitle . '"';
						$image = HTMLHelper::image($imageFile,$imageTitle,$imageParams);
						$linkParams = '';
						echo HTMLHelper::link($link1,$image);
					?>
					</td>
					<?php
					}
					?>
					<td><?php echo $row->teamname;?></td>
					<td class="center">
					<?php
					if($row->playercount == 0)
					{
						$image = "players_add.png";
					}
					else
					{
						$image = "players_edit.png";
					}
					$imageFile = 'administrator/components/com_joomleague/assets/images/' . $image;
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_MANAGE_PLAYERS');
					$imageParams = 'title= "' . $imageTitle . '"';
					$image = HTMLHelper::image($imageFile,$imageTitle,$imageParams) . ' <sub>' . $row->playercount . '</sub>';
					$linkParams = '';
					echo HTMLHelper::link($link2,$image);
					?>
					</td>
					<td class="center">
					<?php
					if($row->staffcount == 0)
					{
						$image = "players_add.png";
					}
					else
					{
						$image = "players_edit.png";
					}
					$imageFile = 'administrator/components/com_joomleague/assets/images/' . $image;
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_MANAGE_STAFF');
					$imageParams = 'title= "' . $imageTitle . '"';
					$image = HTMLHelper::image($imageFile,$imageTitle,$imageParams) . ' <sub>' . $row->staffcount . '</sub>';
					$linkParams = '';
					echo HTMLHelper::link($link3,$image);
					?>
					</td>
					<?php
					if($this->project->project_type == 'DIVISIONS_LEAGUE')
					{
					?>
					<td class="nowrap" class="center">
					<?php
					$append = '';
					if($row->division_id == 0)
					{
						$append = ' style="background-color:#bbffff"';
					}
					echo HTMLHelper::_('select.genericlist',$this->divisions,'division_id'.$row->id,
						$inputappend.'class="input-medium" size="1" onchange="document.getElementById(\'cb'.$i.'\').checked=true"' .
						$append,'value','text',$row->division_id);
					?>
					</td>
					<?php
					}
					?>
					<td class="center">
					<?php
					if(empty($row->picture) || ! JFile::exists(JPATH_SITE . '/' . $row->picture))
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_NO_IMAGE') . $row->picture;
						echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "'.$imageTitle.'"');
					}
					elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("team"))
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_DEFAULT_IMAGE');
						echo HTMLHelper::image('administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "'.$imageTitle.'"');
					}
					else
					{
						if(JFile::exists(JPATH_SITE.'/'.$row->picture))
						{
							$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TEAMS_CUSTOM_IMAGE');
							echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/ok.png',$imageTitle,'title= "'.$imageTitle.'"');
						}
						/*
						 * $imageTitle=Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_CUSTOM_IMAGE');
						 * $imageParams=array();
						 * $imageParams['title']=$imageTitle ;
						 * $imageParams['height']=30;
						 * //$imageParams['width'] =40;
						 * echo
						 * HTMLHelper::image($row->picture,$imageTitle,$imageParams);
						 */
					}
					?>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="start_points_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="start_points<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->start_points)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="matches_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="matches_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->matches_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="points_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="points_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->points_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="neg_points_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="neg_points_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->neg_points_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="won_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="won_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->won_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="draws_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="draws_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->draws_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="lost_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="lost_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->lost_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="homegoals_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="homegoals_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->homegoals_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="guestgoals_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="guestgoals_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->guestgoals_finally)); ?>
						</a>
					</td>
					<td class="center">
						<a href="javascript:void(0);" id="diffgoals_finally_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="diffgoals_finally<?php echo $row->id; ?>">
							<?php echo stripslashes(htmlspecialchars($row->diffgoals_finally)); ?>
						</a>
					</td>
					<td class="center" width="1%">
					<?php
						$team_edit_link = Route::_('index.php?option=com_joomleague&task=team.edit&id='.$row->team_id.'&return=projectteams');
					?>
						<a href="<?php echo $team_edit_link ?>">
							<?php echo $row->team_id; ?>
						</a>
					</td>
					<td class="center" width="1%"><?php echo $row->id; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</fieldset>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
