<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<?php
$picture = $this->projectteam->projectteam_picture;

// only show projectteam picture when the user has uploaded one (showing a placeholder here doesn't make sense)
if (!empty($picture) && $picture != JoomleagueHelper::getDefaultPlaceholder("team"))
{
	?>
	<table width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="center">
				<?php
				$imgTitle = Text::sprintf('COM_JOOMLEAGUE_TEAMPLAN_PICTURE_TEAM', $this->projectteam->name);
				echo JoomleagueHelper::getPictureThumb($picture, $imgTitle,
					$this->config['page_header_team_picture_width'],
					$this->config['page_header_team_picture_height']);
				?>
			</td>
		</tr>
	</table>
<?php
}
?>