<?php defined('_JEXEC') or die;

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

	if ($this->config['show_matchday_pagenav']==2 || $this->config['show_matchday_pagenav']==3)
	{
		echo $this->loadTemplate('pagnav');
	}

	echo $this->loadTemplate('results');

	if ($this->config['show_matchday_pagenav']==1 || $this->config['show_matchday_pagenav']==3)
	{
		echo $this->loadTemplate('pagnav');
	}

	echo "<div>";
	echo $this->loadTemplate('backbutton');
	echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
