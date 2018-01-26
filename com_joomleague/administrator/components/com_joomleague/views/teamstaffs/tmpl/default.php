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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
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
$uri = Uri::root();
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
		serviceUrl: '" . Route::_('index.php?option=com_joomleague&task=quickadd.searchstaff&projectteam_id=' . $this->projectteam->id,false) . "',
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
<fieldset class="form-horizontal">
	<legend><?php echo Text::_("COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_QUICKADD_STAFF");?></legend>
	<form id="quickaddForm" action="<?php echo Uri::root(); ?>administrator/index.php?option=com_joomleague&task=quickadd.addstaff" method="post">
		<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_QUICKADD_DESCR'); ?>
		<div class="clearfix"></div>
		<div class="btn-wrapper input-append pull-left">
			<input type="text" name="p" id="quickadd" size="50" value="<?php htmlspecialchars(Factory::getApplication()->input->getString('q',false)); ?>" />
			<input class="btn" type="submit" name="submit" id="submit" value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ADD');?>" />
		</div>
		<input type="hidden" name="projectteam_id" id="projectteam_id" value="<?php echo $this->projectteam->id; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</fieldset>
<br>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=teamstaffs'); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="form-horizontal">
		<legend>
			<?php
			echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TSTAFFS_TITLE2','<i>'.$this->projectteam->name.'</i>','<i>'.$this->project->name.'</i>');
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
				printf("<a href=\"javascript:searchTeamStaff('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
			}
		?>
		</div></div>
	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
		<table class="table table-striped" id="teamstaffList">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th width="20">&nbsp;</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_TSTAFFS_NAME','a.lastname',$listDirn, $listOrder);?>
					</th>
					<th class="center">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_IMAGE'); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_TSTAFFS_POS','a.project_position_id',$listDirn, $listOrder);?>
					</th>
					<th>
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_STATUS');?>
					</th>
					<th class="center">
						<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED');?>
					</th>
					<th width="20">
						<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_PID');?>
					</th>
					<th width="1%">
						<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder);?>
					</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$n = count($this->items);
			foreach($this->items as $i=>$row):
				$link = Route::_('index.php?option=com_joomleague&task=teamstaff.edit&projectteam='.$this->projectteam->id.'&id='.$row->id);
				$checked = HTMLHelper::_('grid.checkedout',$row,$i);
				$inputappend = '';
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
					<?php
					echo $checked;
					?>
					</td>
					<?php
					if(JLTable::_isCheckedOut($user->get('id'),$row->checked_out))
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
							$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_EDIT_DETAILS');
							echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "'.$imageTitle.'"');
						?>
						</a>
					</td>
					<?php
					}
					?>
					<td>
					<?php echo JoomleagueHelper::formatName(null, $row->firstname, $row->nickname, $row->lastname, 0)?>
					</td>
					<td class="center">
					<?php
					if($row->picture == '')
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_NO_IMAGE');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "'.$imageTitle.'"');
					}
					elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player")){
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_DEFAULT_IMAGE');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "'.$imageTitle.'"');
					}
					elseif($row->picture == ! '')
					{
						$playerName = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0);
						echo JoomleagueHelper::getPictureThumb($row->picture,$playerName,0,21,4);
					}
					?>
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
						$append = '';
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

						echo HTMLHelper::_('select.genericlist',$this->lists['project_position_id'],'project_position_id' . $row->id,
						$inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cb' . $i . '\').checked=true"' . $append,
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
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_INJURED');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/injured.gif',$imageTitle,'title= "'.$imageTitle.'"');
					}
					if($row->suspension > 0)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_SUSPENDED');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/suspension.gif',$imageTitle,'title= "'.$imageTitle.'"');
					}
					if($row->away > 0)
					{
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_TSTAFFS_AWAY');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/away.gif',$imageTitle,'title= "'.$imageTitle.'"');
					}
					?>
						&nbsp;
					</td>
					<td class="center"><?php echo HTMLHelper::_('jgrid.published',$row->published,$i,'teamstaffs.');?></td>
					<td class="center">
					<?php
						$player_edit_link = Route::_('index.php?option=com_joomleague&task=person.edit&id='.$row->person_id.'&return=teamstaffs');
					?>
						<a href="<?php echo $player_edit_link ?>">
					<?php
						echo $row->person_id;
					?>
						</a>
					</td>
					<td class="center"><?php echo $row->id;?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12"><?php echo $this->pagination->getListFooter();?></td>
				</tr>
			</tfoot>
		</table>
		<?php endif;?>
	</fieldset>
	<input type="hidden" name="team" value="<?php echo $this->projectteam->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
