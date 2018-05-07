
<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Marco Vaninetti <martizva@tiscali.it>
 * 
 * @todo: 		add ordering for events?
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

HTMLHelper::_('jquery.framework');
//HTMLHelper::_('behavior.core');
?>
<script type="text/javascript">
<!--
var matchid = <?php echo $this->teams->id; ?>;
var baseajaxurl='<?php echo Uri::root();?>administrator/index.php?option=com_joomleague&<?php echo Session::getFormToken() ?>=1';
var homeroster = new Array;
<?php
$i = 0;
foreach ( $this->rosters ['home'] as $player ) {
	$obj = new stdclass ();
	$obj->value = $player->value;
	switch ($this->default_name_dropdown_list_order) {
		case 'lastname' :
			$obj->text = JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
		
		case 'firstname' :
			$obj->text = JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
		
		case 'position' :
			$obj->text = '(' . Text::_ ( $player->positionname ) . ') - ' . JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
	}
	echo 'homeroster[' . ($i ++) . ']=' . json_encode ( $obj ) . ";\n";
}
?>
var awayroster = new Array;
<?php
$i = 0;
foreach ( $this->rosters ['away'] as $player ) {
	$obj = new stdclass ();
	$obj->value = $player->value;
	switch ($this->default_name_dropdown_list_order) {
		case 'lastname' :
			$obj->text = JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
		
		case 'firstname' :
			$obj->text = JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
		
		case 'position' :
			$obj->text = '(' . Text::_ ( $player->positionname ) . ') - ' . JoomleagueHelper::formatName ( null, $player->firstname, $player->nickname, $player->lastname, $this->default_name_format );
			break;
	}
	echo 'awayroster[' . ($i ++) . ']=' . json_encode ( $obj ) . ";\n";
}
?>
var rosters = Array(homeroster, awayroster);
var str_delete = "<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_DELETE'); ?>";
//-->
</script>

<?php
/**
if (isset ( $this->preFillSuccess ) && $this->preFillSuccess) {
	Factory::getApplication ()->enqueueMessage ( Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_PREFILL_DONE' ), 'message' );
}*/
?>
<form method="post" id="adminForm" name="adminForm">
<?php
$p = 1;
echo HTMLHelper::_ ( 'bootstrap.startTabSet', 'tabs', array (
		'active' => 'panel1' 
) );
echo HTMLHelper::_ ( 'bootstrap.addTab', 'tabs', 'panel' . $p ++, Text::_ ( 'Events', true ) );
?>	
	<!-- events -->
	<fieldset class="form-horizontal">
		<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_DESCR'); ?></legend>
		<div id="ajaxresponse"></div>
		<div class="row">
			<div class="col-md-8">
				<table class="adminlist table">
					<thead>
						<tr>
							<th><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ID'); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TEAM'); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_PLAYER'); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_EVENT'); ?></th>
							<th class="center"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_VALUE_SUM'); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME');?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_MATCH_NOTICE'); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr id="row-new-event"></tr>
					<?php
					$k = 0;
					if (isset ( $this->matchevents )) {
						foreach ( $this->matchevents as $event ) {
							if ($event->event_type_id != 0) {
								?>
							<tr id="rowe-<?php echo $event->id; ?>"
							class="<?php echo "row$k"; ?>">
							<td><?php echo $event->id; ?></td>
							<td><?php echo $event->team; ?></td>
							<td>
								<?php
								// TODO: now remove the empty nickname quotes, but that should probably be solved differently
								echo preg_replace ( '/\'\' /', "", $event->player1 );
								?>
								</td>
							<td><?php echo Text::_($event->event); ?></td>
							<td class="center"><?php echo $event->event_sum; ?></td>
							<td><?php echo $event->event_time; ?></td>
							<td title="" class="hasTip">
									<?php echo (strlen($event->notice) > 20) ? substr($event->notice, 0, 17).'...' : $event->notice; ?>
								</td>
							<td class="center">
								<button id="delete-<?php echo $event->id; ?>" type="button"
									class="button-delete-e btn-small btn">
									<span class="icon-delete"></span>
								</button>
							</td>
						</tr>
							<?php
							}
							$k = 1 - $k;
						}
					}
					?>
				</tbody>
				</table>
			</div>
			<div class="col-md-4">
				<!-- add new Event -->
				<div id="addNewEvent">
					<fieldset class="form-vertical">
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TEAM'); ?></div>
							<div class="controls"><?php echo $this->lists['teams']; ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_PLAYER'); ?></div>
							<div class="controls" id="cell-player"></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_EVENT'); ?></div>
							<div class="controls"><?php echo $this->lists['events']; ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_VALUE_SUM'); ?></div>
							<div class="controls">
								<input type="text" size="3" value="" id="event_sum"
									name="event_sum" class="col-md-4" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME'); ?></div>
							<div class="controls">
								<input type="text" size="3" value="" id="event_time"
									name="event_time" class="col-md-4" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_MATCH_NOTICE'); ?></div>
							<div class="controls">
								<textarea rows="2" cols="150" id="event_notice"
									name="event_notice" class="col-md-12"></textarea>
							</div>
						</div>
						<div>
							<input id="save-new-event" type="button"
								class="inputbox button-save-e btn btn-small btn-success"
								value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_SAVE'); ?>" />
							<br>
							<br>
							<div id="ajaxresponseevent"></div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</fieldset>
		
		<?php
		echo HTMLHelper::_('bootstrap.endTab');
		echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p ++,Text::_('Comments',true));
		?>

		
	<!-- comments -->
	<fieldset class="form-horizontal">
		<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_LIVE_COMMENTARY_DESCR'); ?></legend>
		<div class="row">
			<div class="col-md-7">
				<table class='adminlist table'>
					<thead>
						<tr>
							<th><?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_ID'); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE' ); ?></th>
							<th><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME' );?></th>
							<th style="width: 500px;"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_NOTES' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr id="row-new-comment"></tr>
				<?php
				$k = 0;
				if (isset ( $this->matchevents )) {
					foreach ( $this->matchevents as $event ) {
						if ($event->event_type_id == 0) {
							?>
						<tr id="rowc-<?php echo $event->id; ?>"
							class="<?php echo "row$k"; ?>">
							<td><?php echo $event->id; ?></td>
							<td>
								<?php
							switch ($event->event_sum) {
								case 2 :
									echo Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_2' );
									break;
								case 1 :
									echo Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_1' );
									break;
							}
							?>
							</td>

							<td><?php echo $event->event_time;?></td>
							<td title="" class="hasTooltip"><?php echo $event->notes;?></td>
							<td class="center">
								<button id="delete-<?php echo $event->id; ?>" type="button"
									class="inputbox button-delete-c btn btn-small">
									<span class="icon-delete"></span>
								</button>
							</td>
						</tr>
						<?php
						}
						$k = 1 - $k;
					}
				}
				?>
			</tbody>
				</table>
			</div>
			<div class="col-md-5">
				<!-- add new Comment -->
				<div id="addNewComment">
					<fieldset class="form-vertical">
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE' ); ?></div>
							<div class="controls">
								<select name="ctype" id="ctype"
									class="col-md-4 select-commenttype">
									<option value="1"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_1' ); ?></option>
									<option value="2"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_TYPE_2' ); ?></option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_TIME' ); ?></div>
							<div class="controls">
								<input type="text" size="3" value="" id="comment_event_time"
									name="comment_event_time" class="col-md-2" />
							</div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_EE_LIVE_NOTES' ); ?></div>
							<div class="controls">
								<textarea rows="2" cols="100" id="comment_note"
									name="comment_note" class="col-md-7"></textarea>
							</div>
						</div>
						<div>
							<input id="save-new-comment" type="button"
								class="inputbox button-save-c btn btn-small btn-success"
								value="<?php echo Text::_('COM_JOOMLEAGUE_GLOBAL_SAVE'); ?>" />
							<br>
							<br>
							<div id="ajaxresponsecomment"></div>
						</div>
					</fieldset>
				</div>
			</div>
		</div>
	</fieldset>
<?php
echo HTMLHelper::_('bootstrap.endTab');
	echo HTMLHelper::_('bootstrap.endTabSet');
?>
	<?php echo HTMLHelper::_('form.token'); ?>
	<input type="hidden" name="task" value="" />
</form>
<div class="clearfix"></div>