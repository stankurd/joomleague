<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
foreach ( $this->rows as $position_id => $players ): ?>
<div style="margin:auto; width:720px;">
	<!-- position header -->
	<?php
		$row = current($players);
		$position	= $row->position;
		$k			= 0;
		$colspan	= ( ( $this->config['show_birthday'] > 0 ) ? '6' : '5' );	?>
<h2><?php	echo '&nbsp;' . Text::_( $row->position );	?></h2>
<?php foreach ($players as $row): ?>
<tr class="<?php echo ($k == 0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
<div class="mini-player_links">
			<table>
			  <tbody>
			  <tr>
				<?php if ($this->config['show_player_numbers']) { ?>
				<td>
					<div class="player-trikot">
						<?php
						if ( ! empty( $row->position_number ) )
						{
							echo $row->position_number;
						}
						?>
					</div>
			     </td>
				<?php } ?>
			    <td style="width: 55px;padding:0px;">
				<?php
				$playerName = JoomleagueHelper::formatName(null ,$row->firstname, $row->nickname, $row->lastname, $this->config["name_format"]);
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
					$link = JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$this->team->slug,$row->slug);
					$playerPicture = JoomleagueHelper::getPictureThumb($picture, $imgTitle,
										$this->config['player_picture_width'],
										$this->config['player_picture_height']);
					echo HTMLHelper::link($link,$playerPicture);
				}
				else
				{
					echo JoomleagueHelper::getPictureThumb($picture, $imgTitle,
										$this->config['player_picture_width'],
										$this->config['player_picture_height']);
				}
				?>
				</td>
			    <td style="padding-left: 9px;">
			      <div class="player-position"><?php	echo Text::_( $row->position );	?></div>
				  <div class="player-name">
				  <?php
				  	if ($this->config['link_player']==1)
					{
						$link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$this->team->slug,$row->slug);
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
		</tr>

			<?php	$k = 1 - $k; ?>
	<?php endforeach; ?>
	<div class="clear"></div></div>
	<?php endforeach;	?>
