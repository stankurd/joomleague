<?php
/**
 * Joomleague
 * @subpackage	Module-SportstypeStatistics
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if there is a project
if ($data['projectscount'] == 0) {
	echo '<p class="modjlgsports">' . JText::_('MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_NO_PROJECTS') . '</p>';
	return;
} else {
	?>
<div class="modjlgsports <?php echo $params->get( 'moduleclass_sfx' ); ?>">
	
<div class="Table">
    <div class="Title">
        <h4>
		<?php 
		if($data['sportstype']->icon) { ?><img src="<?php echo $data['sportstype']->icon; ?>"><?php } ?> <?php echo JText::_($data['sportstype']->name); 
		?>
		</h4>
    </div>
    
    <!-- Project -->
    <?php if($params->get('show_project',1) == 1) { ?>
    <div class="Row">
        <div class="Cell">
            <span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
		if($params->get('show_icon',1)==1) {
			echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS").'" src="administrator/components/com_joomleague/assets/images/projects.png">';
			echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS"); 
		} else {
			echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PROJECTS"); 
		}
		?></span>
        </div>
        <div class="Cell">
            <span class="text"><?php echo $data['projectscount']?></span>
        </div>
    </div>
    <?php } ?>
    
    <!-- Leagues -->
	<?php if($params->get('show_leagues',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
			if($params->get('show_icon',1)==1) {
				echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES").'" src="administrator/components/com_joomleague/assets/images/leagues.png">';
				echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES");
			} else {
				echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_LEAGUES"); 
			}
			?>
			</span>
		</div>
		<div class="Cell">
            <span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['leaguescount']?></span>
        </div>
	</div>	
	<?php } ?>
	
	<!-- Seasons -->
	<?php if($params->get('show_seasons',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS").'" src="administrator/components/com_joomleague/assets/images/seasons.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS");
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_SEASONS");
				}
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['seasonscount']?></span>
		</div>
	</div>
	<?php } ?>
	
	<!-- Teams -->
	<?php if($params->get('show_teams',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_TEAMS").'" src="administrator/components/com_joomleague/assets/images/teams.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_TEAMS"); 
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_TEAMS");
				}
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectteamscount']?></span>
		</div>
	</div>
	<?php } ?>
	
	<!-- Players -->
	<?php if($params->get('show_players',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYERS").'" src="administrator/components/com_joomleague/assets/images/players.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYERS"); 
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYERS");
				}
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectteamsplayerscount']?></span>
		</div>
	</div>
	<?php } ?>
	
	<!-- Divisions -->
	<?php if($params->get('show_divisions',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_DIVISIONS").'" src="administrator/components/com_joomleague/assets/images/division.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_DIVISIONS");
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_DIVISIONS");
				} 
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectdivisionscount']?></span>
		</div>	
	</div>
	<?php } ?>
	
	<!-- Rounds -->
	<?php if($params->get('show_rounds',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_ROUNDS").'" src="administrator/components/com_joomleague/assets/images/icon-16-Matchdays.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_ROUNDS"); 
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_ROUNDS");
				}
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectroundscount']?></span>
		</div>
	</div>
	<?php } ?>
	
		<!-- Matches -->
	<?php if($params->get('show_matches',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_MATCHES").'" src="administrator/components/com_joomleague/assets/images/matches.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_MATCHES");
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_MATCHES");
				} 
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchescount']?></span>
		</div>
	</div>
	<?php } ?>
	
	<!-- Player events -->
	<?php if($params->get('show_player_events',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_EVENTS").'" src="administrator/components/com_joomleague/assets/images/events.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_EVENTS"); 
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_EVENTS");
				}	
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatcheseventscount']?></span>
		</div>
	</div>
	<?php } ?>
	
	<!-- ProjectMatchesEventnames -->
	<?php 
	foreach($data['projectmatcheseventnames'] as $event) {
    ?>
    <div class="Row">
		<div class="Cell">
    		<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>">
    			<?php
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_($event->name).'" src="'.$event->icon.'">';
					echo ' '.JText::_($event->name); 
				} else {
					echo JText::_($event->name);
				}	
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>">&nbsp;<?php echo $event->count ?></span>
		</div>
    </div>
    <?php 
    } 
    ?>
    
    <!-- Player Stats -->
	<?php if($params->get('show_player_stats',1) == 1) { ?>
	<div class="Row">
		<div class="Cell">
			<span class="label <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php 
				if($params->get('show_icon',1)==1) {
					echo '<img alt="'.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_STATS").'" src="administrator/components/com_joomleague/assets/images/icon-48-statistics.png">';
					echo ' '.JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_STATS"); 
				} else {
					echo JText::_("MOD_JOOMLEAGUE_SPORTS_TYPE_STATISTICS_PLAYER_STATS");
				} 
				?>
			</span>
		</div>
		<div class="Cell">
			<span class="text <?php echo $params->get( 'moduleclass_sfx' ); ?>"><?php echo $data['projectmatchesstatscount']?></span>
		</div>	
	</div>
	<?php } ?>
	
</div> <!-- end div table -->
</div> <!-- end of container -->
<?php 
}
?>
