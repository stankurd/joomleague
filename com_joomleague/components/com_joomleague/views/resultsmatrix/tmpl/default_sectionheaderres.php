<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<?php
if ( $this->config['show_sectionheader'] == 1 )
{
	?>
		<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				if ($this->roundid > 0)
				{
					$title = Text::_('COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS');
					if ( isset( $this->division))
					{
						$title = Text::sprintf('COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS2', '<i>' . $this->division->name . '</i>' );
					}

					JoomleagueHelperHtml::showMatchdaysTitle($title, $this->roundid, $this->config );

					if ( $this->showediticon )
						{
							$link = JoomleagueHelperRoute::getResultsRoute( $this->project->id, $this->roundid, $this->divisionid, 0, 0, 'match.display');
							$imgTitle = Text::_('COM_JOOMLEAGUE_RESULTS_ENTER_EDIT_RESULTS');
							$desc = HTMLHelper::image('media/com_joomleague/jl_images/edit.png', $imgTitle, array('title' => $imgTitle));
							echo ' ';
							echo HTMLHelper::link( $link, $desc );
						}

				}
				else
				{
					// 1 request for current round
					// seems to be this shall show a plan of matches of a team???
					JoomleagueHelperHtml::showMatchdaysTitle(Text::_('COM_JOOMLEAGUE_RESULTS_PLAN' ) . " - " . $team->name, 0, $this->config);
				}
				?>
			</td>
		</tr>
	</table>
	<?php
}
?>