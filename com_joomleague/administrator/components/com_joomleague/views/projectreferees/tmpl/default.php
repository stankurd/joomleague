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
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('jquery.framework');
$app = Factory::getApplication();
$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
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
		serviceUrl: '" . Route::_('index.php?option=com_joomleague&task=quickadd.searchreferee&project_id=' . $this->project->id,false) . "',
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
<?php
$uri = Uri::root();
?>
<fieldset class="form-horizontal">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTREFEREES_QUICKADD_REFEREE');?></legend>

<form id="quickaddForm" action="<?php echo Uri::root(); ?>administrator/index.php?option=com_joomleague&task=quickadd.addreferee" method="post">
	<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PROJECTREFEREES_QUICKADD_DESCR');?>
	<div class="clearfix"></div>
		<div class="btn-wrapper input-append pull-left">
			<input type="text" name="p" id="quickadd" size="50"value="<?php htmlspecialchars(Factory::getApplication()->input->getString('q',false)); ?>" />
			<input class="btn" type="submit" name="submit" id="submit" value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ADD');?>" />
		</div>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
</fieldset>
<br>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projectreferees'); ?>" method="post" id="adminForm" name="adminForm">
	<div id="j-main-container" class="j-main-container">	
	<fieldset class="form-horizontal">
		<legend><?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_PREF_TITLE2','<i>'.$this->project->name.'</i>');?></legend>
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
		</div>
		</div>
		<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
	<table class="table table-striped" id="projectrefereeList">
		<thead>
			<tr>
				<th width="1%" class="center">
					<?php echo HTMLHelper::_('grid.checkall'); ?>
				</th>
				<th width="20">&nbsp;</th>
				<th>
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PREF_NAME','a.lastname',$listDirn, $listOrder);?>
				</th>
				<th width="5%" class="center">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PREF_IMAGE');?>
				</th>
				<th class="center">
					<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PREF_POS','pref.project_position_id',$listDirn, $listOrder);?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_PUBLISHED');?>
				</th>
				<th width="1%">
					<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PREF_PID');?>
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
			$link = Route::_('index.php?option=com_joomleague&task=projectreferee.edit&id='.$row->id);
			$checked = HTMLHelper::_('grid.checkedout',$row,$i);
			$inputappend = '';
			$published = HTMLHelper::_('jgrid.published',$row->published,$i,'projectreferees.');
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center"><?php echo $checked;?></td>
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
						$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PREF_EDIT_DETAILS');
						echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/edit.png',$imageTitle,'title= "' . $imageTitle . '"');
					?>
					</a>
				</td>
				<?php
				}
				?>
				<td>
				<?php
					echo JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,JoomleagueHelper::defaultNameFormat())
				?>
				</td>
				<td class="center">
				<?php
				if($row->picture == '')
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PREF_NO_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,'title= "' . $imageTitle . '"');
				}
				elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player"))
				{
					$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PREF_DEFAULT_IMAGE');
					echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,'title= "' . $imageTitle . '"');
				}
				elseif($row->picture == ! '')
				{
					$playerName = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,JoomleagueHelper::defaultNameFormat());
					$picture = JPATH_SITE . '/' . $row->picture;
					echo JoomleagueHelper::getPictureThumb($picture,$playerName,0,21,4);
				}
				?>
				</td>
				<td class="center">
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
					echo HTMLHelper::_('select.genericlist',$this->lists['project_position_id'],'project_position_id' . $row->id,
					$inputappend . 'class="inputbox" size="1" onchange="document.getElementById(\'cb' . $i . '\').checked=true"' . $append,
					'value','text',$selectedvalue);
				?>
				</td>
				<td class="center"><?php echo $published;?></td>
				<td class="center" width="1%">
				<?php
					$person_edit_link = Route::_('index.php?option=com_joomleague&task=person.edit&id=' . $row->person_id.'&return=projectreferees');
				?>
					<a href="<?php echo $person_edit_link ?>">
					<?php
						echo $row->person_id;
					?>
					</a>
				</td>
				<td class="center" width="1%"><?php echo $row->id; ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='12'><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>
	<?php endif; ?>
	</fieldset>
	<!-- input fields -->
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
