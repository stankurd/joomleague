<?php
//HTMLHelper::_('behavior.tooltip');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$current  = $this->current;
$previous = $this->previousRanking[$this->division];

$config   = $this->tableconfig;

$counter = 1;
$k = 0;
$j = 0;
$temprank = 0;

$columns = explode(",", $config['ordered_columns']);

$show_separation_lines = $config['show_separation_lines'];
$separation_lines_color = $config['separation_lines_color'];
if (!preg_match('/^#[0-9A-Fa-f]{6}$/',$separation_lines_color))
{
  $separation_lines_color = '#000000';
}
$count_teams = count($current);

foreach ($current as $ptid => $team)
{
	if (!isset($this->teams[$ptid]))
	{
		continue;
	}
	$team->team = $this->teams[$ptid];

	//Table colors
	$class = $k == 0 ? $config['style_class1'] : $config['style_class2'];
	$color = "";
	$use_background_row_color_border = "";

	if (isset($this->colors[$j]["from"]) && $counter == $this->colors[$j]["from"])
	{
		$color = $this->colors[$j]["color"];
	}

	if (isset($this->colors[$j]["from"]) && isset($this->colors[$j]["to"]) &&
		($counter > $this->colors[$j]["from"] && $counter <= $this->colors[$j]["to"]))
	{
		$color = $this->colors[$j]["color"];
	}

	if (isset($this->colors[$j]["from"]) && isset($this->colors[$j]["to"]))
	{
		if ($counter == $this->colors[$j]["to"] && $this->colors[$j]["from"] == 1)
		{
			$use_background_row_color_border = "bottom";
		}
		elseif ($counter == $this->colors[$j]["to"] && $this->colors[$j]["from"] == $this->colors[$j]["to"])
		{
			$use_background_row_color_border = "bottom";
		}
		elseif ($counter == $this->colors[$j]["from"] && $this->colors[$j]["to"] == $count_teams)
		{
			$use_background_row_color_border = "top";
		}
		elseif ($counter == $this->colors[$j]["to"] && $this->colors[$j]["to"] != $count_teams)
		{ 
			$use_background_row_color_border = "bottom";
		}
	}
	
	if (isset($this->colors[$j]["to"]) && $counter == $this->colors[$j]["to"])
	{
		$j++;
	}

	//**********Favorite Team
	$format = "%s";
	$favStyle = '';
	if (in_array($team->team->id, explode(",",$this->project->fav_team)) && $this->project->fav_team_highlight_type == 1)
	{
		if (trim($this->project->fav_team_color) != "")
		{
			$color = trim($this->project->fav_team_color);
		}
		$format = "%s";
		$favStyle = ' style="';
		$favStyle .= $this->project->fav_team_text_bold != '' ? 'font-weight:bold;' : '';
		$favStyle .= trim($this->project->fav_team_text_color) != '' ? 'color:'.trim($this->project->fav_team_text_color).';' : '';
		if ($favStyle != ' style="')
		{
			$favStyle .= '"';
		}
		else
		{
			$favStyle = '';
		}
	}

	if (!empty($use_background_row_color_border) && !empty($show_separation_lines))
	{
		if (empty($favStyle) || $favStyle == ' style=""')
		{
			$favStyle = ' style="border-'.$use_background_row_color_border.': '.$show_separation_lines.' '.$separation_lines_color.';"';
		}
		else
		{
			$favStyle = substr($favStyle, 0, -1);
			$favStyle .= ' border-'.$use_background_row_color_border.': '.$show_separation_lines.' '.$separation_lines_color.';"';
		}
	}
	?>
	<tr class="<?php echo $class; ?>" <?php echo $favStyle; ?>>

		<?php
		//**************ranking cell only background
		if ($color != '' && ($config['use_background_row_color'] == 0)): ?>
		<td class="rankingrow" style="background-color: <?php echo $color; ?>; width: 5px !important">&nbsp;</td>
		<?php elseif ($color == '' && ($config['use_background_row_color'] == 0)): ?>
	  	<td class="rankingrow" style="width: 5px !important">&nbsp;</td>
		<?php endif; ?>

		<?php
		//**************rank row
		$backgroundColorStyle = $color != '' && $config['use_background_row_color'] ? ' style="background-color: ' . $color . ';"' : '';
		?>
		<td class="rankingrow_rank" <?php echo $backgroundColorStyle; ?> nowrap="nowrap">
			<?php if ($team->rank != $temprank):
				printf($format, $team->rank);
			else:
				echo "-";
			endif; ?>
		</td>

		<?php if ($this->tableconfig['last_ranking'] == 1):
		//**************Last rank (image)
		?>
		<td class="rankingrow_lastrankimg" <?php echo $backgroundColorStyle; ?>>
		<?php echo JoomleagueHelperHtml::getLastRankImg($team, $previous, $ptid); ?>
		</td>
		<?php
		//**************Last rank (number) ?>
		<td class="rankingrow_lastrank" nowrap="nowrap" <?php echo $backgroundColorStyle; ?>>
		<?php if (isset($previous[$ptid]->rank)):
			echo "(" . $previous[$ptid]->rank . ")";
		endif; ?>
		</td>
		<?php endif;

		$backgroundColor = $color != '' && $config['use_background_row_color'] ? ' background-color: ' . $color . ';' : '';

		//**************logo - jersey
		if ($config['show_picture'] != "no_logo"):
			$width = $this->config['picture_width'] > 20 ? $this->config['picture_width'] + 5 : 30;
			?>
		<td class="rankingrow_logo" style="width: <?php echo $width; ?>px; <?php echo $backgroundColor; ?>;">
		<?php
		//dynamic object property string
		$pic = $this->config['show_picture'];
		$type = 3;
		switch ($pic)
		{
			case 'logo_small':
				$picture = $team->team->$pic;
				$type = 3;
				echo JoomleagueHelper::getPictureThumb($picture, $team->team->name,
					$this->config['picture_width'], $this->config['picture_height'], $type);
				break;
			case 'logo_middle':
				$picture = $team->team->$pic;
				$type = 2;
				echo JoomleagueHelper::getPictureThumb($picture, $team->team->name,
					$this->config['picture_width'], $this->config['picture_height'], $type);
				break;
			case 'logo_big':
				$picture = $team->team->$pic;
				$type = 1;
				echo JoomleagueHelper::getPictureThumb($picture, $team->team->name,
					$this->config['picture_width'], $this->config['picture_height'], $type);
				break;
			case 'country_small':
				$type = 6;
				$pic = 'country';
				if ($team->team->$pic != '' && !empty($team->team->$pic))
				{
					echo Countries::getCountryFlag($team->team->$pic, 'height="11"');
				}
				break;
			case 'country_big':
				$type = 7;
				$pic = 'country';
				if ($team->team->$pic != '' && !empty($team->team->$pic))
				{
					echo Countries::getCountryFlag($team->team->$pic, 'height="50"');
				}
				break;
		} ?>
		</td>
		<?php endif; ?>

		<?php
		//**************Team name ?>
		<td class="rankingrow_teamname" nowrap="nowrap" <?php echo $backgroundColorStyle; ?>>
			<?php
			$isFavTeam = in_array($team->team->id, explode(",",$this->project->fav_team));
			// TODO: ranking deviates from the other views, regarding highlighting of the favorite team(s). Align this...
			$config['highlight_fav'] = $isFavTeam;
			echo JoomleagueHelper::formatTeamName($team->team, 'tr' . $team->team->id, $config, $isFavTeam);
			?>
		</td>
		<?php
		//**********START OPTIONAL COLUMNS DISPLAY
		foreach ($columns AS $c):
		$columnClass = 'rankingrow';	// Set class here because most cases use this value
		switch (trim(strtoupper($c)))
		{
			case 'PLAYED':
				$columnClass = 'rankingrow_played';
				$content = sprintf($format, $team->cnt_matches);
				break;

			case 'WINS':
				if (($config['show_wdl_teamplan_link']) == 1)
				{
					$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($this->project->id, $team->_teamid, 0, 1);
					$content = HTMLHelper::link($teamplan_link, $team->cnt_won);
				}
				else
				{
					$content = sprintf($format, $team->cnt_won);
				}
				break;

			case 'TIES':
				if (($config['show_wdl_teamplan_link']) == 1)
				{
					$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($this->project->id, $team->_teamid, 0, 2);
					$content = HTMLHelper::link($teamplan_link, $team->cnt_draw);
				}
				else
				{
					$content = sprintf($format, $team->cnt_draw);
				}
				break;

			case 'LOSSES':
				if (($config['show_wdl_teamplan_link']) == 1)
				{
					$teamplan_link  = JoomleagueHelperRoute::getTeamPlanRoute($this->project->id, $team->_teamid, 0, 3);
					$content = HTMLHelper::link($teamplan_link, $team->cnt_lost);
				}
				else
				{
					$content = sprintf($format, $team->cnt_lost);
				}
				break;

			case 'WOT':
				$content = sprintf($format, $team->cnt_wot);
				break;

			case 'WSO':
				$content = sprintf($format, $team->cnt_wso);
				break;

			case 'LOT':
				$content = sprintf($format, $team->cnt_lot);
				break;

			case 'LSO':
				$content = sprintf($format, $team->cnt_lso);
				break;

			case 'WINPCT':
				$content = sprintf($format, sprintf("%.3F", ($team->winpct())));
				break;

			case 'GB':
				//GB calculation, store wins and loss count of the team in first place
				if ($team->rank == 1)
				{
					$ref_won = $team->cnt_won;
					$ref_lost = $team->cnt_lost;
				}
				$content =  sprintf($format, round((($ref_won - $team->cnt_won) - ($ref_lost - $team->cnt_lost)) / 2, 1));
				break;

			case 'LEGS':
				$content = sprintf($format, sprintf("%s:%s", $team->sum_team1_legs, $team->sum_team2_legs));
				break;

			case 'LEGS_DIFF':
				$content = sprintf($format, $team->diff_team_legs);
				break;

			case 'LEGS_RATIO':
				$content = sprintf($format, round($team->legsRatio(), 2));
				break;

			case 'SCOREFOR':
				$content = sprintf($format, sprintf("%s" , $team->sum_team1_result));
				break;

			case 'SCOREAGAINST':
				$content = sprintf($format, sprintf("%s", $team->sum_team2_result));
				break;

			case 'SCOREPCT':
				$content = sprintf($format, round($team->scorePct(), 2));
				break;

			case 'RESULTS':
				$content = sprintf($format, sprintf("%s" . ":" . "%s", $team->sum_team1_result, $team->sum_team2_result));
				break;

			case 'DIFF':
				$content = sprintf($format, $team->diff_team_results);
				break;

			case 'POINTS':
				$columnClass = 'rankingrow_points';
				$content = sprintf($format, $team->getPoints());
				break;

			case 'NEGPOINTS':
				$content = sprintf($format, $team->neg_points);
				break;

			case 'OLDNEGPOINTS':
				$content = sprintf($format, sprintf("%s" . ":" . "%s", $team->getPoints(), $team->neg_points));
				break;

			case 'POINTS_RATIO':
				$content =  sprintf($format, round($team->pointsRatio(), 2));
				break;

			case 'BONUS':
				$content = sprintf($format, $team->bonus_points);
				break;

			case 'START':
				if ($team->team->start_points!=0 && $config['show_manipulations'] == 1)
				{
					$toolTipTitle = Text::_('COM_JOOMLEAGUE_START');
					$toolTipText = $team->team->reason;
					$content = '<span class="hasTip" title="' . $toolTipTitle.' :: '. $toolTipText . '">' .
						sprintf($format, $team->team->start_points) . '</span>';
				}
				else
				{
					$content = sprintf($format, $team->team->start_points);
				}
				break;

			case 'QUOT':
				$content = sprintf($format, number_format($team->pointsQuot(), 3, ",", "."));
				break;

			case 'TADMIN':
				$content = sprintf($format, $team->team->username);
				break;

			case 'GFA':
				$content = sprintf($format, round($team->getGFA(), 2));
				break;

			case 'GAA':
				$content = sprintf($format, round($team->getGAA(), 2));
				break;

			case 'PPG':
				$content = sprintf($format, round($team->getPPG(), 2));
				break;

			case 'PPP':
				$content = sprintf($format, round($team->getPPP(), 2));
				break;

			case 'LASTGAMES':
				$columnClass = 'rankingrow lastgames';
				$content = '';
				foreach ($this->previousgames[$ptid] as $g)
				{
					$txt = $this->teams[$g->projectteam1_id]->name . ' [ ' . $g->team1_result . ' - ' .
						$g->team2_result . ' ] ' . $this->teams[$g->projectteam2_id]->name;
					$attribs = array('title' => $txt);
					if (!$img = JoomleagueHelperHtml::getThumbUpDownImg($g, $ptid, $attribs)) {
						continue;
					}
					switch (JoomleagueFrontHelper::getTeamMatchResult($g, $ptid))
					{
						case -1:
							$attr = array('class' => 'thumblost');
							break;
						case 0:
							$attr = array('class' => 'thumbdraw');
							break;
						case 1:
							$attr = array('class' => 'thumbwon');
							break;
					}

					$url = Route::_(JoomleagueHelperRoute::getMatchReportRoute($g->project_slug, $g->slug));
					$content .= HTMLHelper::link($url, $img, $attr);
				}
				break;
		}
		?>
		<td class="<?php echo $columnClass; ?>" <?php echo $backgroundColorStyle; ?>>
			<?php echo $content; ?>
		</td>
	<?php endforeach; ?>
	</tr>
<?php
	$k = 1 - $k;
	$counter++;
	$temprank = $team->rank;
}
