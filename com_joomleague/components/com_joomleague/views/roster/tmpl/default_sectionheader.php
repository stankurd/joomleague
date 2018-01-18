<?php
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<table class="contentpaneopen">
	<tr>
		<td class="contentheading">
		<?php
		echo $this->pagetitle;
		if ( $this->showediticon )
		{
			$modalheight = ComponentHelper::getParams('com_joomleague')->get('modal_popup_height', 600);
			$modalwidth = ComponentHelper::getParams('com_joomleague')->get('modal_popup_width', 900);
			$link = JoomleagueHelperRoute::getPlayersRoute( $this->project->id, 
															$this->team->id, 
															'teamplayer.select', 
															$this->projectteam->division_id, 
															$this->projectteam->ptid);
			echo ' <a rel="{handler: \'iframe\',size: {x:'.$modalwidth.',y:'.$modalheight.'}}" href="'.$link.'" class="modal">';
			echo HTMLHelper::image("media/com_joomleague/jl_images/edit.png",
					Text::_( 'COM_JOOMLEAGUE_ROSTER_EDIT' ),
					array( "title" => Text::_( "COM_JOOMLEAGUE_ROSTER_EDIT" ) )
			);
			echo '</a>';
		}
		?>
		</td>
	</tr>
</table>
<br />
