<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<h2><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_CLUB_TEAMS'); ?></h2>
<!-- Now show teams of this club -->
<div class='venuecontent'>
<?php foreach ((array)$this->teams AS $team): ?>
	<h4>
		<?php
		$link = JoomleagueHelperRoute:: getTeamInfoRoute($team->project_id, $team->team_id);
		echo $team->project_name . ' - ' . HTMLHelper:: link($link, $team->team_name) .
			($team->team_short_name ? ' (' . $team->team_short_name . ')' : '');
		?>
	</h4>
	<div class='clubteaminfo'>
		<?php
		$notes = $team->team_notes;
		echo (!empty($notes) ? Text:: _('COM_JOOMLEAGUE_PLAYGROUND_TEAMINFO') . ' ' . HTMLHelper::_('content.prepare', $notes) : '');
		?>
	</div>
<?php endforeach; ?>
</div>
