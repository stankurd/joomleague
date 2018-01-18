<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<!-- Main START -->
<table width="96%" align="center" border="0" cellpadding="0"
	cellspacing="0">
	<?php
	if( $this->config['show_logo'] == 1 )
	{
		?>
	<tr class="nextmatch">
		<td class="teamlogo">
	    <?php 
				//dynamic object property string
				$pic = '';
				$pic = $this->config['show_picture'];
				$type=1;
				switch ($this->config['show_picture']) {
					case 'projectteam_picture': 
						$picture = $this->teams[0]->$pic;
						$type = 5; 
						echo JoomleagueHelper::getPictureThumb(
								$picture,
								$this->teams[0]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],
								$type
						);
						break;
					case 'logo_small': 
						$picture = $this->teams[0]->$pic;
						$type = 3; 
						echo JoomleagueHelper::getPictureThumb(
								$picture,
								$this->teams[0]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],
								$type
						);
						break;
					case 'logo_medium': 
						$picture = $this->teams[0]->$pic;
						$type = 2;
						echo JoomleagueHelper::getPictureThumb(
								$picture,
								$this->teams[0]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],
								$type
						);
						break;
					case 'logo_big': 
						$picture = $this->teams[0]->$pic;
						$type = 1;
						echo JoomleagueHelper::getPictureThumb(
								$picture,
								$this->teams[0]->name,
								$this->config['team_picture_width'],
								$this->config['team_picture_height'],
								$type
						);
						break;
					case 'country_small': 
						$type = 6;
						$pic = 'country';
						if($this->teams[0]->$pic != '' && !empty($this->teams[0]->$pic)) {
							echo Countries::getCountryFlag($this->teams[0]->$pic, 'height="11"');
						}
						break;
					case 'country_big': 
						$type = 7; 
						$pic = 'country';
						if($this->teams[0]->$pic != '' && !empty($this->teams[0]->$pic)) {
							echo Countries::getCountryFlag($this->teams[0]->$pic, 'height="50"');
						}
						break;
				}
			?>
		</td>
		<td class="vs">&nbsp;</td>
		<td class="teamlogo">
        <?php 
			//dynamic object property string
			$pic = '';
			$pic = $this->config['show_picture'];
			$type=1;
			switch ($this->config['show_picture']) {
				case 'projectteam_picture':
					$picture = $this->teams[1]->$pic;
					$type = 5;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$this->teams[1]->name,
							$this->config['team_picture_width'],
							$this->config['team_picture_height'],
							$type
					);
					break;
				case 'logo_small': 
					$picture = $this->teams[1]->$pic;
					$type = 3; 
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$this->teams[1]->name,
							$this->config['team_picture_width'],
							$this->config['team_picture_height'],
							$type
					);
					break;
				case 'logo_medium': 
					$picture = $this->teams[1]->$pic;
					$type = 2;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$this->teams[1]->name,
							$this->config['team_picture_width'],
							$this->config['team_picture_height'],
							$type
					);
					break;
				case 'logo_big': 
					$picture = $this->teams[1]->$pic;
					$type = 1;
					echo JoomleagueHelper::getPictureThumb(
							$picture,
							$this->teams[1]->name,
							$this->config['team_picture_width'],
							$this->config['team_picture_height'],
							$type
					);
					break;
				case 'country_small': 
					$type = 6;
					$pic = 'country';
					if($this->teams[1]->$pic != '' && !empty($this->teams[1]->$pic)) {
						echo Countries::getCountryFlag($this->teams[1]->$pic, 'height="11"');
					}
					break;
				case 'country_big': 
					$type = 7; 
					$pic = 'country';
					if($this->teams[1]->$pic != '' && !empty($this->teams[1]->$pic)) {
						echo Countries::getCountryFlag($this->teams[1]->$pic, 'height="50"');
					}
					break;
			}
		?>
		</td>
	</tr>
	<?php
	}
	?>
	<tr class="nextmatch">
		<td class="team"><?php
		if ( !is_null ( $this->teams ) )
		{
			echo $this->teams[0]->name;
		}
		else
		{
			echo Text::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
		}
		?></td>
		<td class="vs"><?php
		echo Text::_( "COM_JOOMLEAGUE_NEXTMATCH_VS" );
		?></td>
		<td class="team"><?php
		if ( !is_null ( $this->teams ) )
		{
			echo $this->teams[1]->name;
		}
		else
		{
			echo Text::_( "COM_JOOMLEAGUE_NEXTMATCH_UNKNOWNTEAM" );
		}
		?></td>
	</tr>
</table>

	<?php 
        $report_link = JoomleagueHelperRoute::getMatchReportRoute( $this->project->id,$this->match->id);
					
        if(isset($this->match->team1_result) && isset($this->match->team2_result))
            { ?>
			<div class="notice">
			<?php 
                $text = Text::_( "COM_JOOMLEAGUE_NEXTMATCH_ALREADYPLAYED" );
                echo HTMLHelper::link( $report_link, $text );
			?>
			</div>
			<?php 
            } ?>
                
<br />