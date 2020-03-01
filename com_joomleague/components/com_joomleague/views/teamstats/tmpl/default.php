<?php use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die; 

// Set display style dependent on what stat parts should be shown
$show_general = $this->config['show_general_stats'] == 1 ? 'inline' : 'none';
$show_goals = $this->config['show_goals_stats'] == 1 ? 'inline' : 'none';
$show_attendance = $this->config['show_attendance_stats'] == 1 ? 'inline' : 'none';
$show_flash = $this->config['show_goals_stats_flash'] == 0 ? 'display:none;' : '';
//$show_att_ranking = $this->config['show_attendance_ranking'] == 0 ? 'display:none;' : '';
//$show_events = $this->config['show_events_stats'] == 0 ? 'display:none;' : '';

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class='joomleague'>
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

	if ($this->config['show_attendance_stats'])
	{
		echo $this->loadTemplate('attendance_stats'); 
	}	
	?>
	<div style='width:100%; float:left'>
	<?php
	if ( $this->config['show_goals_stats_flash'] )
	{
	    $document= Factory::getDocument();
	    //$version = urlencode(JoomleagueHelper::getVersion());
	    //$document->addScript( Uri::base(true).'/components/com_joomleague/assets/js/json2.js?v='.$version);
	    //$document->addScript( Uri::base(true).'/components/com_joomleague/assets/js/swfobject.js?v='.$version);
	    echo $this->loadTemplate('flashchart'); 
	}
	?>
	</div>

	<div>
		<?php
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
		?>
	</div>
</div>
