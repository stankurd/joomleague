<?php use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (isset($this->project))
{
	?>
	<div id='jlg_ranking_table' align='center'>
		<br />
		<?php
		if (count($this->matches) > 0)
		{
			switch ($this->config['result_style'])
			{
				case 3:
					echo $this->loadTemplate('results_style3');
					break;

				default:
					echo $this->loadTemplate('results_style0');
					break;
			}
		}
		?>
	</div>
	<!-- Main END -->
	<?php
	if ($this->config['show_dnp_teams'])
	{
		echo $this->loadTemplate('freeteams');
	}
}
else
{
	$msg = Text::_('Error: ProjectID was not submitted in URL or selected project was not found in database!');
	Factory::getApplication()->enqueueMessage($msg, 'warning');
}
?>