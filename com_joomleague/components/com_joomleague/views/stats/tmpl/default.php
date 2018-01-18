<?php use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die; 

if ( !isset( $this->project ) )
{
    $msg = Text::_( 'Error: ProjectID was not submitted in URL or project was not found in database!' );
    Factory::getApplication()->enqueueMessage($msg, 'warning');
}
else
{
    // Show Content of TeamStats Template

    // Set display style dependent on what stat parts should be shown
    $show_general = 'none';
    if ( $this->config['show_general_stats'] == "1" )
    {
        $show_general = 'inline';
    }
    $show_goals = 'none';
    if ( $this->config['show_goals_stats'] == "1" )
    {
        $show_goals = 'inline';
    }
    $show_attendance = 'none';
    if ( $this->config['show_attendance_stats'] == "1" )
    {
        $show_attendance = 'inline';
    }
    $show_flash = '';
    if ( $this->config['show_goals_stats_flash'] == "0" )
    {
        $show_flash = 'display:none;';
    }
    $show_att_ranking = '';
    if ( $this->config['show_attendance_ranking'] == "0" )
    {
		$show_att_ranking = 'display:none;';
    }

	// Make sure that in case extensions are written for mentioned (common) views,
	// that they are loaded i.s.o. of the template of this view
	$templatesToLoad = array('projectheading', 'backbutton', 'footer');
	JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
	?>
<div class="joomleague">
	<?php
	if ($this->config['show_sectionheader'])
	{
		echo $this->loadTemplate('sectionheader');
	}

	echo $this->loadTemplate('projectheading');

	if ($this->config['show_general_stats'])
	{
		echo $this->loadTemplate('stats'); 
	}

	if ($this->config['show_goals_stats'])
	{
		echo $this->loadTemplate('goals_stats'); 
	}

	if ($this->config['show_attendance_stats'] && $this->totals->sumspectators > 0 && $this->totals->attendedmatches > 0)
	{
		echo $this->loadTemplate('attendance_stats'); 
	}

	if ( $this->config['show_goals_stats_flash'] )
	{
		echo $this->loadTemplate('flashchart');
	}

	if ($this->config['show_attendance_ranking']) 
	{
		echo $this->loadTemplate('ranking');
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
<?php
}
?>
