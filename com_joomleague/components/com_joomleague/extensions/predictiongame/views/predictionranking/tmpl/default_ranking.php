<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die(Text::_('Restricted access'));
HTMLHelper::_('behavior.tooltip');
//echo '<br /><pre>~' . print_r($this->config,true) . '~</pre><br />';
//echo '<br /><pre>~' . print_r($this->config['limit'],true) . '~</pre><br />';
?>
<a name='jl_top' id='jl_top'></a>
<?php
foreach ($this->model->_predictionProjectS AS $predictionProject)
{
	$gotSettings = $predictionProjectSettings = $this->model->getPredictionProject($predictionProject->project_id);
	if ((($this->model->pjID==$predictionProject->project_id) && ($gotSettings)) || ($this->model->pjID==0))
	{
		$showProjectID = (count($this->model->_predictionProjectS) > 1) ? $this->model->pjID : $predictionProject->project_id;
		$this->model->pjID = $predictionProject->project_id;
		$this->model->predictionProject = $predictionProject;
		$actualProjectCurrentRound = $this->model->getProjectSettings($predictionProject->project_id);
		
		if (!isset($this->roundID) || ($this->roundID < 1)){
			$this->roundID=$actualProjectCurrentRound;
		}
		if ($this->roundID < 1){$this->roundID=1;}
		if ($this->model->from < 1){
			$this->model->from=1;
		}
		if ($this->roundID > $this->model->getProjectRounds($predictionProject->project_id)){
			$this->roundID=$this->model->_projectRoundsCount;
		}
		if ($this->model->to > $this->model->_projectRoundsCount){
			$this->model->to=$this->model->_projectRoundsCount;
		}
		if ($this->model->to == 0){
			$this->model->to=$actualProjectCurrentRound;
		}
		
		?>
		<form name='resultsRoundSelector' method='post' >
			<input type='hidden' name='prediction_id' value='<?php echo (int)$this->predictionGame->id; ?>' />
			<input type='hidden' name='p' value='<?php echo (int)$predictionProject->project_id; ?>' />
			<input type='hidden' name='r' value='<?php echo (int)$this->roundID; ?>' />
			<input type='hidden' name='pjID' value='<?php echo (int)$showProjectID; ?>' />
			<input type='hidden' name='task' value='selectProjectRound' />
			<input type='hidden' name='option' value='com_joomleague' />
			<input type='hidden' name='controller' value='predictionranking' />

			<table class='blog' cellpadding='0' cellspacing='0' >
				<tr>
					<td class='sectiontableheader'>
						<?php
						echo '<b>'.Text::sprintf('COM_JOOMLEAGUE_PRED_RANK_SUBTITLE_01').'</b>';
						?>
					</td>
					<td class='sectiontableheader' style='text-align:right; ' width='20%' nowrap='nowrap' ><?php
						echo $this->model->createProjectSelector(	$this->model->_predictionProjectS,
																	$predictionProject->project_id,
																	$showProjectID);
						if ($showProjectID > 0)
						{

							echo '&nbsp;&nbsp;';
							$link = JoomleagueHelperRoute::getResultsRoute($predictionProject->project_id,$this->roundID);
							$imgTitle=Text::_('COM_JOOMLEAGUE_PRED_ROUND_RESULTS_TITLE');
							$desc = HTMLHelper::image('media/com_joomleague/jl_images/icon-16-Matchdays.png',$imgTitle,array('border' => 0,'title' => $imgTitle));
							echo HTMLHelper::link($link,$desc,array('target' => '_blank'));
						}
						?></td>
				</tr>
			</table><br />
			<?php echo HTMLHelper::_( 'form.token' ); ?>
		</form>
		<?php
		if (($showProjectID > 0) && ($this->config['show_rankingnav']))
		{
			$from_matchday=$this->model->createFromMatchdayList($predictionProject->project_id);
			$to_matchday=$this->model->createToMatchdayList($predictionProject->project_id);
			?>
			<form name='adminForm' id='adminForm' method='post'>
				<table>
					<tr>
						<td><?php echo HTMLHelper::_('select.genericlist',$this->lists['type'],'type','class="inputbox" size="1"','value','text',$this->model->type); ?></td>
						<td><?php echo HTMLHelper::_('select.genericlist',$from_matchday,'from','class="inputbox" size="1"','value','text',$this->model->from); ?></td>
						<td><?php echo HTMLHelper::_('select.genericlist',$to_matchday,'to','class="inputbox" size="1"','value','text',$this->model->to); ?></td>
						<td><input type='submit' class='button' name='reload View' value='<?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_FILTER'); ?>' /></td>
					</tr>
				</table>
				<?php echo HTMLHelper::_( 'form.token' ); ?>
			</form><br />
			<?php
		}
		?>
		<table width='100%' cellpadding='0' cellspacing='0'>
			<tr>
				<td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK'); ?></td>
				<?php
				if ($this->config['show_user_icon'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_AVATAR'); ?></td><?php
				}
				?>
				<td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_MEMBER'); ?></td>
				<?php
				if ($this->config['show_tip_details'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_DETAILS'); ?></td><?php
				}
				?>
				<td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_POINTS'); ?></td>
				<?php
				if ($this->config['show_average_points'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_AVERAGE'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_count_tips'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_PREDICTIONS'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_count_joker'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_JOKERS'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_count_topptips'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_TOPS'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_count_difftips'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_MARGINS'); ?></td><?php
				}
				?>
				<?php
				if ($this->config['show_count_tendtipps'])
				{
					?><td class='sectiontableheader' style='text-align:center; vertical-align:top; '><?php echo Text::_('COM_JOOMLEAGUE_PRED_RANK_TENDENCIES'); ?></td><?php
				}
				?>
			</tr>
			<?php
				//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';
				$k = 0;
				$memberList = $this->model->getPredictionMembersList($this->config);
				//echo '<br /><pre>~' . print_r($memberList,true) . '~</pre><br />';
				
				$membersResultsArray = array();
				$membersDataArray = array();

				foreach ($memberList AS $member)
				{

					//echo '<br /><pre>~' . print_r($this->model->page,true) . '~</pre><br />';
				    $memberPredictionPoints = $this->model->getPredictionMembersResultsList($showProjectID,
				    $this->model->from,
				    $this->model->to,
				    $member->user_id,
				    $this->model->type);
					//echo '<br /><pre>~' . print_r($memberPredictionPoints,true) . '~</pre><br />';
					$predictionsCount=0;
					$totalPoints=0;
					$totalTop=0;
					$totalDiff=0;
					$totalTend=0;
					$totalJoker=0;
					if (!empty($memberPredictionPoints))
					{
						foreach ($memberPredictionPoints AS $memberPredictionPoint)
						{
							if ((!is_null($memberPredictionPoint->homeResult)) ||
								(!is_null($memberPredictionPoint->awayResult)) ||
								(!is_null($memberPredictionPoint->homeDecision)) ||
								(!is_null($memberPredictionPoint->awayDecision)))
							{
								$predictionsCount++;
								$result = $this->model->createResultsObject(	$memberPredictionPoint->homeResult,
														$memberPredictionPoint->awayResult,
														$memberPredictionPoint->prTipp,
														$memberPredictionPoint->prHomeTipp,
														$memberPredictionPoint->prAwayTipp,
														$memberPredictionPoint->prJoker,
														$memberPredictionPoint->homeDecision,
														$memberPredictionPoint->awayDecision);
								$newPoints = $this->model->getMemberPredictionPointsForSelectedMatch($predictionProject,$result);
								//if (!is_null($memberPredictionPoint->prPoints))
								{
									$points=$memberPredictionPoint->prPoints;
									if ($newPoints!=$points)
									{
										// this check also should be done if the result is not displayed
										$memberPredictionPoint=$this->model->savePredictionPoints(	$memberPredictionPoint,
																									$predictionProject,
																									true);
										$points=$newPoints;
									}
									$totalPoints=$totalPoints+$points;
								}
								if (!is_null($memberPredictionPoint->prJoker)){$totalJoker=$totalJoker+$memberPredictionPoint->prJoker;}
								if (!is_null($memberPredictionPoint->prTop)){$totalTop=$totalTop+$memberPredictionPoint->prTop;}
								if (!is_null($memberPredictionPoint->prDiff)){$totalDiff=$totalDiff+$memberPredictionPoint->prDiff;}
								if (!is_null($memberPredictionPoint->prTend)){$totalTend=$totalTend+$memberPredictionPoint->prTend;}
							}
						}
					}

					$membersResultsArray[$member->pmID]['rank']		= 0;
					$membersResultsArray[$member->pmID]['predictionsCount']	= $predictionsCount;
					$membersResultsArray[$member->pmID]['totalPoints']	= $totalPoints;
					$membersResultsArray[$member->pmID]['totalTop']		= $totalTop;
					$membersResultsArray[$member->pmID]['totalDiff']	= $totalDiff;
					$membersResultsArray[$member->pmID]['totalTend']	= $totalTend;
					$membersResultsArray[$member->pmID]['totalJoker']	= $totalJoker;

					// check all needed output for later
					$picture = $member->avatar;
					$playerName = $member->name;					
					
					if (((!isset($member->avatar)) ||
						($member->avatar=='') ||
						(!file_exists($member->avatar)) ||
						((!$member->show_profile) && ($this->predictionMember->pmID!=$member->pmID))))
					{
						$picture = JoomleagueHelper::getDefaultPlaceholder("player");
					}
					//tobe removed
					//$imgTitle = Text::sprintf('COM_JOOMLEAGUE_PRED_AVATAR_OF',$member->name);
					//$output = HTMLHelper::image($member->avatar,$imgTitle,array(' width' => 20, ' title' => $imgTitle));
					
					$output = JoomleagueHelper::getPictureThumb($picture, $playerName,0,25);
					$membersDataArray[$member->pmID]['show_user_icon'] = $output;

					if (($this->config['link_name_to'])&&(($member->show_profile)||($this->predictionMember->pmID==$member->pmID)))
					{
						$link = PredictionHelperRoute::getPredictionMemberRoute($this->predictionGame->id,$member->pmID);
						$output = HTMLHelper::link($link,$member->name);
					}
					else
					{
						$output = $member->name;
					}
					$membersDataArray[$member->pmID]['name'] = $output;
					
					$imgTitle = Text::sprintf('COM_JOOMLEAGUE_PRED_RANK_SHOW_DETAILS_OF',$member->name);
					$imgFile=HTMLHelper::image( "media/com_joomleague/jl_images/zoom.png", $imgTitle , array(' title' => $imgTitle));
					$link=PredictionHelperRoute::getPredictionResultsRoute($this->predictionGame->id ,$actualProjectCurrentRound ,$this->model->pjID);
					if (($member->show_profile)||($this->predictionMember->pmID==$member->pmID))
					{
						$output = HTMLHelper::link( $link, $imgFile);
					}
					else
					{
						$output = '&nbsp;';
					}

					$membersDataArray[$member->pmID]['show_tip_details']	= $output;
				}

				//echo '<br /><pre>~' . print_r($membersResultsArray,true) . '~</pre><br />';
				//echo '<br /><pre>~' . print_r($membersDataArray,true) . '~</pre><br />';
				//$membersResultsArray2=$membersResultsArray;
				//$membersResultsArray3=$membersResultsArray;
				//$membersResultsArray4=$membersResultsArray;
				//$membersResultsArray5=$membersResultsArray;
				//$membersResultsArray6=$membersResultsArray;

				/*
				$membersResultsArray = array_merge(	$membersResultsArray,
													$membersResultsArray2,
													$membersResultsArray3,
													$membersResultsArray4,
													$membersResultsArray5,
													$membersResultsArray6
													);
				*/
				$computedMembersRanking=$this->model->computeMembersRanking($membersResultsArray,$this->config);
				$recordCount=count($computedMembersRanking);
				//echo '<br /><pre>~' . print_r($computedMembersRanking,true) . '~</pre><br />';

				$i=1;
				if ((int)$this->config['limit'] < 1){$this->config['limit']=10;}
				$rlimit=ceil($recordCount / $this->config['limit']);
				$this->model->page=($this->model->page > $rlimit) ? $rlimit : $this->model->page;
				$skipMemberCount=($this->model->page > 0) ? (($this->model->page-1)*$this->config['limit']) : 0;

				foreach ($computedMembersRanking AS $key => $value)
				{
					//echo '<br /><pre>~' . print_r($value,true) . '~</pre><br />';
					if ($i <= $skipMemberCount) { $i++; continue; }

					$class = ($k==0) ? 'sectiontableentry1' : 'sectiontableentry2';
					$styleStr = ($this->predictionMember->pmID==$key) ? ' style="background-color:yellow; color:black; " ' : '';
					$class = ($this->predictionMember->pmID==$key) ? 'sectiontableentry1' : $class;
					$tdStyleStr = " style='text-align:center; vertical-align:middle; ' ";

					//$this->config['show_all_user']=1;
					if (((!$this->config['show_all_user']) && ($value['predictionsCount'] > 0)) ||
						($this->config['show_all_user']) ||
						($this->predictionMember->pmID==$key))
					{
						?>
						<tr class='<?php echo $class; ?>' <?php echo $styleStr; ?> >
							<td<?php echo $tdStyleStr; ?>><?php echo $value['rank']; ?></td>
							<?php
							if ($this->config['show_user_icon'])
							{
								?>
								<td<?php echo $tdStyleStr; ?>><?php echo $membersDataArray[$key]['show_user_icon']; ?></td>
								<?php
							}
							?>
							<td<?php echo $tdStyleStr; ?>><?php echo $membersDataArray[$key]['name']; ?></td>
							<?php
							if ($this->config['show_tip_details'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php echo $membersDataArray[$key]['show_tip_details']; ?></td><?php
							}
							?>
							<td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['totalPoints']; ?></td>
							<?php
							if ($this->config['show_average_points'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php
								if ($membersResultsArray[$key]['predictionsCount'] > 0)
								{
									echo number_format(round($membersResultsArray[$key]['totalPoints']/$membersResultsArray[$key]['predictionsCount'],2),2);
								}
								else
								{
									echo number_format(0,2);
								}
								?></td><?php
							}
							?>
							<?php
							if ($this->config['show_count_tips'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['predictionsCount']; ?></td><?php
							}
							?>
							<?php
							if ($this->config['show_count_joker'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['totalJoker']; ?></td><?php
							}
							?>
							<?php
							if ($this->config['show_count_topptips'])
							{
								?><td style='text-align:center; vertical-align:middle; '><?php echo $membersResultsArray[$key]['totalTop']; ?></td><?php
							}
							?>
							<?php
							if ($this->config['show_count_difftips'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['totalDiff']; ?></td><?php
							}
							?>
							<?php
							if ($this->config['show_count_tendtipps'])
							{
								?><td<?php echo $tdStyleStr; ?>><?php echo $membersResultsArray[$key]['totalTend']; ?></td><?php
							}
							?>
						</tr>
						<?php
						$k = (1-$k);
						$i++;
						if ($i > $skipMemberCount+$this->config['limit']){break;}
					}
				}
			?> 
		</table>
		<?php
	}
}
?>