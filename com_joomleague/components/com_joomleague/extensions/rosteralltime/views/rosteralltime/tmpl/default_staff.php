<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;
// Show team-staff as defined
if (count($this->stafflist) > 0)
{
	// Lock the tables for staff to that of players, so that columns are vertically aligned
	// NOT USED in next lines means that this info is not applicable for staff
	//  1. Position number, NOT USED  (optional : $this->config['show_player_numbers'])
	//  2. Player picture   (optional : $this->config['show_player_icon'])
	//  3. Country flag     (optional : $this->config['show_country_flag'])
	//  4. Player name
	//  5. Injured/suspended/away icons, , NOT USED
	//  6. Birthday         (optional : $this->config['show_birthday'])
	//  7. Games played     (optional, NOT USED : $this->overallconfig['use_jl_substitution'] && $this->config['show_games_played'])
	//  7. Staff position   (only for staff)
	//  8. Starting line-up (optional, NOT USED : $this->overallconfig['use_jl_substitution'] && $this->config['show_substitution_stats'])
	//  9. In               (optional, NOT USED : $this->overallconfig['use_jl_substitution'] && $this->config['show_substitution_stats'])
	// 10. Out              (optional, NOT USED : $this->overallconfig['use_jl_substitution'] && $this->config['show_substitution_stats'])
	// 10. Event type       (optional, NOT USED : $this->config['show_events_stats'] && count($this->playereventstats) > 0,
	//                       multiple columns possible (depends on the number of event types for the position))
	// 11. Stats type       (optional, NOT USED : $this->config['show_stats'] && isset($this->stats[$row->position_id]),
	//                       multiple columns possible (depends on the number of stats types for the position))

	$positionHeaderSpan = 0;
	$dummyColumnSpan = 0;
	if ($this->config['show_player_numbers'])
	{
		$positionHeaderSpan++;
		$dummyColumnSpan++;
	}
	if ($this->config['show_player_icon'] || $this->config['show_staff_icon'])
	{
		$positionHeaderSpan++;
	}
	if ($this->config['show_country_flag'] || $this->config['show_country_flag_staff'])
	{
		$positionHeaderSpan++;
	}
	// Player name and injured/suspended/away columns are always there
	$positionHeaderSpan += 2;

	?>
	<br>
	<table width="100%" class="contentpaneopen">
		<tr>
			<td class="contentheading">
				<?php
				echo '&nbsp;';
				if ($this->config['show_team_shortform'] == 1)
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ROSTER_STAFF_OF2',$this->team->name, $this->team->short_name);
				}
				else
				{
					echo Text::sprintf('COM_JOOMLEAGUE_ROSTER_STAFF_OF',$this->team->name);
				}
				?>
			</td>
		</tr>
	</table>
	<br />
	<table width="100%" style="text-align:center; " border="0" cellpadding="0" cellspacing="0">
		<thead>
			<tr class="sectiontableheader rosterheader">
				<th width="60%" colspan="<?php echo $positionHeaderSpan; ?>">
					<?php echo Text::_("COM_JOOMLEAGUE_ROSTER_STAFF").'&nbsp;'; ?>
				</th>
				<?php
				if ($this->config['show_birthday_staff'] > 0)
				{ ?>
				<th class="td_c">
			  	<?php
					switch ( $this->config['show_birthday'] )
					{
						case 	1:			// show Birthday and Age
											$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY_AGE';
											break;

						case 	2:			// show Only Birthday
											$outputStr = 'COM_JOOMLEAGUE_PERSON_BIRTHDAY';
											break;

						case 	3:			// show Only Age
											$outputStr = 'COM_JOOMLEAGUE_PERSON_AGE';
											break;

						case 	4:			// show Only Year of birth
											$outputStr = 'COM_JOOMLEAGUE_PERSON_YEAR_OF_BIRTH';
											break;
					}
					echo Text::_( $outputStr );
				?>
				</th><?php
				}
				elseif ($this->config['show_birthday'] > 0)
				{
					// Put empty column to keep vertical alignment with the player table
					?>
				<th class="td_c">&nbsp;</th><?php
				} ?>
				<th><?php echo Text::_('COM_JOOMLEAGUE_ROSTER_STAFF_FUNCTION'); ?></th>
			</tr>
		</thead>
		<?php
			$k=0;
			for ($i=0, $n=count($this->stafflist); $i < $n; $i++)
			{
				$row = $this->stafflist[$i];
				?>
			<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
				<?php
				if ($this->config['show_player_numbers'])
				{
					?>
				<td width="30" class="td_c">&nbsp;</td><?php
				}
				$playerName = JoomleagueHelper::formatName(null, $row->firstname,
															$row->nickname,
															$row->lastname,
															$this->config["name_format_staff"]);
				if ($this->config['show_staff_icon'])
				{
					$picture = $row->picture;
					if ((empty($picture)) || ($picture == JoomleagueHelper::getDefaultPlaceholder("player") ))
					{
						$picture = $row->ppic;
					}
					if ( !file_exists( $picture ) )
					{
						$picture = JoomleagueHelper::getDefaultPlaceholder("player");
					} ?>
				<td width="40" class="td_c" nowrap="nowrap"><?php
					echo JoomleagueHelper::getPictureThumb($picture, $playerName,
															$this->config['staff_picture_width'],
															$this->config['staff_picture_height']);
					?>
				</td>
				<?php
				}
				elseif ($this->config['show_player_icon'])
				{
					// Put empty column to keep vertical alignment with the player table
					?>
				<td width="40" class="td_c" nowrap="nowrap">&nbsp;</td><?php
				}
				if ($this->config['show_country_flag_staff'])
				{ ?>
				<td width="16" nowrap="nowrap" style="text-align:center; ">
					<?php echo Countries::getCountryFlag($row->country);?>
				</td><?php
				}
				elseif ($this->config['show_country_flag'])
				{
					// Put empty column to keep vertical alignment with the player table
					?>
				<td width="16" nowrap="nowrap" style="text-align:center; ">&nbsp;</td><?php
				}
				?>
				<td class="td_l"><?php
				if ($this->config['link_staff']==1)
				{
					$link=JoomleagueHelperRoute::getStaffRoute($this->project->slug,$this->team->slug,$row->slug);
					echo HTMLHelper::link($link, '<span class="staffname">'. $playerName.'</span>');
				}
				else
				{
					echo '<span class="staffname">'.$playerName.'</i>';
				} ?>
				</td>

				<td width="5%" style="text-align: center;" class="nowrap">
				<?php

				$mdlStaff = BaseDatabaseModel::getInstance("Staff", "JoomleagueModel");
				$this->stafftool=$mdlStaff->getTeamStaffByRound($this->project->current_round, $row->pid);

				$today = HTMLHelper::date('now' .' UTC',
								Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
								JoomleagueHelper::getTimezone($this->project, $this->overallconfig));

				if (!empty($this->stafftool->injury))
				{
					$injury_date = "";
					$injury_end  = "";
					$injury_text = "";

					$injury_date = HTMLHelper::date($this->stafftool->injury_date .' UTC',
												Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
												JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rinjury_from))
					$injury_date .= " (".$this->stafftool->rinjury_from.")";

					$injury_end = HTMLHelper::date($this->stafftool->injury_end .' UTC',
												Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
												JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rinjury_to))
					$injury_end .= " (".$this->stafftool->rinjury_to.")";

					$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_INJURED');

					if ($this->stafftool->injury_date == $this->stafftool->injury_end)
					{
						if (($injury_date == $today) || (!empty($injury_date)))
						{
							$injury_text = $imageTitle;
						}
					}
					else
					{
						if ($injury_date != $today)
						{
							$injury_text = Text::_('COM_JOOMLEAGUE_PERSON_INJURY_DATE')." ".$injury_date;
						}
						if ($injury_end != $today)
						{
							$injury_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_INJURY_END')." ".$injury_end;
						}
					}

					if (!empty($this->stafftool->injury_detail))
					{
						$injury_detail = htmlspecialchars( $this->stafftool->injury_detail );
						$injury_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_INJURY_TYPE')." ".$injury_detail;
					}
					echo HTMLHelper::image('images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/injured.gif',
										$imageTitle,array('title'=> $injury_text,'height'=> 20));

				}

				if (!empty($this->stafftool->suspension))
				{
					$suspension_date = "";
					$suspension_end  = "";
					$suspension_text = "";

					$suspension_date = HTMLHelper::date($this->stafftool->suspension_date .' UTC',
													Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
													JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rsusp_from))
					$suspension_date .= " (".$this->stafftool->rsusp_from.")";

					$suspension_end = HTMLHelper::date($this->stafftool->suspension_end .' UTC',
													Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
													JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rsusp_to))
					$suspension_end .= " (".$this->stafftool->rsusp_to.")";

					$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_SUSPENDED');

					if ($this->stafftool->suspension_date == $this->stafftool->suspension_end)
					{
						if (($suspension_date == $today) || (!empty($suspension_date)))
						{
							$suspension_text = $imageTitle;
						}
					}
					else
					{
						if ($suspension_date != $today)
						{
							$suspension_text = Text::_('COM_JOOMLEAGUE_PERSON_SUSPENSION_DATE')." ".$suspension_date;
						}
						if ($suspension_end != $today)
						{
							$suspension_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_SUSPENSION_END')." ".$suspension_end;
						}
					}

					if (!empty($this->stafftool->suspension_detail))
					{
						$suspension_detail = htmlspecialchars( $this->stafftool->suspension_detail );
						$suspension_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_SUSPENSION_REASON')." ".$suspension_detail;
					}
					echo HTMLHelper::image('images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/suspension.gif',
										$imageTitle,array('title'=> $suspension_text,'height'=> 20));

				}

				if (!empty($this->stafftool->away))
				{
					$away_date = "";
					$away_end  = "";
					$away_text = "";

					$away_date = HTMLHelper::date($this->stafftool->away_date .' UTC',
													Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
													JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rsusp_from))
					$away_date .= " (".$this->stafftool->rsusp_from.")";

					$away_end = HTMLHelper::date($this->stafftool->away_end .' UTC',
													Text::_('COM_JOOMLEAGUE_GLOBAL_MATCHDAYDATE'),
													JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					if(isset($this->stafftool->rsusp_to))
					$away_end .= " (".$this->stafftool->rsusp_to.")";

					$imageTitle=Text::_('COM_JOOMLEAGUE_PERSON_AWAY');

					if ($this->stafftool->away_date == $this->stafftool->away_end)
					{
						if (($away_date == $today) || (!empty($away_date)))
						{
							$away_text = $imageTitle;
						}
					}
					else
					{
						if ($away_date != $today)
						{
							$away_text = Text::_('COM_JOOMLEAGUE_PERSON_AWAY_DATE')." ".$away_date;
						}
						if ($away_end != $today)
						{
							$away_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_AWAY_END')." ".$away_end;
						}
					}

					if (!empty($this->stafftool->away_detail))
					{
						$away_detail = htmlspecialchars( $this->stafftool->away_detail );
						$away_text .= "&#013;".Text::_('COM_JOOMLEAGUE_PERSON_AWAY_REASON')." ".$away_detail;
					}
					echo HTMLHelper::image('images/com_joomleague/database/events/'.$this->project->fs_sport_type_name.'/away.gif',
										$imageTitle,array('title'=> $away_text,'height'=> 20));

				}
				?>
				</td>
				<?php
				if ($this->config['show_birthday_staff'] > 0)
				{
					?>
				<td width="10%" nowrap="nowrap" style="text-align: center;"><?php
					if ($row->birthday !="0000-00-00")
					{
						switch ($this->config['show_birthday_staff'])
						{
							case 1:	 // show Birthday and Age
								$birthdateStr = HTMLHelper::date($row->birthday, Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'), $this->overallconfig['time_zone']);
								$birthdateStr.="&nbsp;(".JoomleagueHelper::getAge($row->birthday, $row->deathday).")";
								break;
							case 2:	 // show Only Birthday
								$birthdateStr = HTMLHelper::date($row->birthday, Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'), $this->overallconfig['time_zone']);
								break;
							case 3:	 // show Only Age
								$birthdateStr = "(".JoomleagueHelper::getAge($row->birthday, $row->deathday).")";
								break;
							case 4:	 // show Only Year of birth
								$birthdateStr = HTMLHelper::date($row->birthday, 'Y');
								break;
							default:
								$birthdateStr = "";
								break;
						}
					}
					else
					{
						$birthdateStr="-";
					}
					// deathday
					if ( $row->deathday !="0000-00-00" )
					{
						$birthdateStr .= ' [ &dagger; '.HTMLHelper::date($row->deathday, Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'), $this->overallconfig['time_zone']).']';
					}

					echo $birthdateStr;
				?>
				</td><?php
				}
				elseif ($this->config['show_birthday'] > 0)
				{
					?>
				<td width="10%" nowrap="nowrap" style="text-align: left;">&nbsp;</td><?php
				} ?>
				<td width="30%"><?php
				$staff_position = '';
				switch ($this->config['staff_position_format'])
				{
					case 2:	 // show member with text
								$staff_position = Text::sprintf('COM_JOOMLEAGUE_ROSTER_MEMBER_OF',Text::_($row->parentname));
								break;

					case 3:	 // show function with text
								$staff_position .= Text::sprintf('COM_JOOMLEAGUE_ROSTER_FUNCTION_IS',Text::_($row->position));
								break;

					case 4:	 // show only function
								$staff_position = Text::_($row->parentname);
								break;

					case 5:	 // show only position
								$staff_position = Text::_($row->position);
								break;

					default: // show member+function with text
								$staff_position = Text::sprintf('COM_JOOMLEAGUE_ROSTER_MEMBER_OF',Text::_($row->parentname));
								$staff_position .= '<br />';
								$staff_position .= Text::sprintf('COM_JOOMLEAGUE_ROSTER_FUNCTION_IS',Text::_($row->position));
								break;
				}
				echo $staff_position;
				?>
				</td>
			</tr>
				<?php
				$k=1 - $k;
			}
			?>
	</table>
	<?php
}
?>
