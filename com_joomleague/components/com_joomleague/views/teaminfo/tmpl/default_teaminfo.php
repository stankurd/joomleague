<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (!isset($this->team) || !isset($this->club))
{
	Factory::getApplication()->enqueueMessage(Text::_('COM_JOOMLEAGUE_TEAMINFO_ERROR'), 'warning');
}
else
{
	if ($this->config['show_club_info'] || $this->config['show_team_info'])
	{
		$attributes = 'class="left-column"';
	}
	else
	{
		$attributes = 'style="text-align:center; width:100%;"';
	}
	?>
	<div <?php echo $attributes; ?>>
		<?php
		//dynamic object property string
		$pic = $this->config['show_picture'];
		echo JoomleagueHelper::getPictureThumb($this->team->$pic,
												$this->team->name,
												$this->config['team_picture_width'],
												$this->config['team_picture_height'],
												1);
		?>
	</div>
	<?php if ($this->config['show_club_info'] || $this->config['show_team_info']): ?>
	<div class='right-column'>
		<?php if ($this->config['show_club_info']): ?>
			<?php if ($this->club->address || $this->club->zipcode): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'><?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_ADDRESS'); ?></span>
				<?php
				$dummy = Countries::convertAddressString(	$this->club->name,
															$this->club->address,
															$this->club->state,
															$this->club->zipcode,
															$this->club->location,
															$this->club->country,
															'COM_JOOMLEAGUE_TEAMINFO_CLUB_ADDRESS_FORM' );
				$dummy = explode('<br />', $dummy);
				?>
				<?php for ($i = 0; $i < count($dummy); $i++): ?>
					<?php if ($i > 0): ?>
			<span class='clubinfo_listing_item'>&nbsp;</span>
					<?php endif; ?>
			<span class='clubinfo_listing_value'><?php echo $dummy[$i]; ?></span>
				<?php endfor; ?>
		</div>
			<?php endif; ?>

			<?php if ($this->club->phone): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_PHONE'); ?></span>
			<span class='clubinfo_listing_value'> <?php echo $this->club->phone; ?></span>
		</div>
			<?php endif; ?>

			<?php if ($this->club->fax): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_FAX'); ?></span>
			<span class='clubinfo_listing_value'> <?php echo $this->club->fax; ?></span>
		</div>
			<?php endif; ?>

			<?php if ($this->club->email): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_EMAIL'); ?></span>
			<span class='clubinfo_listing_value'>
				<?php
				$user = Factory::getUser();
				if (($user->id) or (!$this->overallconfig['nospam_email']))
				{
					echo HTMLHelper::link('mailto:'. $this->club->email, $this->club->email);
				} else {
					echo HTMLHelper::_('email.cloak', $this->club->email);
				}
				?>
			</span>
		</div>
			<?php endif; ?>

			<?php if ($this->club): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_NAME'); ?></span>
			<span class='clubinfo_listing_value'>
				<?php
				$link = JoomleagueHelperRoute::getClubInfoRoute($this->project->slug, $this->club->slug);
				echo HTMLHelper::link($link, $this->club->name);
				?>
			</span>
		</div>
			<?php endif; ?>

			<?php if ($this->club->website): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_CLUB_SITE'); ?></span>
			<span class='clubinfo_listing_value'> 
				<?php echo HTMLHelper::link($this->club->website, $this->club->website, array('target' => '_blank')); ?>
			</span>
		</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if ($this->config['show_team_info']): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_NAME'); ?></span>
			<span class='clubinfo_listing_value'>
				<?php
				$link = JoomleagueHelperRoute::getTeamInfoRoute($this->project->slug, $this->team->slug);
				echo HTMLHelper::link($link, $this->team->tname);
				?>
			</span>
		</div>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_NAME_SHORT'); ?></span>
			<span class='clubinfo_listing_value'>
				<?php
				$link = JoomleagueHelperRoute::getTeamStatsRoute($this->project->slug, $this->team->slug);
				echo HTMLHelper::link($link, $this->team->short_name);
				?>
			</span>
		</div>
			<?php if ($this->team->info): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_INFO'); ?></span>
			<span class='clubinfo_listing_value'> <?php echo $this->team->info; ?></span>
		</div>
			<?php endif; ?>

			<?php if ($this->team->website): ?>
		<div class='jl_parentContainer'>
			<span class='clubinfo_listing_item'> <?php echo Text::_('COM_JOOMLEAGUE_TEAMINFO_TEAM_SITE'); ?></span>
			<span class='clubinfo_listing_value'> <?php
				echo HTMLHelper::link($this->team->team_website, $this->team->team_website, array('target' => '_blank')); ?>
			</span>
		</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<br />
	<?php endif; ?>
<?php
}
?>
