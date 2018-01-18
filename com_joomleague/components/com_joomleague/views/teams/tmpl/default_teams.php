<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<table style='width:96%; border: 0; text-align:center;'>
	<thead>
	<tr class='sectiontableheader'>
		<?php if ($this->config['show_team_picture']): ?>
		<th class='team_logo'><?php echo Text::_('COM_JOOMLEAGUE_TEAMS_LOGO_TEAM'); ?></th>
		<?php endif; ?>
		<th class='team_name'><?php echo Text::_('COM_JOOMLEAGUE_TEAMS_NAME_TEAM'); ?></th>
		<th class='club_name'><?php echo Text::_('COM_JOOMLEAGUE_TEAMS_NAME_CLUB'); ?></th>
		<?php if ($this->config['show_club_picture']): ?>
		<th class='club_logo'><?php echo Text::_('COM_JOOMLEAGUE_TEAMS_LOGO_CLUB'); ?></th>
		<?php endif; ?>
		<th class='club_address'><?php echo Text::_('COM_JOOMLEAGUE_TEAMS_NAME_CLUBADDRESS'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	foreach ($this->teams as $team)
	{
		$teaminfo_link	= JoomleagueHelperRoute::getTeamInfoRoute($this->project->slug, $team->team_slug);
		$clubinfo_link	= JoomleagueHelperRoute::getClubInfoRoute($this->project->slug, $team->club_slug);
		$teamTitle		= Text::sprintf('COM_JOOMLEAGUE_TEAMS_TEAM_PROJECT_INFO', $team->team_name);
		$clubTitle		= Text::sprintf('COM_JOOMLEAGUE_TEAMS_CLUB_PROJECT_INFO', $team->club_name);
		?>
		<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">

			<?php if ($this->config['show_team_picture']): ?>
			<?php
				$teampic = $this->config['team_picture'];
				$picture = $team->$teampic;
				$image = JoomleagueHelper::getPictureThumb($picture, $team->team_name,
					$this->config['team_picture_width'], $this->config['team_picture_height'], 5);
			?>
			<td class='team_logo'><?php echo HTMLHelper::link($teaminfo_link, $image); ?></td>
			<?php endif; ?>

			<td class='team_name'>
				<?php
				if ($this->config['which_link1'] == 0)
				{
					if (!empty($team->team_www))
					{
						echo HTMLHelper::link($team->team_www, $team->team_name, array('target' => '_blank'));
					}
					else
					{
						echo $team->team_name;
					}
				}
				if ($this->config['which_link1'] == 1)
				{
					echo HTMLHelper::link($teaminfo_link, $team->team_name);
				}
				?>
			</td>

			<td class='club_name'>
				<?php
				if ($this->config['which_link2'] == 0)
				{
					if (!empty($team->club_www))
					{
						echo HTMLHelper::link($team->club_www, $team->club_name, array('target' => '_blank'));
					}
					else
					{
						echo $team->club_name;
					}
				}
				if ($this->config['which_link2']==1)
				{
					echo HTMLHelper::link($clubinfo_link, $team->club_name);
				}
				?>
			</td>

			<?php if ($this->config['show_club_picture']): ?>
			<?php
				$clubpic = $this->config['club_picture'];
				$picture = $team->$clubpic;
				$image = JoomleagueHelper::getPictureThumb($picture, $team->team_name,
					$this->config['club_picture_width'], $this->config['club_picture_height'], 1);
			?>
			<td class='club_logo'><?php echo HTMLHelper::link($clubinfo_link, $image); ?></td>
			<?php endif; ?>

			<td class='club_address'>
				<?php echo Countries::convertAddressString(	$team->club_name, $team->club_address, $team->club_state,
					$team->club_zipcode, $team->club_location, $team->club_country, 'COM_JOOMLEAGUE_TEAMS_ADDRESS_FORM');
				?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>
