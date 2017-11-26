<?php defined('_JEXEC') or die;

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php
	if ($this->config['show_sectionheader'] == 1 && $this->club)
	{
		echo $this->loadTemplate('sectionheader');
	}
	echo $this->loadTemplate('projectheading');
	echo $this->loadTemplate('datenav');

	switch ($this->config['type_matches'])
	{
	case 0 : // All matches
		$this->formatMatches($this->allmatches, 'matches', 'COM_JOOMLEAGUE_CLUBPLAN_NO_MATCHES');
		break;
	case 1 : // Home matches
		$this->formatMatches($this->homematches, 'matches', 'COM_JOOMLEAGUE_CLUBPLAN_NO_HOME_MATCHES');
		break;
	case 2 : // Away matches
		$this->formatMatches($this->awaymatches, 'matches', 'COM_JOOMLEAGUE_CLUBPLAN_NO_AWAY_MATCHES');
		break;
	case 4 : // matches sorted by date
		$this->formatMatches($this->allmatches, 'matches_sorted_by_date', 'COM_JOOMLEAGUE_CLUBPLAN_NO_MATCHES');
		break;
	default : // Home+Away matches
		$this->formatMatches($this->homematches, 'matches', 'COM_JOOMLEAGUE_CLUBPLAN_NO_HOME_MATCHES');
		$this->formatMatches($this->awaymatches, 'matches', 'COM_JOOMLEAGUE_CLUBPLAN_NO_AWAY_MATCHES');
		break;
	}
	?>
	<div>
		<?php
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
		?>
	</div>
</div>
