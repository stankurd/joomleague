<?php use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class='joomleague'>
<?php if (!empty($this->project->id)): ?>
	<?php
	if ($this->config['show_sectionheader'] == 1)
	{
		echo $this->loadTemplate('sectionheader');
	}

	echo $this->loadTemplate('projectheading');

	if ($this->config['show_page_header_team_picture'] == 1)
	{
		echo $this->loadTemplate('picture');
	}

	if ($this->config['show_description'] == 1)
	{
		echo $this->loadTemplate('description');
	}

	if ($this->config['show_plan_layout'] == 'plan_default' ||
		$this->config['show_plan_layout'] == 'plan_sorted_by_matchnumber')
	{
		echo $this->loadTemplate('plan');
	} else if ($this->config['show_plan_layout'] == 'plan_sorted_by_date') {
		echo $this->loadTemplate('plan_sorted_by_date');
	}
	?>
	<div>
<?php else: ?>
	<div>
		<p><?php echo Text::_('At least you need to submit a project-id to get a teamplan of JoomLeague!'); ?></p>
<?php endif; ?>
<?php
	echo $this->loadTemplate('backbutton');
	echo $this->loadTemplate('footer');
?>
	</div>
</div>
