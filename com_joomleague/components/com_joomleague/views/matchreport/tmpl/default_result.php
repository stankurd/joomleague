<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>

<!-- START: game result -->
<table class="matchreport" border="0">

    <?php
    if ($this->config['show_team_logo'] == 1)
    {
    ?>
        <tr>
            <td class="teamlogo">
                <?php
					//dynamic object property string
					$pic = '';
					$pic = $this->config['show_picture'];
					$type=1;
					switch ($this->config['show_picture']) {
						case 'projectteam_picture':
							$picture = $this->team1->$pic;
							$type = 5;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team1->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_small':
							$picture = $this->team1->$pic;
							$type = 3;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team1->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_medium':
							$picture = $this->team1->$pic;
							$type = 2;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team1->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_big':
							$picture = $this->team1->$pic;
							$type = 1;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team1->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'country_small':
							$type = 6;
							$pic = 'country';
							if($this->team1->$pic != '' && !empty($this->team1->$pic)) {
								echo Countries::getCountryFlag($this->team1->$pic, 'height="11"');
							}
							break;
						case 'country_big':
							$type = 7;
							$pic = 'country';
							if($this->team1->$pic != '' && !empty($this->team1->$pic)) {
								echo Countries::getCountryFlag($this->team1->$pic, 'height="50"');
							}
							break;
					}
				?>
		</td>
		<td>
		</td>
		<td class="teamlogo">
                <?php
					//dynamic object property string
					$pic = '';
					$pic = $this->config['show_picture'];
					$type=1;
					switch ($this->config['show_picture']) {
						case 'projectteam_picture':
							$picture = $this->team2->$pic;
							$type = 5;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team2->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_small':
							$picture = $this->team2->$pic;
							$type = 3;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team2->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_medium':
							$picture = $this->team2->$pic;
							$type = 2;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team2->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'logo_big':
							$picture = $this->team2->$pic;
							$type = 1;
							echo JoomleagueHelper::getPictureThumb(
									$picture,
									$this->team2->name,
									$this->config['team_picture_width'],
									$this->config['team_picture_height'],
									$type
							);
							break;
						case 'country_small':
							$type = 6;
							$pic = 'country';
							if($this->team2->$pic != '' && !empty($this->team2->$pic)) {
								echo Countries::getCountryFlag($this->team2->$pic, 'height="11"');
							}
							break;
						case 'country_big':
							$type = 7;
							$pic = 'country';
							if($this->team2->$pic != '' && !empty($this->team2->$pic)) {
								echo Countries::getCountryFlag($this->team2->$pic, 'height="50"');
							}
							break;
					}
				?>
		</td>
	</tr>

    <?php
    } // end team logo
    ?>

	<tr>
		<td class="team">
			<?php
			if ( $this->config['names'] == "short_name" ) {
			    echo $this->team1->short_name;
			}
			if ( $this->config['names'] == "middle_name" ) {
			    echo $this->team1->middle_name;
			}
			if ( $this->config['names'] == "name" ) {
			    echo $this->team1->name;
			}
			?>
		</td>
		<td>
			<?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_VS') ?>
		</td>
		<td class="team">
			<?php
			if ( $this->config['names'] == "short_name" ) {
			    echo $this->team2->short_name;
			}
			if ( $this->config['names'] == "middle_name" ) {
			    echo $this->team2->middle_name;
			}
			if ( $this->config['names'] == "name" ) {
			    echo $this->team2->name;
			}
			?>
		</td>
	</tr>
</table>

<?php
if ($this->match->cancel > 0)
{
	?>
	<table class="matchreport" border="0">
		<tr>
			<td class="result">
					<?php echo $this->match->cancel_reason; ?>
			</td>
		</tr>
	</table>
	<?php
}
else
{
	?>
	<table class="matchreport" border="0">
		<tr>
			<td class="result">
				<?php echo $this->showMatchresult($this->match->alt_decision, 1); ?>
			</td>
			<td class="result">
				<?php echo $this->showMatchresult($this->match->alt_decision, 2); ?>
			</td>
		</tr>

		<?php
        if ($this->config['show_period_result'] == 1)
        {
            // show only one half time result for soccer and handball
            $search_empty_part_results = array(";", "NULL");
            if ($this->project->sport_type_name == "COM_JOOMLEAGUE_ST_SOCCER" || $this->project->sport_type_name == "COM_JOOMLEAGUE_ST_HANDBALL")
            {
                if((str_replace($search_empty_part_results, '', $this->match->team1_result_split) != "") && (str_replace($search_empty_part_results, '', $this->match->team2_result_split) != "")) {
                ?>
                  <tr>
                    <td class="legs" colspan="2">
                    <?php echo '(' . strstr($this->match->team1_result_split, ';', true) . ':' . strstr($this->match->team2_result_split, ';', true) . ')'; ?>
                    </td>
                  </tr>
                <?php
                }
            }
            else
            {
                if ( $this->showLegresult() )
                {
                    ?>
                    <tr>
                        <td class="legs">
                            <?php echo $this->showLegresult(1); ?>
                        </td>
                        <td class="legs">
                            <?php echo $this->showLegresult(2); ?>
                        </td>
                    </tr>
                    <?php
                }
            }
        }

        if ($this->config['show_overtime_result'] == 1)
        {
            if ( $this->showOvertimeResult() )
            {
                ?>
                <tr>
                    <td class="legs" colspan="2">
                        <?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_OVERTIME');
                        echo " " . $this->showOvertimeresult(); ?>
                    </td>
                </tr>
                <?php
            }
        }

        if ($this->config['show_shotout_result'] == 1)
        {
            if ( $this->showShotoutResult() )
            {
                ?>
                <tr>
                    <td class="legs" colspan="2">
                        <?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_SHOOTOUT');
                        echo " " . $this->showShotoutResult(); ?>
                    </td>
                </tr>
                <?php
            }
        }
		?>
	</table>
	<?php
}
?>

<!-- START of decision info -->
<?php
if ( $this->match->decision_info != '' )
{
	?>
	<table class="matchreport">
		<tr>
			<td>
				<i><?php echo $this->match->decision_info; ?></i>
			</td>
		</tr>
	</table>

	<?php
}
?>
<!-- END of decision info -->
<!-- END: game result -->
