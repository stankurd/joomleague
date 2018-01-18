<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; ?>
<!-- START of match events -->

<h2><?php echo Text::_('COM_JOOMLEAGUE_MATCHREPORT_EVENTS'); ?></h2>

            <?php
		    $txt_tab='';
			$iPanel = 1;
			$selector = 'defaulteventstab';
			echo HTMLHelper::_('bootstrap.startTabSet', $selector, array('active'=>'panel'.$iPanel)); 
		    foreach ($this->eventtypes AS $event)
			{
				$pic_tab=$event->icon;
				if ($pic_tab == '/events/event.gif')
				{
					$txt_tab = Text::_($event->name);
				}
				else
				{
					$imgTitle=Text::_($event->name);
					$imgTitle2=array(' title' => $imgTitle, ' alt' => $imgTitle, ' style' => 'max-height:40px;');
					$txt_tab=HTMLHelper::image($pic_tab,$imgTitle,$imgTitle2);
				}

				echo HTMLHelper::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $txt_tab);
				echo '<table class="matchreport">';
				echo '<tr>';
				echo '<td class="list">';
				echo '<ul>';
				foreach ($this->matchevents AS $me)
				{
					if ($me->event_type_id==$event->id && $me->ptid==$this->match->projectteam1_id)
					{
						echo '<li class="list">';

						if ($this->config['show_event_minute'] == 1 && $me->event_time > 0)
						{
						    $prefix = str_pad($me->event_time, 2 ,'0', STR_PAD_LEFT)."' ";
						} else {
						    $prefix = null;
						}

						$match_player = JoomleagueHelper::formatName($prefix, $me->firstname1, $me->nickname1, $me->lastname1, $this->config["name_format"]);
                        if ($this->config['event_link_player'] == 1 && $me->playerid != 0)
                        {
                            $player_link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$me->team_id,$me->playerid);
                            $match_player = HTMLHelper::link($player_link,$match_player);
                        }
                        echo $match_player;

						// only show event sum and match notice when set to on in template cofig
						$sum_notice = "";
						if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
						{
						    if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) || ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
							{
								$sum_notice .= ' (';
									if ($this->config['show_event_sum'] == 1 && $me->event_sum > 0)
									{
										$sum_notice .= $me->event_sum;
									}
									if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) && ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
									{
										$sum_notice .= ' | ';
									}
									if ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0)
									{
										$sum_notice .= $me->notice;
									}
								$sum_notice .= ')';
							}
						}
						echo $sum_notice;

						echo '</li>';
					}
				}
				echo '</ul>';
				echo '</td>';
				echo '<td class="list">';
				echo '<ul>';
				foreach ($this->matchevents AS $me)
				{
					if ($me->event_type_id==$event->id && $me->ptid==$this->match->projectteam2_id)
					{
						echo '<li class="list">';

						if ($this->config['show_event_minute'] == 1 && $me->event_time > 0)
						{
						    $prefix = str_pad($me->event_time, 2 ,'0', STR_PAD_LEFT)."' ";
						} else {
						    $prefix = null;
						}

						$match_player = JoomleagueHelper::formatName($prefix, $me->firstname1, $me->nickname1, $me->lastname1, $this->config["name_format"]);
						if ($this->config['event_link_player'] == 1 && $me->playerid != 0)
						{
							$player_link=JoomleagueHelperRoute::getPlayerRoute($this->project->slug,$me->team_id,$me->playerid);
							$match_player = HTMLHelper::link($player_link,$match_player);
						}
						echo $match_player;

						// only show event sum and match notice when set to on in template cofig
						$sum_notice = "";
						if($this->config['show_event_sum'] == 1 || $this->config['show_event_notice'] == 1)
						{
						    if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) || ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
							{
								$sum_notice .= ' (';
									if ($this->config['show_event_sum'] == 1 && $me->event_sum > 0)
									{
										$sum_notice .= $me->event_sum;
									}
									if (($this->config['show_event_sum'] == 1 && $me->event_sum > 0) && ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0))
									{
										$sum_notice .= ' | ';
									}
									if ($this->config['show_event_notice'] == 1 && strlen($me->notice) > 0)
									{
										$sum_notice .= $me->notice;
									}
								$sum_notice .= ')';
							}
						}
						echo $sum_notice;

						echo '</li>';
					}
				}
				echo '</ul>';
				echo '</td>';
				echo '</tr>';
				echo '</table>';
				echo HTMLHelper::_('bootstrap.endTab');
			}
			echo HTMLHelper::_('bootstrap.endTabSet');

            ?>
<!-- END of match events -->
<br />