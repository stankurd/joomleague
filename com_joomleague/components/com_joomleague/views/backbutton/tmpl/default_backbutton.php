<?php 
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if ( isset( $this->overallconfig['show_back_button'] ) )
{
	?>
	<br />
	<?php
	if ( $this->overallconfig['show_back_button'] == '1' )
	{
		$alignStr = '<div style="text-align:left; ">';
	}
	else
	{
		$alignStr = '<div style="text-align:right; ">';
	}
	if ( $this->overallconfig['show_back_button'] != '0' )
	{
		
	echo $alignStr;
	?>
		<div class="back_button">
			<a href='javascript:history.go(-1)'>
				<?php
				echo Text::_( 'COM_JOOMLEAGUE_BACKBUTTON_BACK' );
				?>
			</a>
		</div>
	</div>
	<?php
	}
}
?>