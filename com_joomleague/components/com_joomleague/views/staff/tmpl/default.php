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

	if ($this->config['show_projectheader'] == 1)
	{	
		echo $this->loadTemplate('projectheading');
	}

	// General part of person view START
	if ($this->config['show_info'] == 1)
	{
		echo $this->loadTemplate('info');
	}

	if (($this->config['show_extended']) == 1)
	{
		echo $this->loadTemplate('extended');
	}

	if ($this->config['show_status'] == 1)
	{
		//FIXME 
		/*
		( ! ) Notice: Undefined property: stdClass::$status in components\com_joomleague\views\person\tmpl\default_status.php on line 3
		*/
		//echo $this->loadTemplate('status');
	}

	if ($this->config['show_description'] == 1)
	{
		echo $this->loadTemplate('description');
	}
	// General part of person view END

	if ($this->config['show_careerstats'] == 1)
	{
		echo $this->loadTemplate('stats');
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
