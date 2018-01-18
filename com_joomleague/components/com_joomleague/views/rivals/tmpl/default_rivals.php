<?php use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die; 

//add js file
//JHtml::_('behavior.framework');
?>

<table class="fixtures" width="96%" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr class="sectiontableheader">
		<th class="name_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_RIVAL'); ?></th>
		<th class="match_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_MATCHES'); ?></th>
		<th class="win_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_WIN'); ?></th>
		<th class="tie_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_DRAW'); ?></th>
		<th class="los_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_LOS'); ?></th>
		<th class="goals_row"><?php echo Text::_('COM_JOOMLEAGUE_RIVALS_TOTAL_GOALS'); ?></th>
	</tr>
	<?php
	$k=0;
	foreach ($this->opos as $opos => $v)
	{
		if($v['name'] == '') continue;
		$team = ArrayHelper::toObject($v);
		if(empty($team->id)) continue;
		?>
	<tr class="<?php echo ($k==0)? $this->config['style_class1'] : $this->config['style_class2']; ?>">
		<td class="name_row"><?php 
		$isFavTeam = in_array( $team->id, explode(",",$this->project->fav_team) );
		// TODO: ranking deviates from the other views, regarding highlighting of the favorite team(s). Align this...
		$config['highlight_fav'] = $isFavTeam;
		echo JoomleagueHelper::formatTeamName( $team, 'tr' . $k, $this->config, $isFavTeam );
		//echo $v['name'];
		?></td>
		<td class="match_row"><?php echo $v['match']; ?></td>
		<td class="win_row"><?php echo $v['win']!=0 ? $v['win'] : 0; ?></td>
		<td class="tie_row"><?php echo $v['tie']!=0 ? $v['tie']: 0 ; ?></td>
		<td class="los_row"><?php echo $v['los']!=0 ? $v['los']: 0 ; ?></td>
		<td class="goals_row"><?php echo $v['g_for'].' '. $this->overallconfig['seperator'] .' '.$v['g_aga']; ?></td>
	</tr>
	<?php
	$k=1-$k;
	}
	?>
</table>
