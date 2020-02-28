<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

/**
 * provides html code snippets for the views
 * @author julienV
 */
class JoomleagueHelperHtml {


	/**
	 * Return formatted match time
	 *
	 * @param object $game
	 * @param array $config
	 * @param array $overallconfig
	 * @param object $project
	 * @return string html
	 */
	public static function showMatchTime(&$game, &$config, &$overallconfig, &$project)
	// overallconfig could be deleted here and replaced below by config as both array were merged in view.html.php
	{
		$output='';

		if (!isset($overallconfig['time_format'])) {
			$overallconfig['time_format']='H:i';
		}
		$timeSuffix=Text::_('COM_JOOMLEAGUE_GLOBAL_CLOCK');
		if ($timeSuffix=='COM_JOOMLEAGUE_GLOBAL_CLOCK') {
			$timeSuffix='%1$s&nbsp;h';
		}

		if ($game->match_date)
		{
			$matchTime = JoomleagueHelper::getMatchTime($game, $overallconfig['time_format']);

			if ($config['show_time_suffix'] == 1)
			{
				$output .= sprintf($timeSuffix,$matchTime);
			}
			else
			{
				$output .= $matchTime;
			}

			$config['mark_now_playing']=(isset($config['mark_now_playing'])) ? $config['mark_now_playing'] : 0;

			if ($config['mark_now_playing'])
			{
				$totalMatchDuration = ($project->halftime * ($project->game_parts - 1)) + $project->game_regular_time;
				if ($project->allow_add_time == 1 && ($game->team1_result == $game->team2_result))
				{
					$totalMatchDuration += $project->add_time;
				}
				$project_tz = new DateTimeZone($game->timezone);
				$startTimestamp = JoomleagueHelper::getMatchStartTimestamp($game);
				$startTime = new DateTime($startTimestamp, $project_tz);
				$endTime = new DateTime($startTimestamp, $project_tz);
				$endTime->add(new DateInterval('PT'.$totalMatchDuration.'M'));
				$now = new DateTime('now', $project_tz);
				if ($now >= $startTime && $now <= $endTime)
				{
					$match_begin=$output.' ';
					$title=str_replace('%STARTTIME%',$match_begin,trim(htmlspecialchars($config['mark_now_playing_alt_text'])));
					$title=str_replace('%ACTUALTIME%',self::mark_now_playing($thistime,$match_stamp,$config,$project),$title);
					$styletext='';
					if (isset($config['mark_now_playing_blink']) && $config['mark_now_playing_blink'])
					{
						$styletext=' style="text-decoration:blink"';
					}
					$output='<b><i><acronym title="'.$title.'"'.$styletext.'>';
					$output .= $config['mark_now_playing_text'];
					$output .= '</acronym></i></b>';
				}
			}
		}
		else
		{
			$matchTime='--&nbsp;:&nbsp;--';
			if ($config['show_time_suffix'])
			{
				$output .= sprintf($timeSuffix,$matchTime);
			}
			else
			{
				$output .= $matchTime;
			}
		}

		return $output;
	}

	/**
	 * prints teams names and divisions...
	 *
	 * @param int $projectId
	 * @param object $homeTeam
	 * @param object $guestTeam
	 * @param array $config
	 * @return string html
	 */
	public static function showDivisonRemark($projectId, &$homeTeam, &$guestTeam, &$config)
	{
		$output='';
		if ($config['switch_home_guest'])
		{
			$tmpteam = $homeTeam;
			$homeTeam = $guestTeam;
			$guestTeam = $tmpteam;
		}
		if (isset($homeTeam) && $homeTeam->division_id > 0 && isset($guestTeam) && $guestTeam->division_id > 0)
		{
			//TODO: Where is spacer defined???
			if (!isset($config['spacer']))
			{
				$config['spacer']='/';
			}

			$nametype = 'division_' . $config['show_division_name'];

			if ($config['show_division_link'])
			{
				$link = JoomleagueHelperRoute::getRankingRoute($projectId, null, null, null, 0, $homeTeam->division_id);
				$output .= HTMLHelper::link($link, $homeTeam->$nametype);
			}
			else
			{
				$output .= $homeTeam->$nametype;
			}

			if ($homeTeam->division_id != $guestTeam->division_id)
			{
				$output .= $config['spacer'];
				if ($config['show_division_link'] == 1)
				{
					$link = JoomleagueHelperRoute::getRankingRoute($projectId, null, null, null, 0, $guestTeam->division_id);
					$output .= HTMLHelper::link($link, $guestTeam->$nametype);
				}
				else
				{
					$output .= $guestTeam->$nametype;
				}
			}
		}
		else
		{
			$output .= '&nbsp;';
		}
		return $output;
	}

	/**
	 * Shows matchday title
	 *
	 * @param string $title
	 * @param int $current_round
	 * @param array $config
	 * @param int $mode
	 * @return string html
	 */
	public static function showMatchdaysTitle($title, $current_round, &$config, $mode=0)
	{
		$app 	= Factory::getApplication();
		$jinput = $app->input;
		
		$projectid = $jinput->getInt('p',0);
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$mdlProject->setProjectID($projectid);
		$project = $mdlProject->getProject();
		
		echo ($title != '') ? $title.' - ' : $title;
		if ($current_round > 0)
		{
			$thisround = Table::getInstance('Round','Table');
			$thisround->load($current_round);

			if ($config['type_section_heading'] == 1 && $thisround->name != '')
			{
				if ($mode == 1)
				{
					$link=JoomleagueHelperRoute::getRankingRoute($projectid,$thisround->id);
					echo HTMLHelper::link($link,$thisround->name);
				}
				else
				{
					echo $thisround->name;
				}
			}
			elseif ($thisround->roundcode > 0)
			{
				echo ' '.Text::sprintf('COM_JOOMLEAGUE_RESULTS_MATCHDAY', $thisround->roundcode).'&nbsp;';
			}

			if ($config['show_rounds_dates'] == 1)
			{
				echo " (";
				if (! strstr($thisround->round_date_first,"0000-00-00"))
				{
					echo HTMLHelper::date($thisround->round_date_first .' UTC',
										'COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE',
										JoomleagueHelper::getTimezone($project,$config));
				}
				if (($thisround->round_date_last != $thisround->round_date_first) &&
				(! strstr($thisround->round_date_last,"0000-00-00")))
				{
					echo " - ".HTMLHelper::date($thisround->round_date_last .' UTC',
											'COM_JOOMLEAGUE_GLOBAL_CALENDAR_DATE',
											JoomleagueHelper::getTimezone($project,$config));
				}
				echo ")";
			}
		}
	}

	/**
	 * display match playground
	 *
	 * @param int $projectId
	 * @param array $teams
	 * @param object $game
	 * @param array $config
	 * @return string
	 */
	public static function showMatchPlayground($projectId, $teams, &$game, $config)
	{
		if (($config['show_playground'] || $config['show_playground_alert']) && isset($game->playground_id))
		{
			if (empty($game->playground_id))
			{
				$game->playground_id = $teams[$game->projectteam1_id]->standard_playground;
			}
			if (empty($game->playground_id))
			{
				$cinfo = Table::getInstance('Club','Table');
				$cinfo->load($teams[$game->projectteam1_id]->club_id);
				$game->playground_id = $cinfo->standard_playground;
				$teams[$game->projectteam1_id]->standard_playground = $cinfo->standard_playground;
			}

			if (!$config['show_playground'] && $config['show_playground_alert'])
			{
				if ($teams[$game->projectteam1_id]->standard_playground == $game->playground_id)
				{
					echo '-';
					return '';
				}
			}

			$boldStart	= '';
			$boldEnd	= '';
			$toolTipTitle	= Text::_('COM_JOOMLEAGUE_PLAYGROUND_MATCH');
			$toolTipText	= '';

			if ($config['show_playground_alert'] &&
				$teams[$game->projectteam1_id]->standard_playground != $game->playground_id)
			{
				$boldStart		= '<b style="color:red; ">';
				$boldEnd		= '</b>';
				$toolTipTitle	= Text::_('COM_JOOMLEAGUE_PLAYGROUND_NEW');
			}

			$pginfo = Table::getInstance('Playground','Table');
			$pginfo->load($game->playground_id);

			$toolTipText	.= $pginfo->name . '&lt;br /&gt;';
			$toolTipText	.= $pginfo->address . '&lt;br /&gt;';
			$toolTipText	.= $pginfo->zipcode . ' ' . $pginfo->city . '&lt;br /&gt;';

			$link = JoomleagueHelperRoute::getPlaygroundRoute($projectId,$game->playground_id);
			$playgroundName = $config['show_playground_name'] == 'name' ? $pginfo->name : $pginfo->short_name;
			?>
		<span class='hasTip' title='<?php echo $toolTipTitle; ?> :: <?php echo $toolTipText; ?>'>
			<?php	echo HTMLHelper::link($link, $boldStart . $playgroundName . $boldEnd); ?>
		</span>
	<?php
		}
	}

	/**
	 * mark currently playing game
	 *
	 * @param int $thistime
	 * @param int $match_stamp
	 * @param array $config
	 * @param object $project
	 * @return string
	 */
	public function mark_now_playing($thistime,$match_stamp,&$config,&$project)
	{
		$whichpart=1;
		$gone_since_begin=intval(($thistime - $match_stamp)/60);
		$parts_time=intval($project->game_regular_time / $project->game_parts);
		if ($project->allow_add_time) {
			$overtime=1;
		}else{$overtime=0;
		}
		$temptext=Text::_('COM_JOOMLEAGUE_RESULTS_LIVE_WRONG');
		for ($temp_count=1; $temp_count <= $project->game_parts+$overtime; $temp_count++)
		{
			$this_part_start=(($temp_count-1) * ($project->halftime + $parts_time));
			$this_part_end=$this_part_start + $parts_time;
			$next_part_start=$this_part_end + $project->halftime;
			if ($gone_since_begin >= $this_part_start && $gone_since_begin <= $this_part_end)
			{
				$temptext=str_replace('%PART%',$temp_count,trim(htmlspecialchars($config['mark_now_playing_alt_actual_time'])));
				$temptext=str_replace('%MINUTE%',($gone_since_begin+1 - ($temp_count-1)*$project->halftime),$temptext);
				break;
			}
			elseif ($gone_since_begin > $this_part_end && $gone_since_begin < $next_part_start)
			{
				$temptext=str_replace('%PART%',$temp_count,trim(htmlspecialchars($config['mark_now_playing_alt_actual_break'])));
				break;
			}
		}
		return $temptext;
	}

	/**
	 * return thumb up/down image url if team won/loss
	 *
	 * @param object $game
	 * @param int $projectteam_id
	 * @param array attributes
	 * @return string image html code
	 */
	public static function getThumbUpDownImg($game, $projectteam_id, $attributes = null)
	{
		$res = JoomleagueFrontHelper::getTeamMatchResult($game, $projectteam_id);
		if ($res === false) {
			return false;
		}

		if ($res == 0)
		{
			$img = 'images/com_joomleague/jl_images/draw.png';
			$alt = Text::_('COM_JOOMLEAGUE_GLOBAL_DRAW');
			$title = $alt;
		}
		else if ($res < 0)
		{
			$img = 'images/com_joomleague/jl_images/thumbs_down.png';
			$alt = Text::_('COM_JOOMLEAGUE_GLOBAL_LOST');
			$title = $alt;
		}
		else
		{
			$img = 'images/com_joomleague/jl_images/thumbs_up.png';
			$alt = Text::_('COM_JOOMLEAGUE_GLOBAL_WON');
			$title = $alt;
		}

		// default title attribute, if not specified in passed attributes
		$def_attribs = array('title' => $title);
		if ($attributes) {
			$attributes = array_merge($def_attribs, $attributes);
		}
		else {
			$attributes = $def_attribs;
		}

		return HTMLHelper::image($img, $alt, $attributes);
	}

	/**
	* return thumb up/down image as link with score as title
	*
	* @param object $game
	* @param int $projectteam_id
	* @param array attributes
	* @return string linked image html code
	*/
	public static function getThumbScore($game, $projectteam_id, $attributes = null)
	{
		if (!$img = self::getThumbUpDownImg($game, $projectteam_id, $attributes = null)) {
			return false;
		}
		$txt = $teams[$game->projectteam1_id]->name.' - '.$teams[$game->projectteam2_id]->name.' '.$game->team1_result.' - '. $game->team2_result;

		$attribs = array('title' => $txt);
		if (is_array($attributes)) {
			$attribs = array_merge($attributes, $attribs);
		}
		$url = Route::_(JoomleagueHelperRoute::getMatchReportRoute($game->project_slug, $game->slug));
		return HTMLHelper::link($url, $img);
	}

	/**
	* return up/down image for ranking
	*
	* @param object $team (rank)
	* @param object $previous (rank)
	* @param int $ptid
	* @return string image html code
	*/
	public static function getLastRankImg($team,$previous,$ptid,$attributes = null)
	{
		if ( isset( $previous[$ptid]->rank ) )
		{
			$imgsrc = 'images/com_joomleague/jl_images/';
			if ( ( $team->rank == $previous[$ptid]->rank ) || ( $previous[$ptid]->rank == "" ) )
			{
				$imgsrc .= "same.png";
				$alt	 = Text::_('COM_JOOMLEAGUE_RANKING_SAME');
				$title	 = $alt;
			}
			elseif ( $team->rank < $previous[$ptid]->rank )
			{
				$imgsrc .= "up.png";
				$alt	 = Text::_('COM_JOOMLEAGUE_RANKING_UP');
				$title	 = $alt;
			}
			elseif ( $team->rank > $previous[$ptid]->rank )
			{
				$imgsrc .= "down.png";
				$alt	 = Text::_('COM_JOOMLEAGUE_RANKING_DOWN');
				$title	 = $alt;
			}

		$def_attribs = array('title' => $title);
		if ($attributes) {
			$attributes = array_merge($def_attribs, $attributes);
		}
		else {
			$attributes = $def_attribs;
		}
		return HTMLHelper::image($imgsrc,$alt,$attributes);
		}

	}

	public static function printColumnHeadingSort( $columnTitle, $paramName, $config = null, $default="DESC" )
	{
	    $app 	= Factory::getApplication();
		$output = "";
		$img='';
		if ( $config['column_sorting'] || $config == null)
		{
			$params = array(
					"option" => "com_joomleague",
					"view"   => $app->input->getVar("view", "ranking"),
					"p" => $app->input->getInt( "p", 0 ),
					"r" => $app->input->getInt( "r", 0 ),
					"type" => $app->input->getVar( "type", "" ) );

			if ( $app->input->getVar( 'order', '' ) == $paramName )
			{
				$params["order"] = $paramName;
				$params["dir"] = ( $app->input->getVar( 'dir', '') == 'ASC' ) ? 'DESC' : 'ASC';
				$imgname = 'sort'.($app->input->getVar( 'dir', '') == 'ASC' ? "02" :"01" ).'.gif';
				$img = HTMLHelper::image(
										'images/com_joomleague/jl_images/' . $imgname,
				$params["dir"] );
			}
			else
			{
				$params["order"] = $paramName;
				$params["dir"] = $default;
			}
			$query = Uri::buildQuery( $params );
			echo HTMLHelper::link(
			Route::_( "index.php?".$query ),
			Text::_($columnTitle),
			array( "class" => "jl_rankingheader" ) ).$img;
		}
		else
		{
			echo Text::_($columnTitle);
		}
	}

	public static function nextLastPages( $url, $text, $maxentries, $limitstart = 0, $limit = 10 )
	{
		$latestlimitstart = 0;
		if ( intval( $limitstart - $limit ) > 0 )
		{
			$latestlimitstart = intval( $limitstart - $limit );
		}
		$nextlimitstart = 0;
		if ( ( $limitstart + $limit ) < $maxentries )
		{
			$nextlimitstart = $limitstart + $limit;
		}
		$lastlimitstart = ( $maxentries - ( $maxentries % $limit ) );
		if ( ( $maxentries % $limit ) == 0 )
		{
			$lastlimitstart = ( $maxentries - ( $maxentries % $limit ) - $limit );
		}

		echo '<center>';
		echo '<table style="width: 50%; align: center;" cellspacing="0" cellpadding="0" border="0">';
		echo '<tr>';
		echo '<td style="width: 10%; text-align: left;" nowrap="nowrap">';
		if ( $limitstart > 0 )
		{
			$query = Uri::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => 0 ) );
			echo HTMLHelper::link( $url.$query, '&lt;&lt;&lt;' );
			echo '&nbsp;&nbsp;&nbsp';
			$query = Uri::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $latestlimitstart ) );
			echo HTMLHelper::link( $url.$query, '&lt;&lt;' );
			echo '&nbsp;&nbsp;&nbsp;';
		}
		echo '</td>';
		echo '<td style="text-align: center;" nowrap="nowrap">';
		$players_to = $maxentries;
		if ( ( $limitstart + $limit ) < $maxentries )
		{
			$players_to = ( $limitstart + $limit );
		}
		echo sprintf( $text, $maxentries, ($limitstart+1).' - '.$players_to );
		echo '</td>';
		echo '<td style="width: 10%; text-align: right;" nowrap="nowrap">';
		if ( $nextlimitstart > 0 )
		{
			echo '&nbsp;&nbsp;&nbsp;';
			$query = Uri::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $nextlimitstart ) );
			echo HTMLHelper::link( $url.$query, '&gt;&gt;' );
			echo '&nbsp;&nbsp;&nbsp';
			$query = Uri::buildQuery(
			array(
					"limit" => $limit,
					"limitstart" => $lastlimitstart ) );
			echo HTMLHelper::link( $url.$query, '&gt;&gt;&gt;' );
		}
		echo '</td>';
		echo '</tr>';
		echo '</table>';
		echo '</center>';
	}
	 public static function printColumnHeadingSortAllTimeRanking( $columnTitle, $paramName, $config = null, $default="DESC" )
	{
	    $app = Factory::getApplication();
		$output = "";
		$img='';
		if ( $config['column_sorting'] || $config == null)
		{
			$params = array(
					"option" => "com_joomleague",
					"view"   => $app->input->getVar("view", "rankingalltime"),
					"p" => $app->input->getInt( "p", 0 ),
                    "l" => $app->input->getInt( "l", 0 ),
					"r" => $app->input->getInt( "r", 0 ),
                    "points" => $app->input->getVar( "points", "" ),
					"type" => $app->input->getVar( "type", "" ) );
	
			if ( $app->input->getVar( 'order', '' ) == $paramName )
			{
				$params["order"] = $paramName;
				$params["dir"] = ( $app->input->getVar( 'dir', '') == 'ASC' ) ? 'DESC' : 'ASC';
				$imgname = 'sort'.($app->input->getVar( 'dir', '') == 'ASC' ? "02" :"01" ).'.gif';
				$img = HTMLHelper::image(
										'images/com_joomleague/jl_images/' . $imgname,
				$params["dir"] );
			}
			else
			{
				$params["order"] = $paramName;
				$params["dir"] = $default;
			}
			$query = Uri::buildQuery( $params );
			echo HTMLHelper::link(
			Route::_( "index.php?".$query ),
			Text::_($columnTitle),
			array( "class" => "jl_rankingheader" ) ).$img;
		}
		else
		{
			echo Text::_($columnTitle);
		}
	}

}