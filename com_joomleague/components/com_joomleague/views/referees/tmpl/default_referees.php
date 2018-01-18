<?php use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
// Show referees as defined
if (!empty($this->referees)): ?>
<table class='table'>
	<?php
	$colspan = $this->config['show_birthday'] > 0 ? '5' : '4';
	$k = 0;
	$position = '';
	$totalEvents = array();
	foreach ($this->referees as $referee)
	{
		$refereeName = JoomleagueHelper::formatName(null, $referee->firstname, $referee->nickname,
			$referee->lastname, $this->config['name_format']);
		if ($position != $referee->position):
			$position = $referee->position;
			$k = 0; ?>
		<tr class='sectiontableheader'>
			<td width='60%' colspan='<?php echo $colspan; ?>'>
				<?php echo '&nbsp;' . Text::_($referee->position); ?>
			</td>

			<?php if ($this->config['show_games_count']): ?>
			<td style='text-align:center;'>
				<?php
				$imageTitle = Text::_('COM_JOOMLEAGUE_REFEREES_GAMES');
				echo HTMLHelper::image('media/com_joomleague/event_icons/refereed.png',
					$imageTitle, array('title' => $imageTitle, 'height' => 20));
				?>
			</td>
			<?php endif; ?>
		</tr>
		<?php endif; ?>

		<tr class="<?php echo  $k == 0 ? $this->config['style_class1'] : $this->config['style_class2']; ?>">
			<td width='30' style='text-align:center;'>
				<?php echo '&nbsp;'; ?>
			</td>
			<td width='40' style='text-align:center;' class='nowrap'>
				<?php
				if ($this->config['show_icon'] == 1)
				{
					echo JoomleagueHelper::getPictureThumb($referee->picture, $refereeName,
															$this->config['referee_picture_width'],
															$this->config['referee_picture_height']);
				}
				?>
			</td>
			<td style='width:20%;'>
				<?php
				if ($this->config['link_name'] == 1)
				{
					$link = JoomleagueHelperRoute::getRefereeRoute($this->project->slug, $referee->slug);
					echo HTMLHelper::link($link, '<i>' . $refereeName . '</i>');
				}
				else
				{
					echo '<i>' . $refereeName . '</i>';
				}
				?>
			</td>
			<td style='width:16px; text-align:center; ' class='nowrap' >
				<?php echo Countries::getCountryFlag($referee->country); ?>
			</td>

			<?php if ($this->config['show_birthday'] > 0): ?>
			<td width='10%' class='nowrap' style='text-align:left;'>
				<?php echo $this->formattedBirthDay($referee); ?>
			</td>
			<?php endif; ?>

			<?php if ($this->config['show_games_count']): ?>
			<td style='text-align:center;'>
				<?php echo $referee->countGames; ?>
			</td>
			<?php endif; ?>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
</table>
<?php endif; ?>
<br />
