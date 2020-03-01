<?php
use Joomla\CMS\Factory;

defined( '_JEXEC' ) or die( 'Restricted access' );

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);


//echo 'all time player<pre>'.print_r($this->rows,true).'</pre><br>';
//echo 'all time player config<pre>'.print_r($this->config,true).'</pre><br>';
//echo 'all time player playerposition<pre>'.print_r($this->playerposition,true).'</pre><br>';
//echo 'all time player positioneventtypes<pre>'.print_r($this->positioneventtypes,true).'</pre><br>';
if (($this->config['show_sectionheader'])==1)
{
    echo $this->loadTemplate('sectionheader');
}

if ($this->config['show_projectheader'] == 1)
{
    echo $this->loadTemplate('projectheading');
}

    if (($this->config['show_team_logo'])==1)
    {
        echo $this->loadTemplate('picture');
    }
    
    if (($this->config['show_description'])==1)
    {
        echo $this->loadTemplate('description');
    }
//echo $this->loadTemplate('players');

$option = Factory::getApplication()->input->get('option');
if (($this->config['show_players'])==1)
{
    if (($this->config['show_players_layout'])=='player_standard')
    {
        echo $this->loadTemplate('players');
    } else if (($this->config['show_players_layout'])=='player_card') {
        $document 	= Factory::getDocument();
        $version 	= urlencode(JoomleagueHelper::getVersion());
        $document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_card.css?v=' . $version );
        echo $this->loadTemplate('players_card');
    }
}

if (($this->config['show_staff'])==1)
{
    if (($this->config['show_staff_layout'])=='staff_standard')
    {
        echo $this->loadTemplate('staff');
    } else if (($this->config['show_staff_layout'])=='staff_card') {
        $document 	= Factory::getDocument();
        $version 	= urlencode(JoomleagueHelper::getVersion());
        $document->addStyleSheet(  $this->baseurl . '/components/'.$option.'/assets/css/'.$this->getName().'_card.css?v=' . $version );
        echo $this->loadTemplate('staff_card');
    }
}

else
{
    echo "<p>Project team could not be determined</p>";
}

echo "<div>";
echo $this->loadTemplate('backbutton');
echo $this->loadTemplate('footer');
echo "</div>";
	?>
