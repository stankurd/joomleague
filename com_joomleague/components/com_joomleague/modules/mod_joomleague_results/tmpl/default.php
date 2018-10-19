<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 * @subpackage	Module-Results
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>


<?php if ($params->get('show_custom_css', 0)):
$stylelink = "<style type=\"text/css\">\n";
$stylelink .= ".mod_jl_results".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results')." }\n";
$stylelink .= ".mod_jl_results".$params->get( 'moduleclass_sfx' )." a:link, a:visited, a:hover, a:active{ ".$params->get('style_mod_jl_results_link')." }\n";
$stylelink .= ".mod_jl_results_matches".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_matches')." }\n";
$stylelink .= ".mod_jl_results_project_name".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_project_name')." }\n";
$stylelink .= ".mod_jl_results_round_name".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_round_name')." }\n";
$stylelink .= ".mod_jl_results_date".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_date')." }\n";
$stylelink .= ".mod_jl_results_time".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_time')." }\n";
$stylelink .= ".mod_jl_results_score".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_score')." }\n";
$stylelink .= ".mod_jl_results_opponent_left".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_opponent_left')." }\n";
$stylelink .= ".mod_jl_results_opponent_right".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_opponent_right')." }\n";
$stylelink .= ".mod_jl_results_fulltablelink".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_fulltablelink')." }\n";
$stylelink .= ".mod_jl_results_matchwin".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_matchwin')." }\n";
$stylelink .= ".mod_jl_results_matchloss".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_matchloss')." }\n";
$stylelink .= ".mod_jl_results_matchdraw".$params->get( 'moduleclass_sfx' )." { ".$params->get('style_mod_jl_results_matchdraw')." }\n";
$stylelink .= "</style>\n";
$document->addCustomTag($stylelink);
 endif; ?>


<?php
// check if any results returned
$items = count($list['matches']);
if (!$items) {
?>

	<div class="mod_jl_results<?php echo $params->get( 'moduleclass_sfx' ) ?>">
		<?php echo Text::_($params->get('no_items_text')); ?>
	</div>
	
<?php
	return;
}

$nametype 	= $params->get('nametype', 'short_name');
$matches 	= modJLGResultsHelper::sortByDate($list['matches']);
$teams   	= $list['teams'];

$colspan = 0;
if (($params->get('show_time')=='1') && ($params->get('show_date')=='1')) {
	$colspan	=	'2';
}
elseif (($params->get('show_time')=='1') || ($params->get('show_date')=='1')) {
	$colspan	=	'1';
}
else {
	$colspan	=	'0';
}

?>


<div class="mod_jl_results_link<?php echo $params->get( 'moduleclass_sfx' ) ?>">


<div class="mod_jl_results<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<?php if ($params->get('show_project_name', 0)):?>
		<span class="mod_jl_results_project_name<?php echo $params->get( 'moduleclass_sfx' ) ?>"><?php echo $list['project']->name; ?></span>
	<?php endif; ?>
	<?php if ($params->get('show_round_name', 0)):?>
		<span class="mod_jl_results_round_name<?php echo $params->get( 'moduleclass_sfx' ) ?>"><?php echo $list['round']->name; ?></span>
	<?php endif; ?>


	<table class="mod_jl_results_matches<?php echo $params->get( 'moduleclass_sfx' ) ?>">
		<tbody>
		
		<?php foreach ($matches as $date => $items):  ?>
			<?php if ($params->get('show_date_heading', 1)): ?>
				<tr>
					<th colspan="5" class="mod_jl_results_date<?php echo $params->get( 'moduleclass_sfx' ) ?>">
						<?php echo JoomleagueHelper::getMatchDate($items[0], Text::_($params->get('date_format'))); ?>
					</th>
				</tr>
			<?php endif; ?>
			
				<?php $k = 0; ?>
				<?php foreach ($items as $match): ?>
					<tr class="d<?php echo $k; ?>">
			
						<?php if ($params->get('show_date', 1)): ?>
							<td class="mod_jl_results_date<?php echo $params->get( 'moduleclass_sfx' ) ?>">
								<?php echo JoomleagueHelper::getMatchDate($match, Text::_($params->get('date_format'))); ?>
							</td>
						<?php endif; ?>
				
						<?php if ($params->get('show_time', 1)): ?>
							<td class="mod_jl_results_time<?php echo $params->get( 'moduleclass_sfx' ) ?>">
								<?php echo JoomleagueHelper::getMatchTime($match, Text::_($params->get('time_format'))); ?>
							</td>
						<?php endif; ?>
				
						<td class="mod_jl_results_opponent_left<?php echo $params->get( 'moduleclass_sfx' ) ?>">
							<?php 
								if(isset($teams[$match->projectteam1_id])) {	
									if($params->get('teamlink') != '0')
									{
										echo HTMLHelper::link(modJLGResultsHelper::getTeamLink($teams[$match->projectteam1_id], $params, $list['project']), $teams[$match->projectteam1_id]->$nametype);
									}
									else
									{
										echo $teams[$match->projectteam1_id]->$nametype;
									}
									echo '&nbsp;';
									echo modJLGResultsHelper::getLogo($teams[$match->projectteam1_id], $params);
								}
							?>
						</td>

					<?php if ($params->get('show_score_design', 1)): ?>
						<?php if(($match->team1_result > $match->team2_result) || ($match->team1_result_decision > $match->team2_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchwin" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchloss" . $params->get( 'moduleclass_sfx' );
						}
						elseif(($match->team2_result > $match->team1_result) || ($match->team2_result_decision > $match->team1_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchloss" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchwin" . $params->get( 'moduleclass_sfx' );
						}
						elseif(($match->team1_result == $match->team2_result) || ($match->team1_result_decision == $match->team2_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchdraw" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchdraw" . $params->get( 'moduleclass_sfx' );
						}
						?>
						<td class="mod_jl_results_score<?php echo $params->get( 'moduleclass_sfx' ) ?>">
							<?php if ($params->get('scorelink', 1)):?>
								<a href="<?php echo modJLGResultsHelper::getScoreLink($match, $list['project']); ?>">
							<?php endif; ?>
							<?php if ($match->alt_decision): ?>
								<span class="<?php echo $matchresultclass1; ?>">
									<?php echo ((int) $match->team1_result_decision); ?>
								</span>
									&nbsp;-&nbsp;
								<span class="<?php echo $matchresultclass2; ?>">
									<?php echo ((int) $match->team2_result_decision); ?>
								</span>									
							<?php else:?>
								<span class="<?php echo $matchresultclass1; ?>">
									<?php echo $match->team1_result; ?>
								</span>
									&nbsp;-&nbsp;
								<span class="<?php echo $matchresultclass2; ?>">
									<?php echo $match->team2_result; ?>
								</span>							
							<?php endif;?>
							<?php if ($params->get('scorelink', 1)):?>
								</a>
							<?php endif?>
						</td>
					<?php endif?>
						
						<td class="mod_jl_results_opponent_right<?php echo $params->get( 'moduleclass_sfx' ) ?>">
							<?php
							if(isset($teams[$match->projectteam2_id])) {
								echo modJLGResultsHelper::getLogo($teams[$match->projectteam2_id], $params);
								echo '&nbsp;';
								if($params->get('teamlink') != '0')
								{
									echo HTMLHelper::link(modJLGResultsHelper::getTeamLink($teams[$match->projectteam2_id], $params, $list['project']), $teams[$match->projectteam2_id]->$nametype);
								}
								else
								{
									echo $teams[$match->projectteam2_id]->$nametype;
								}
							}
							?>
						</td>
				
					</tr>	
					
					
					<?php if (!$params->get('show_score_design', 1)): ?>
						<?php if(($match->team1_result > $match->team2_result) || ($match->team1_result_decision > $match->team2_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchwin" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchloss" . $params->get( 'moduleclass_sfx' );
						}
						elseif(($match->team2_result > $match->team1_result) || ($match->team2_result_decision > $match->team1_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchloss" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchwin" . $params->get( 'moduleclass_sfx' );
						}
						elseif(($match->team1_result == $match->team2_result) || ($match->team1_result_decision == $match->team2_result_decision)) {
							$matchresultclass1	=	"mod_jl_results_matchdraw" . $params->get( 'moduleclass_sfx' );
							$matchresultclass2	=	"mod_jl_results_matchdraw" . $params->get( 'moduleclass_sfx' );
						}
						?>
					<tr>
						<?php if($colspan !== '0'): ?>
							<td colspan="<?php echo $colspan; ?>"></td>
						<?php endif; ?>
						<td colspan="2" class="mod_jl_results_score<?php echo $params->get( 'moduleclass_sfx' ) ?>">
							<?php if ($params->get('scorelink', 1)):?>
								<a href="<?php echo modJLGResultsHelper::getScoreLink($match, $list['project']); ?>">
							<?php endif; ?>
							<?php if ($match->alt_decision): ?>
								<span class="<?php echo $matchresultclass1; ?>"><?php echo ((int) $match->team1_result_decision); ?></span>&nbsp;-&nbsp;<span class="<?php echo $matchresultclass2; ?>"><?php echo ((int) $match->team2_result_decision); ?>
								</span>									
							<?php else:?>
								<span class="<?php echo $matchresultclass1; ?>"><?php echo $match->team1_result; ?></span>&nbsp;-&nbsp;<span class="<?php echo $matchresultclass2; ?>"><?php echo $match->team2_result; ?>
								</span>							
							<?php endif;?>
							<?php if ($params->get('scorelink', 1)):?>
								</a>
							<?php endif?>
						</td>
					</tr>
					<?php endif?>
			
				<?php $k = 1 - $k; ?>
				<?php endforeach; ?>
		<?php endforeach; ?>
		
		</tbody>
	</table>


	<?php if ($params->get('show_full_link', 1)):?>
		<div class="mod_jl_results_fulltablelink<?php echo $params->get( 'moduleclass_sfx' ) ?>"><?php echo HTMLHelper::link(JoomleagueHelperRoute::getResultsRoute($list['project']->id, $list['round']->id, $list['divisionid']), Text::_('MOD_JOOMLEAGUE_RESULTS_VIEW_FULL')); ?></div>
	<?php endif; ?>
	
	
</div>


</div>