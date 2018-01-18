<?php use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<!-- section header e.g. ranking, results etc. -->
<a id='jl_top'></a>

<table class='contentpaneopen'>
	<tr>
		<td class='contentheading'><?php echo $this->pageTitle; ?></td>
	</tr>
	<tr>
		<td class='contentheading'>
		<?php if ($this->roundid):
			$title = Text::_('COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS');
			if (isset($this->division))
			{
				$title = Text::sprintf('COM_JOOMLEAGUE_RESULTS_ROUND_RESULTS2', '<i>' . $this->division->name . '</i>');
			}
			JoomleagueHelperHtml::showMatchdaysTitle($title, $this->roundid, $this->config, 0);

			if ($this->showediticon):
				$modalheight = ComponentHelper::getParams('com_joomleague')->get('modal_popup_height', 600);
				$modalwidth = ComponentHelper::getParams('com_joomleague')->get('modal_popup_width', 900);
				$link = JoomleagueHelperRoute::getResultsRoute($this->project->id, $this->roundid, $this->model->divisionid,
					$this->model->mode, $this->model->order, 'match.display');
				?>
				<a rel="{handler: \'iframe\',size: {x:<?php echo $modalwidth; ?>,y:<?php echo $modalheight; ?>}}"
				   href="<?php echo $link; ?>" class='modal'>
					<?php
					$imgTitle = Text::_('COM_JOOMLEAGUE_RESULTS_ENTER_EDIT_RESULTS');
					echo HTMLHelper::image('media/com_joomleague/jl_images/edit.png', $imgTitle, array(' title' => $imgTitle));
					?>
				</a>
				<?php
			endif;
		else:
			//1 request for current round
			// seems to be this shall show a plan of matches of a team???
			JoomleagueHelperHtml::showMatchdaysTitle(Text::_('COM_JOOMLEAGUE_RESULTS_PLAN') , 0, $this->config, 0);
		endif; ?>
		</td>

		<?php if ($this->config['show_matchday_dropdown']==1): ?>
		<td class='contentheading' style='text-align:right; font-size: 100%;'>
			<?php echo $this->matchdaysoptions; ?>
		</td>
		<?php endif; ?>
	</tr>
</table>