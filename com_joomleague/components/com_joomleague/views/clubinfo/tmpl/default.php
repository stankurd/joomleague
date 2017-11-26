<?php use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die; 

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php 
	if (($this->config['show_sectionheader']) == 1)
	{ 
		echo $this->loadTemplate('sectionheader');
	}
	echo $this->loadTemplate('projectheading');
	echo $this->loadTemplate('clubinfo');
	?>
	<div class='jl_defaultview_spacing'>&nbsp;</div>
	<?php
	if ($this->config['show_description'] == 1)
	{
		echo $this->loadTemplate('description');
	}
	?>
	<div class='jl_defaultview_spacing'>&nbsp;</div>
	<?php
	//fix me
	if (($this->config['show_extended']) == 1)
	{
		echo $this->loadTemplate('extended');
		?>
		<div class='jl_defaultview_spacing'>&nbsp;</div>
		<?php
	}

	if (($this->config['show_maps']) == 1 &&
		(PluginHelper::isEnabled('system', 'plugin_googlemap2') ||
		 PluginHelper::isEnabled('system', 'plugin_googlemap3')))
	{ 
		echo $this->loadTemplate('maps');
		?>
		<div class='jl_defaultview_spacing'>&nbsp;</div>
		<?php
	}

		
	if ($this->config['show_teams_of_club'] == 1)
	{ 
		echo $this->loadTemplate('teams');
		?>
		<div class='jl_defaultview_spacing'>&nbsp;</div>
		<?php
	}
	?>

	<div>
		<?php
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
		?>
	</div>
</div>
