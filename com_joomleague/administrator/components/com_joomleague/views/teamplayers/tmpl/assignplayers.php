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
if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomleague&task=teamplayers.saveOrderAjax&tmpl=component';
	HTMLHelper::_('sortablelist.sortable','teamplayerList','adminForm',strtolower($listDirn),$saveOrderingUrl);
}
?>
<script>
function searchPerson(val)
{
	jQuery('#filter_search').val(val);
	jQuery('#adminForm').submit();
}
</script>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=teamplayers&layout=assignplayers'); ?>" method="post" id="adminForm" name="adminForm">
	<fieldset class="adminform">
		<legend>
			<?php
			echo Text::sprintf('Assign Person(s) as Player to [%1$s] in project [%2$s]','<i>'.$this->team_name.'</i>',
			'<i>'.$this->prj_name.'</i>');
			?>
		</legend>
	<div class="clearfix">
	<?php
	// Search tools bar
	echo LayoutHelper::render('searchtools.default',array('view' => $this),Uri::root().'administrator/components/com_joomleague/layouts');
	?>
	<div class="clearfix"></div>
	<div class="btn-wrapper pull-right">
	<?php
	for($i = 65;$i < 91;$i ++)
	{
		printf("<a href=\"javascript:searchPerson('%s')\">%s</a>&nbsp;&nbsp;&nbsp;&nbsp;",chr($i),chr($i));
	}
	?>
	</div></div>
	<div class="clearfix"></div>
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
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_L_NAME','a.lastname',$listDirn, $listOrder); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_F_NAME','a.firstname',$listDirn, $listOrder); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_N_NAME','a.nickname',$listDirn, $listOrder); ?>
						</th>
						<th class="title" class="nowrap">
							<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_INFO'); ?>
						</th>
						<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_IMAGE'); ?></th>
						<th class="title" class="nowrap">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_BIRTHDAY','a.birthday',$listDirn, $listOrder); ?>
						</th>
						<th width="5%" class="center">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_ADMIN_PERSONS_NATIONALITY','a.country',$listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="center">
							<?php echo HTMLHelper::_('searchtools.sort','COM_JOOMLEAGUE_GLOBAL_ID','a.id',$listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<tbody>
					<?php
					$n = count($this->items);
					foreach($this->items as $i=>$row):
						if(($row->firstname != '!Unknown') && ($row->lastname != '!Player')) // Ghostplayer for match-events
						{
							$checked = HTMLHelper::_('grid.checkedout',$row,$i);
							?>
							<tr class="row<?php echo $i % 2; ?>">
						<td class="center"><?php echo $checked; ?></td>
						<td><?php echo $row->lastname; ?></td>
						<td><?php echo $row->firstname; ?></td>
						<td><?php echo $row->nickname; ?></td>

						<td><?php echo $row->info; ?></td>

						<td class="center">
									<?php
							if($row->picture == '')
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_NO_IMAGE');
								echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/delete.png',$imageTitle,
										'title= "' . $imageTitle . '"');
							}
							elseif($row->picture == JoomleagueHelper::getDefaultPlaceholder("player"))
							{
								$imageTitle = Text::_('COM_JOOMLEAGUE_ADMIN_PERSONS_DEFAULT_IMAGE');
								echo HTMLHelper::_('image','administrator/components/com_joomleague/assets/images/information.png',$imageTitle,
										'title= "' . $imageTitle . '"');
							}
							elseif($row->picture == ! '')
							{
								$playerName = JoomleagueHelper::formatName(null,$row->firstname,$row->nickname,$row->lastname,0);
								echo JoomleagueHelper::getPictureThumb($row->picture,$playerName,0,21,4);
							}
							?>
								</td>
						<td class="nowrap center">
						<?php 
						$birthday = JoomleagueHelper::convertDate($row->birthday); 
						if ($birthday == '00-00-0000' || $birthday == '0000-00-00' || $birthday == '01-01-1970') {
							//
						} else {
							echo $birthday;
						}
						?>
						</td>
						<td width="5%"><?php echo Countries::getCountryFlag($row->country); ?></td>
						<td width="1%" class="center"><?php echo $row->id; ?></td>
					</tr>
							<?php
						}
					endforeach
					;
					?>
				</tbody>
			</table>
			<?php endif;?>
	</fieldset>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="project_team_id" value="<?php echo $this->project_team_id; ?>" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>