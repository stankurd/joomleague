<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
?>
<div class="mini-team">
	<table style="width: 100%;" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;';
				if ( $this->config['show_team_shortform'] == 1 )
				{
					echo Text::sprintf( 'COM_JOOMLEAGUE_ROSTER_STAFF_OF2', $this->team->name, $this->team->short_name );
				}
				else
				{
					echo Text::sprintf( 'COM_JOOMLEAGUE_ROSTER_STAFF_OF', $this->team->name );
				}
				?>
			</td>
		</tr>
	</table>
<?php
			$k = 0;
			for ( $i = 0, $n = count( $this->stafflist ); $i < $n; $i++ )
			{
				$row = $this->stafflist[$i];
				?>
				<tr class="<?php echo ($k == 0)? '' : 'sectiontableentry2'; ?>"></td><div class="mini-team-toggler">
			<div class="short-team">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
				  <tbody><tr>
				    <td style="padding: 5px 10px; color: rgb(173, 173, 173); font-weight: bold; width: 200px; text-transform: uppercase;">
				      <?php
				echo Text::_($row->position);
				?>
				    </td>
				    <td>
					  <div class="player-name">
					  <?php
					  	$playerName = JoomleagueHelper::formatName(null ,$row->firstname, $row->nickname, $row->lastname, $this->config["name_format"]);
						if ($this->config['link_player']==1)
						{
							$link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$this->team->slug,$row->slug);
							echo HTMLHelper::link($link,'<i>'.$playerName.'</i>');
						}
						else
						{
							echo '<i>'.$playerName.'</i>';
						}
						?>
					</td>
				  </tr>
				</tbody></table>
			</div>
			<div class="quick-team">
				<table width="100%" cellspacing="0" cellpadding="0" border="0">
				  <tbody><tr>
				    <td style="padding: 5px 10px; color: rgb(173, 173, 173); font-weight: bold; width: 200px; text-transform: uppercase;">
				      <?php
				echo Text::_($row->position);
				?>
				    </td>
				    <td style="width: 55px;">

						  <?php

		$imgTitle = Text::sprintf( $playerName );
		$picture = $row->picture;
		if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ))
		{
		$picture = $row->ppic;
		}
		if ( !file_exists( $picture ) )
		{
			$picture = JoomleagueHelper::getDefaultPlaceholder("player");
		}
	  	if ($this->config['link_player']==1)
		{
			$link = JoomleagueHelperRoute::getStaffRoute( $this->project->slug, $this->team->slug, $row->slug );
			$staffPicture = JoomleagueHelper::getPictureThumb($picture, $imgTitle,
										$this->config['staff_picture_width'],
										$this->config['staff_picture_height']);
			echo HTMLHelper::link($link,$staffPicture);
		}
		else
		{
			echo JoomleagueHelper::getPictureThumb($picture, $imgTitle,
										$this->config['staff_picture_width'],
										$this->config['staff_picture_height']);
		}
		?>

					</td>
				    <td style="padding-left: 10px;">
				      <div class="player-position"><?php
						echo Text::_( $row->position );
				?></div>
					  <div class="player-name">
					  <?php $projectid = $this->project->id;
					  if ($this->config['link_player']==1)
					  {
							$link = JoomleagueHelperRoute::getStaffRoute( $this->project->slug, $this->team->slug, $row->slug );
							echo HTMLHelper::link($link,'<i>'.$playerName.'</i>');
					  }
					  else
					  {
					  		echo '<i>'.$playerName.'</i>';
					  }
					  ?></div>
					</td>
				  </tr>
				</tbody></table>
			</div>
		</div></td></tr><?php
				$k = 1 - $k;
			}
			?>
		</div>