<?php defined('_JEXEC') or die;


jimport('joomla.html.pane');
?>

<!-- START: game stats -->
<?php
if (!empty($this->matchplayerpositions ))
{
	$hasMatchPlayerStats = false;
	$hasMatchStaffStats = false;
	foreach ( $this->matchplayerpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchPlayerStats = true;
					break;
				}
			}
		}
	}
	foreach ( $this->matchstaffpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchStaffStats = true;
				}
			}
		}
	}
	if($hasMatchPlayerStats || $hasMatchStaffStats) :
	?>

	<h2><?php echo JText::_('COM_JOOMLEAGUE_MATCHREPORT_STATISTICS'); ?></h2>

		<?php
		$iPanel = 1;
		$selector = 'defaultstats';
		echo JHtml::_('bootstrap.startTabSet', $selector, array('active'=>'details')); 
			
		echo JHtml::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $this->team1->name);
		echo $this->loadTemplate('stats_home');
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.addTab', $selector, 'panel'.$iPanel++, $this->team2->name);
		echo $this->loadTemplate('stats_away');
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.endTabSet');
		
	endif;
}
?>
<!-- END of game stats -->
