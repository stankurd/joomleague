<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
//$this->config['show_small_logo']	= 1;
//$this->config['show_medium_logo']	= 0;
//$this->config['show_big_logo']	= 0;
//$this->config['team_picture_small_height'] = 80;
?>
<table class='table'>
	<thead>
	<tr class='sectiontableheader'>
		<?php if ($this->config['show_small_logo']): ?>
		<th class='club_logo'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_LOGO'); ?></th>
		<?php endif; ?>

		<?php if ($this->config['show_medium_logo']): ?>
		<th class='club_logo'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_LOGO'); ?></th>
		<?php endif; ?>

		<?php if ($this->config['show_big_logo']): ?>
		<th class='club_logo'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_LOGO'); ?></th>
		<?php endif; ?>

		<th class='club_name'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_CLUBNAME'); ?></th>

		<?php if ($this->config['show_club_teams']): ?>
		<th class='club_teams'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_TEAMS'); ?></th>
		<?php endif; ?>

		<?php if ($this->config['show_address']): ?>
		<th class='club_address'><?php echo Text::_('COM_JOOMLEAGUE_CLUBS_ADDRESS'); ?></th>
		<?php endif; ?>
	</tr>
	</thead>
	<tbody>
	<?php
	$k = 0;
	foreach ($this->clubs as $club)
	{
		$clubinfo_link = JoomleagueHelperRoute::getClubInfoRoute($this->project->slug, $club->club_slug);
		$title = Text::sprintf('COM_JOOMLEAGUE_CLUBS_TITLE2', $club->name);

		$picture = $club->logo_small;
		if (is_null($picture) || !file_exists($picture))
		{
			$picture = JoomleagueHelper::getDefaultPlaceholder('clublogosmall');
		}
		$image = HTMLHelper::image($picture, $title, array('height'=>21, 'title' => $title, ' border' => 0));
		$smallClubLogoLink = HTMLHelper::link($clubinfo_link, $image);

		$picture = $club->logo_middle;
		if (is_null($picture) || !file_exists($picture))
		{
			$picture = JoomleagueHelper::getDefaultPlaceholder('clublogomedium');
		}
		$image = HTMLHelper::image($picture, $title, array('height'=>50, 'title' => $title, ' border' => 0));
		$mediumClubLogoLink = HTMLHelper::link($clubinfo_link, $image);

		$picture = $club->logo_big;
		if (is_null($picture) || !file_exists($picture))
		{
			$picture = JoomleagueHelper::getDefaultPlaceholder('clublogobig');
		}
		$image = HTMLHelper::image($picture, $title, array('height'=>150, 'title' => $title, ' border' => 0));
		$bigClubLogoLink = HTMLHelper::link($clubinfo_link, $image);
		?>
		<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
			<?php if ($this->config['show_small_logo']): ?>
			<td><?php echo $smallClubLogoLink; ?></td>
			<?php endif; ?>

			<?php if ($this->config['show_medium_logo']): ?>
			<td><?php echo $mediumClubLogoLink; ?></td>
			<?php endif; ?>

			<?php if ($this->config['show_big_logo']): ?>
			<td><?php echo $bigClubLogoLink; ?></td>
			<?php endif; ?>

			<td>
				<?php
					if (!empty($club->website))
					{
						echo HTMLHelper::link($club->website, $club->name, array('target' => '_blank'));
					}
					else
					{
						echo $club->name;
					}
				?>
			</td>

			<?php if ($this->config['show_club_teams']): ?>
			<td>
				<?php
				foreach ($club->teams as $team)
				{
					//dynamic object property string
					$pic = $this->config['show_picture'];
					echo JoomleagueHelper::getPictureThumb($team->$pic, $team->name,
						$this->config['team_picture_width'], $this->config['team_picture_height'], 1);
					$teaminfo_link = JoomleagueHelperRoute::getTeamInfoRoute($this->project->slug, $team->team_slug);
					echo HTMLHelper::link($teaminfo_link, $team->name);
					echo '<br />';
				}
				?>
			</td>
			<?php endif; ?>

			<?php if ($this->config['show_address']): ?>
			<td>
				<?php
				echo Countries::convertAddressString($club->name, $club->address, $club->state, $club->zipcode,
					$club->location, $club->country, 'COM_JOOMLEAGUE_CLUBS_ADDRESS_FORM');
			?>
			</td>
			<?php endif; ?>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>