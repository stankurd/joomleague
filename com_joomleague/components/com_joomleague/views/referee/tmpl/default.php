<?php defined('_JEXEC') or die;

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class='joomleague'>
	<?php
	if ($this->config['show_sectionheader'] == 1)
	{
		echo $this->loadTemplate('sectionheader');
	}

	echo $this->loadTemplate('projectheading');

	if ($this->config['show_info'] == 1)
	{
		echo $this->loadTemplate('info');
	}

	if ($this->config['show_extended'] == 1)
	{
		echo $this->loadTemplate('extended');
	}

	if ($this->config['show_description'] == 1)
	{
		echo $this->loadTemplate('description');
	}

	if ($this->config['show_gameshistory'] == 1)
	{
		echo $this->loadTemplate('gameshistory');
	}

	if ($this->config['show_career'] == 1)
	{
		echo $this->loadTemplate('career');
	}
	?>
	<div>
		<?php
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
		?>
	</div>
</div>
