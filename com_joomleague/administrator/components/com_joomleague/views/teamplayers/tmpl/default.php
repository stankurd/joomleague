<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * Autocomplete is using code from mod_finder
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$archived = $this->state->get('filter.published') == 2 ? true : false;
$trashed = $this->state->get('filter.published') == - 2 ? true : false;
$saveOrder = $listOrder == 'a.ordering';

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('jquery.framework');
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

HTMLHelper::_('script','media/com_joomleague/autocomplete/jquery.autocomplete.min.js',false,false,false,false,true);

$script .= "
	var suggest = jQuery('#quickadd').autocomplete({
		serviceUrl: '" . Route::_('index.php?option=com_joomleague&task=quickadd.searchplayer&projectteam_id=' . $this->projectteam->id,false) . "',
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
    jQuery.fn.editable.defaults.mode = 'popup';

    jQuery('[id^="jerseynumber_"]').each(function() {
    	jQuery(this).editable({
    		url: 'index.php?option=com_joomleague&task=teamplayers.saveshortAjax',
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
<!-- Quickadd form -->
<fieldset class="adminform">
	<legend><?php echo Text::_("COM_JOOMLEAGUE_ADMIN_TEAMPLAYERS_QUICKADD_PLAYER");?></legend>
	<form id="quickaddForm" action="<?php echo Uri::root(); ?>administrator/index.php?option=com_joomleague&task=quickadd.addplayer" method="post">
		<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_QUICKADD_DESCR'); ?>
		<div class="clearfix"></div>
		<div class="btn-wrapper input-append pull-left">
			<input type="text" name="p" id="quickadd" size="50" value="<?php htmlspecialchars(Factory::getApplication()->input->getString('q',false)); ?>" />
			<input class="btn" type="submit" name="submit" id="submit" value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ADD');?>" />
		</div>
		<input type="hidden" name="projectteam_id" id="projectteam_id" value="<?php echo $this->projectteam->id; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</fieldset>
<!-- main form -->
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=teamplayers'); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="adminform">
		<legend>
			<?php
			echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TPLAYERS_TITLE2','<i>'.$this->projectteam->name.'</i>',
			'<i>'.$this->project->name.'</i>');
			?>
		</legend>
	<div class="clearfix">
	<?php
	// Search tools bar
	echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
	?>
	<div class="btn-wrapper pull-right">
	<?php
		for($i = 65;$i < 91;$i ++)
		{
			printf("<a href=\"javascript:searchPlayer('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
		}
	?>
	</div></div>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
	<table class="table table-striped" id="teamplayerList">
		<thead>
			<tr>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_TPLAYERS_NAME','a.lastname',$listDirn, $listOrder);?>
				</th>
				<th class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_IMAGE');?>
				</th>
				<th width="20" class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_SHIRTNR');?>
				</th>
				<th width="20">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_TPLAYERS_POS','tp.project_position_id',$listDirn, $listOrder);?>
				</th>
				<th>
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_STATUS');?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED');?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','PID','tp.person_id',$listDirn, $listOrder);?>
				</th>
				<th width="1%">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','tpid',$listDirn, $listOrder);?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$n = count($this->items);
		foreach($this->items as $i=>$row):
			$link = Route::_('index.php?option=com_joomleague&task=teamplayer.edit&projectteam='.$row->projectteam_id.'&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$inputappend = '';
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $checked;?></td>
				<?php
				if(JLTable::_isCheckedOut($this->user->get('id'),$row->checked_out))
				{
					$inputappend = ' disabled="disabled"';
				?>
				<td>&nbsp;</td>
				<?php
				}
				else
				{
				?>
				<td class="center">
					<a href="<?php echo $link; ?>">
				<?php
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_EDIT_DETAILS');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
				?>
					</a>
				</td>
				<?php
				}
				?>
				<td><?php echo JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,JoomleagueHelper::defaultNameFormat());?></td>
				<td class="center">
				<?php
				if($row->picture == '')
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_NO_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "' . $imageTitle . '"');
				}
				elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player"))
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_DEFAULT_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "' . $imageTitle . '"');
				}
				elseif($row->picture == ! '')
				{
					echo JoomleagueHelper::getPictureThumb($row->picture,false,0,21,4);
				}
				?>
				</td>
				<td class="center">
					<a href="javascript:void(0);" id="jerseynumber_<?php echo $row->id; ?>" data-type="text" data-pk="<?php echo $row->id; ?>" data-title="jerseynumber<?php echo $row->id; ?>">
						<?php echo stripslashes(htmlspecialchars($row->jerseynumber)); ?>
					</a>
				</td>
				<td class="nowrap" class="center">
				<?php
				if($row->project_position_id != 0)
				{
					$selectedvalue = $row->project_position_id;
					$append = '';
				}
				else
				{
					$selectedvalue = 0;
					$append = ' style="background-color:#FFCCCC"';
				}
				if($append != '')
				{
				?>
					<script>document.getElementById('cb<?php echo $i; ?>').checked=true;</script>
				<?php
				}
				if($row->project_position_id == 0)
				{
					$append = ' style="background-color:#FFCCCC"';
				}
					echo HTMLHelper::_('select.genericlist',$this->lists['project_position_id'],'project_position_id'.$row->id,
					$inputappend . 'class="input-medium" size="1" onchange="document.getElementById(\'cb'.$i.'\').checked=true"'.$append,
					'value','text',$selectedvalue);
				?>
				</td>
				<td class="nowrap" class="center">
				<?php
				// $row->injury = 1;
				// $row->suspension = 1;
				// $row->away = 1;
				if($row->injury > 0)
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_INJURED');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/injured.gif',$imageTitle,'title= "'.$imageTitle.'"');
				}
				if($row->suspension > 0)
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_SUSPENDED');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/suspension.gif',$imageTitle,'title= "'.$imageTitle.'"');
				}
				if($row->away > 0)
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TPLAYERS_AWAY');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/away.gif',$imageTitle,'title= "'.$imageTitle.'"');
				}
				?>
					&nbsp;
				</td>
				<td class="center"><?php echo HTMLHelper::_('jgrid.published',$row->published,$i,'teamplayers.');?></td>
				<td class="center">
				<?php
					$player_edit_link = Route::_('index.php?option=com_joomleague&task=person.edit&id='.$row->person_id.'&return=teamplayers');
				?>
					<a href="<?php echo $player_edit_link ?>">
				<?php
					echo $row->person_id;
				?>
					</a>
				</td>
				<td class="center"><?php echo $row->tpid;?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter();?>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php endif;?>
	</fieldset>
	<!-- input fields -->
	<input type="hidden" name="project_team_id" value="<?php echo $this->projectteam->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
