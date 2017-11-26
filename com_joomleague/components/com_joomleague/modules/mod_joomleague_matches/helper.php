<?php
/**
 * Joomleague
 * @subpackage	Module-Matches
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

class modMatchesHelper {

	public function __construct(& $params, $id, $match_id = 0) {
		$this->module_id = $id;
		$this->params = $params;
		$itemid = $this->params->get('Itemid');
		$this->itemid = (!empty ($itemid)) ? '&amp;Itemid=' . $itemid : '';
		$this->id = $match_id;
		$this->usedteams = array (
			0 => array ()
		);
		$this->addusedprojects();
		$this->iconpath = ($params->get('use_icons') != '-1') ? _JLMATCHLISTMODURL . 'assets/images/' .
		$params->get('use_icons') . '/' : false;
	}

	public function arrStrToClean(& $string, $sep = ',') {
		if (empty ($string)) {
			return $string;
		}
		$ids = explode($sep, $string);
		JArrayHelper :: toInteger($ids);
		$string = implode($sep, $ids);
		return $string;
	}

	public function getFromDB($query, $key = "", $type = "objlist") {
		$db = JFactory::getDbo();
		$db->setQuery($query);
		switch ($type) {
			case "obj" :
				$result = $db->loadObject($key);
				break;
			case 'arr' :
				$result = $db->loadColumn($key);
				break;
			case 'assc' :
				$result = $db->loadAssocList($key);
				break;
			default :
				$result = $db->loadObjectList($key);
				break;
		}
		if ($this->params->get('debug', 0) == 1) {
			echo $query . '<br />';
		}
		return $result;
	}

	public function jl_utf8_convert($text, $fromenc = 'iso-8859-1', $toenc = 'UTF-8') {
		if (!defined('_MODJLML_JOOMLEAGUE_JC'))
		return $text;
		if (strtolower($fromenc) == strtolower($toenc) || $this->params->get('convert', 1) == 0)
		return $text;

		elseif (function_exists('iconv')) {
			return iconv($fromenc, $toenc, $text);
		}
		elseif (strtolower($fromenc) == 'iso-8859-1' && strtolower($toenc) == 'utf-8') {
			return utf8_encode($text);
		}
		elseif (strtolower($fromenc) == 'utf-8' && strtolower($toenc) == 'iso-8859-1') {
			return utf8_decode($text);
		} else
		return $text;
	}

	public function sortObject($array, $comparefunction, $property = '') {
		$zcount = count($array);
		for ($i = 1; $i < $zcount; $i++) {
			for ($a = $zcount -1; $a >= $i; $a--) {
				if ($this-> $comparefunction ($array[$a -1]-> $property, $array[$a]-> $property) > 0) {
					$tempzal = $array[$a -1];
					$array[$a -1] = $array[$a];
					$array[$a] = $tempzal;
				}
			}
		}
		return $array;
	}

	public function sortArray($array, $comparefunction, $property = '') {
		$zcount = count($array);
		for ($i = 1; $i < $zcount; $i++) {
			for ($a = $zcount -1; $a >= $i; $a--) {
				if ($this-> $comparefunction ($array[$a -1][$property], $array[$a][$property]) > 0) {
					$tempzal = $array[$a -1];
					$array[$a -1] = $array[$a];
					$array[$a] = $tempzal;
				}
			}
		}
		return $array;
	}

	private function asc($a, $b) {
		if ($a < $b)
		return -1;
		if ($a == $b)
		return 0;
		return 1;
	}

	private function desc($a, $b) {
		if ($a > $b)
		return -1;
		if ($a == $b)
		return 0;
		return 1;
	}

	public function array_trim($a) {
		$j = 0;
		$b = array ();
		for ($i = 0; $i < count($a); $i++) {
			if (trim($a[$i]) != "") {
				$b[$j++] = $a[$i];
			} else
			$b[$j++] = '-';
		}
		return $b;
	}

	private function addusedprojects() {
		$usedp = $this->params->get('project');
		if (is_array($usedp)) {
			foreach ($usedp AS $p) {
				$this->usedteams[$p] = array ();
			}
		}
		elseif (!empty ($usedp)) {
			$this->usedteams[$usedp] = array ();
		}
	}

	public function usedteamscheck($team_id, $project_id) {
		return ((isset ($this->usedteams[0]) && is_array($this->usedteams[0]) && in_array($team_id, $this->usedteams[0])) || (isset ($this->usedteams[$project_id]) && is_array($this->usedteams[$project_id]) && in_array($team_id, $this->usedteams[$project_id]))) ? 1 : 0;
	}

	public function addteamicon($which) {
		$path = ($this->iconpath) ? $this->iconpath . 'teamlinks/' : false;
		if ($path) {
			return JHtml :: _('image', $path . $which . '.png', $this->params->get($which . '_text'), 'title="' . $this->params->get($which . '_text') . '"');
		} else {
			return $this->params->get($which . '_text') . '<br />';
		}
	}

	public function getTeamDetails(& $team, $nr) {
		if (!is_object($team))
		return false;
		$usedname = $this->params->get('team_names');
		$class = 'jlmlTeamname';
		$isusedteam = $this->usedteamscheck($team->id, $team->project_id);
		if ($isusedteam) {
			$class .= ' jlmlusedteam';
		}
		if (($this->params->get('link_teams', 0) + $isusedteam) >= 2)
		$class .= " jlmhaslinks";
		$teamdetails['logo'] = $this->getpix($team); //returns false if logos are not to be shown
		$teamdetails['name'] = '<span class="' . $class . '" id="jlmlTeamname' . $team->id . 'mod' . $this->module_id . 'nr' . $nr . '">';
		switch ($usedname) {
			case "short_name" :
				$teamdetails['name'] .= '<acronym title="' .
				$this->jl_utf8_convert($team->name, 'iso-8859-1', 'utf-8') . '">' .
				$this->jl_utf8_convert($team->short_name, 'iso-8859-1', 'utf-8') .
				'</acronym>';
				break;
			default :
				$teamdetails['name'] .= ($team-> $usedname != '') ? $this->jl_utf8_convert($team-> $usedname, 'iso-8859-1', 'utf-8') : $this->jl_utf8_convert($team->name, 'iso-8859-1', 'utf-8');
				break;
		}
		$teamdetails['name'] .= '</span>';
		return $teamdetails;
	}

	public function getpix($team) {
		$pt = $this->params->get('picture_type');
		if ($this->params->get('show_picture') == 0)
		return false;
		$appendimage = ' align="middle" ';
		$countrycode = $team->country;
		if($pt=="country" && strlen($countrycode)==3) {
			$pic['src'] = Countries::getIso3Flag($countrycode);
			$pic['alt'] = Countries::getCountryName($countrycode);
		} else {
			$defaultlogos = $this->getDefaultLogos();
			$matchpart_pic = (!empty ($team-> $pt) && file_exists(JPATH_ROOT.'/'.$team-> $pt)) ? $team-> $pt : $defaultlogos[$pt];
			if (file_exists(JPATH_ROOT.'/'.$matchpart_pic)) {
				$size = getimagesize(JPATH_ROOT.'/'.$matchpart_pic);
				$pic_width = $size[0];
				$pic_height = $size[1];
				$whichparam = ($pic_width > $pic_height) ? ' width' : ' height';
				if ($this->params->get('xsize') > 0)
				$appendimage .= $whichparam . '="' . $this->params->get('xsize') . '"';
				elseif ($this->params->get('ysize') > 0) $appendimage .= $whichparam . '="' . $this->params->get('ysize') . '"';
			}
			$pic['src'] = (trim($matchpart_pic) != "" && file_exists(JPATH_ROOT.'/'.trim($matchpart_pic))) ? JUri :: root(true) .
			'/' . $matchpart_pic : JUri :: root(true) . '/' . $defaultlogos[$pt];
			$pic['alt'] = $this->jl_utf8_convert($team->name, 'iso-8859-1', 'utf-8');
		}
		$pic['append'] = $appendimage;
		return $pic;
	}

	public function formatResults(& $row, & $match) {
		$live = ($match->live == 'z') ? 0 : 1;
		$mrt = array (
		0 => array (
		0 => '',
		1 => ''
		),
		1 => array (
		0 => JText :: _('AET'),
		1 => JText :: _('IET')
		),
		2 => array (
		0 => JText :: _('ONP'),
		1 => JText :: _('INP')
		)
		);
		$partresults = '';
		if ($this->params->get('part_result') == 1 && trim(str_replace(';', '', $match->team1_result_split)) != '') {
			$homepartresults = $this->array_trim(explode(";", $match->team1_result_split));
			$guestpartresults = $this->array_trim(explode(";", $match->team2_result_split));
			$cntmp = ($match->game_parts + $this->params->get('part_result_count'));
			for ($x = 0; $x < $cntmp; $x++) {
				$highlight_parts = false;
				if ($x == ($match->game_parts + $this->params->get('part_result_count')))
				break;
				if (isset ($homepartresults[$x]) && isset ($guestpartresults[$x])) {

					if (is_null($match->team1_result) && ($x +1) <= $cntmp && (!isset ($homepartresults[$x +1]) OR $homepartresults[$x +1] == '-') AND $homepartresults[$x] != '-' AND $match->match_result_type == 0) {
						$highlight_parts = true;
					}
					if ($x == 0)
					$partresults .= '(';
					if ($x != 0)
					$partresults .= $this->params->get('part_results_separator');
					if ($live != 'z' && $highlight_parts)
					$partresults .= '<span class="jlml_livescore">';
					$partresults .= $homepartresults[$x] . ':' . $guestpartresults[$x];
					if ($live != 'z' && $highlight_parts)
					$partresults .= '</span>';

				} else
				break;
			}

			if (!is_null($match->team1_result_ot)) {
				array_push($homepartresults, $match->team1_result_ot);
				array_push($guestpartresults, $match->team2_result_ot);
				$partresults .= ' - ';
				if (is_null($match->team1_result))
				$partresults .= '<span class="jlml_livescore">';
				$partresults .= JText :: _('IET') . ' ' . $match->team1_result_ot . ':' . $match->team2_result_ot;
				if (is_null($match->team1_result))
				$partresults .= '</span>';
			}
			if (trim($partresults) != '')
			$partresults .= ')';
		}
		if ($live != 'z' && trim(str_replace(';', '', $match->team1_result_split)) != '' && is_null($match->team1_result)) {
			if (isset ($homepartresults) && count($homepartresults) > 0) {
				$row['homescore'] = array_sum($homepartresults);
			}
			if (isset ($guestpartresults) && count($guestpartresults) > 0) {
				$row['awayscore'] = array_sum($guestpartresults);
			}
			$row['homescore'] = '<span class="jlml_livescore">' . $row['homescore'] . '</span>';
			$row['awayscore'] = '<span class="jlml_livescore">' . $row['awayscore'] . '</span>';
		}
		$row['partresults'] = $partresults;

		$row['result'] = $row['homescore'] . $this->params->get('team_separator') . $row['awayscore'];
		if ($live == 0)
		$row['result'] .= $mrt[$match->match_result_type][$live];
	}

	public function createMatchInfo(& $row, $match) {
		$row['notice'] = ($match->match_result_detail != '' && $this->params->get('show_match_notice') == 1) ? $match->match_result_detail : '';
		if ($this->params->get('show_referee', 1) == 1 && $match->refname != '') {
			$row['referee'] = '<span style="float:right;">';
			$row['referee'] .= ($this->iconpath) ? JHtml :: _('image', $this->iconpath . 'referee.png', 'Referee', array (
				'title' => 'Referee',
				'height' => '16',
				'width' => '16'
				)) : 'Referee: ';
				$row['referee'] .= $this->jl_utf8_convert($match->refname, 'iso-8859-1', 'utf-8') . '</span>';
		} else {
			$row['referee'] = '';
		}
		if ($this->params->get('show_spectators', 1) == 1 && $match->crowd > 0) {
			$row['spectators'] = '<span style="float:left;">';
			$row['spectators'] .= ($this->iconpath) ? JHtml :: _('image', $this->iconpath . 'spectators.png', 'Spectators', array (
				'title' => 'Spectators',
				'height' => '16',
				'width' => '16'
				)) : 'Spectators: ';
				$row['spectators'] .= number_format($match->crowd, 0, ',', '.') . '</span>';
				;
		} else {
			$row['spectators'] = '';
		}
	}

	public function formatMatches(& $matches) {
		if ($this->params->get('lastsortorder') == 'desc') {
			$matches = $this->sortObject($matches, 'desc', 'alreadyplayed');
		}
		if ($this->params->get('upcoming_first', 1) == 1) {
			$matches = $this->sortObject($matches, 'asc', 'upcoming');
		}

		$matches = $this->sortObject($matches, 'asc', 'actplaying');
		$matches = $this->sortObject($matches, 'asc', 'live');
		$teams = $this->getTeamsFromMatches($matches);
		$rows = array ();
		$useicons = $this->iconpath;
		$cnt = JRequest :: getVar('nr', 0, 'default', 'POST');
		$hteam = false;
		$ateam = false;
		foreach ((array) $matches AS $key => $match) {
			//echo 'NOW(): '.JHtml::_('date', $match->currenttime, $this->params->get('dateformat').' '.$this->params->get('timeformat'),0).'<br />';
			if ($match->projectteam1_id) {
				$hteam = $teams[$match->projectteam1_id];
			}
			if ($match->projectteam2_id) {
				$ateam = $teams[$match->projectteam2_id];
			}
			//$matches[$key]->live = false;
			$rows[$match->match_id]['type'] = 'undefined';
			if ($this->params->get('show_status_notice') == 1) {
				if ($match->live != 'z') {
					$rows[$match->match_id]['type'] = 'live';
				}
				else {
					$matches[$key]->live = false;
					if ($match->actplaying != 'z') {
						$rows[$match->match_id]['type'] = 'actplaying';
					}
					elseif ($match->alreadyplayed != 0) {
						$rows[$match->match_id]['type'] = 'alreadyplayed';
					}
					elseif ($match->upcoming != 'z') {
						$rows[$match->match_id]['type'] = 'upcoming';
					}
				}
			}
			$rows[$match->match_id]['date'] = JoomleagueHelper::getMatchDate($match, JText::_($this->params->get('dateformat')));
			if ($useicons) {
				$rows[$match->match_id]['date'] = JHtml::_('image', $this->iconpath . 'date.png', 'Datum', array (
						'title' => 'Datum',
						'height' => '16',
						'width' => '16'
				)) .
				' ' . $rows[$match->match_id]['date'];
			}
			$rows[$match->match_id]['time'] = JoomleagueHelper::getMatchTime($match, JText::_($this->params->get('timeformat')));

			if ($useicons) {
				$rows[$match->match_id]['time'] = JHtml :: _('image', $this->iconpath . 'time.png', 'Beginn', array (
						'title' => 'Beginn',
						'height' => '16',
						'width' => '16'
				)) .
				' ' . $rows[$match->match_id]['time'];
			}

			if (isset ($match->meeting)) {
				$rows[$match->match_id]['meeting'] = JHtml :: _('date', $match->meetingtime, $this->params->get('timeformat'), null);
				if ($useicons) {
					$rows[$match->match_id]['meeting'] = JHtml :: _('image', $this->iconpath . 'time_go.png', 'Treffpunkt', array (
							'title' => 'Treffpunkt',
							'height' => '16',
							'width' => '16'
					)) .
					' ' . $rows[$match->match_id]['meeting'];
				}
			}
			$rows[$match->match_id]['project_id'] = $match->project_id;
			$rows[$match->match_id]['totaltime'] = $match->totaltime;
			$rows[$match->match_id]['round_id'] = $match->round_id;
			$rows[$match->match_id]['hometeam'] = $this->getTeamDetails($hteam, $cnt);
			$rows[$match->match_id]['homescore'] = (!is_null($match->team1_result)) ? $match->team1_result : '-';
			$rows[$match->match_id]['homeover'] = $this->buildTeamLinks($hteam, $cnt);
			$rows[$match->match_id]['awayteam'] = $this->getTeamDetails($ateam, $cnt);
			$rows[$match->match_id]['awayscore'] = (!is_null($match->team2_result)) ? $match->team2_result : '-';
			$rows[$match->match_id]['awayover'] = $this->buildTeamLinks($ateam, $cnt);
			$this->createMatchInfo($rows[$match->match_id], $match);
			$this->formatHeading($rows[$match->match_id], $match);
			$this->formatResults($rows[$match->match_id], $match);
			$this->createMatchLinks($rows[$match->match_id], $match);
			$this->createLocation($rows[$match->match_id], $match, $hteam);
			$this->createAjaxMenu($match, $cnt);
			$rows[$match->match_id]['ajax'] = $match->ajax;
			$cnt++;
		}
		return $rows;
	}

	public function getTimeLimit() {
		$livematchestime = "IF((p.allow_add_time > 0), ((p.game_regular_time+(p.game_parts * p.halftime)) + p.add_time), (p.game_regular_time+(p.game_parts * p.halftime)))";
		$timeforfirstmatch = "DATE_SUB(" . $this->getDateString() . ", INTERVAL $livematchestime MINUTE) > UTC_TIMESTAMP()";
		//$timeforfirstmatch = $this->getDateString() . " > NOW()";
		if ($this->params->get('show_played', 0) == 1 && ($this->params->get('result_add_time', 0)) > 0) {
			$timeforfirstmatch = $this->getDateString() . " > DATE_SUB(UTC_TIMESTAMP(), INTERVAL " . intval($this->params->get('result_add_time', 0)) . " " . $this->params->get('result_add_unit') . ")";
		}
		$timeforlastmatch = ($this->params->get('period_int', 0) > 0) ? $this->getDateString() . " < DATE_ADD(UTC_TIMESTAMP(), INTERVAL " . intval($this->params->get('period_int')) . " " . $this->params->get('period_string') . ")" : '';
		$wheretime = "(" . $timeforfirstmatch;
		if (!empty ($timeforlastmatch))
		$wheretime .= " AND " . $timeforlastmatch;
		$wheretime .= ")";
		return $wheretime;
	}

	public function createAjaxMenu(& $row, $cnt) {
		if ($this->params->get('next_last', 0) == 0) {
			$row->ajax = false;
			return false;
		}
		if ( $this->params->get('nextlast_from_same_project')== 0) {
			$this->next_last2($row);
		}
		else {
		$this->next_last($row);
		}
		$origin = JRequest :: getVar('origin', $row->match_id, 'default', 'POST');
		$jsfunc = "jlml_loadMatch('%s', '%s', '" . $this->module_id . "', '" . $cnt . "', '%s')";
		$options = array (
			'height' => '16',
			'width' => '16',
			'onclick' => 'alert(\'coming soon...\')',
			'style' => 'cursor:pointer;'
			);
			// start ajaxifying
			$showhome = (($this->params->get('next_last') + $this->usedteamscheck($row->team1_id, $row->project_id)) >= 2);
			$showaway = (($this->params->get('next_last') + $this->usedteamscheck($row->team2_id, $row->project_id)) >= 2);
			$temp = '<div class="jlmlext_ajaxmenu" style="text-align:center;width:100%;display:block;clear:both;margin-top:10px;">';
			if ($showhome && ($row->lasthome OR $row->nexthome)) {
				$temp .= '<span style="float:left">';
				if ($row->lasthome) {
					$tmp = $options;
					$tmp['title'] = 'Previous Match';
					$tmp['onclick'] = sprintf($jsfunc, $row->team1_id, $row->lasthome, $origin);
					$alt = $tmp['title'];
					if ($this->iconpath && $this->params->get('icons_for_ajax') == 1)
					$temp .= JHtml :: _('image', $this->iconpath . 'page_prev.png', $alt, $tmp);
					else
					$temp .= '<input type="button" class="' . $this->params->get('reset_class') . '" value="' . $this->params->get('last_text') . '" style="cursor:pointer;" onclick="' . $tmp['onclick'] . '" />';
				}
				if ($row->nexthome) {
					$tmp = $options;
					$tmp['title'] = 'next Match';
					$tmp['onclick'] = sprintf($jsfunc, $row->team1_id, $row->nexthome, $origin);
					$alt = $tmp['title'];
					if ($this->iconpath && $this->params->get('icons_for_ajax') == 1)
					$temp .= JHtml :: _('image', $this->iconpath . 'page_next.png', $alt, $tmp);
					else
					$temp .= '<input type="button" class="' . $this->params->get('reset_class') . '" value="' . $this->params->get('next_text') . '" style="cursor:pointer;" onclick="' . $tmp['onclick'] . '" />';
				}
				$temp .= '</span>';
			}
			if ($showaway && ($row->lastaway OR $row->nextaway)) {
				$temp .= '<span style="float:right">';
				if ($row->lastaway) {
					$tmp = $options;
					$tmp['title'] = 'Previous Match';
					$tmp['onclick'] = sprintf($jsfunc, $row->team2_id, $row->lastaway, $origin);
					$alt = $tmp['title'];
					if ($this->iconpath && $this->params->get('icons_for_ajax') == 1)
					$temp .= JHtml :: _('image', $this->iconpath . 'page_prev.png', $alt, $tmp);
					else
					$temp .= '<input type="button" class="' . $this->params->get('reset_class') . '" value="' . $this->params->get('last_text') . '" style="cursor:pointer;" onclick="' . $tmp['onclick'] . '" />';
				}
				if ($row->nextaway) {
					$tmp = $options;
					$tmp['title'] = 'next Match';
					$tmp['onclick'] = sprintf($jsfunc, $row->team2_id, $row->nextaway, $origin);
					$alt = $tmp['title'];
					if ($this->iconpath && $this->params->get('icons_for_ajax') == 1)
					$temp .= JHtml :: _('image', $this->iconpath . 'page_next.png', $alt, $tmp);
					else
					$temp .= '<input type="button" class="' . $this->params->get('reset_class') . '" value="' . $this->params->get('next_text') . '" style="cursor:pointer;" onclick="' . $tmp['onclick'] . '" />';
				}
				$temp .= '</span>';
			}
			if ($this->params->get('reset_start_match') == 1 && $origin != $row->id) {
				$temp .= '<span style="float:none;">';
				$tmp = $options;
				$tmp['title'] = 'Reset';
				$tmp['onclick'] = sprintf($jsfunc, '0', $origin, $origin);
				$alt = $tmp['title'];
				if ($this->iconpath && $this->params->get('icons_for_ajax') == 1)
				$temp .= JHtml :: _('image', $this->iconpath . 'page_reset.png', $alt, $tmp);
				else
				$temp .= '<input type="button" class="' . $this->params->get('reset_class') . '" value="' . $this->params->get('reset_text') . '" style="cursor:pointer;" onclick="' . $tmp['onclick'] . '" />';
				$temp .= '</span>';
			}
			$temp .= '</div>';
			$row->ajax = $temp;
	}

	public function arrayToUri(& $arr) {
		if (!is_array($arr))
		return false;
		$str = '';
		foreach ($arr AS $key => $val) {
			$str .= '&amp;' . $key . '=' . $val;
		}
		return $str;
	}
}