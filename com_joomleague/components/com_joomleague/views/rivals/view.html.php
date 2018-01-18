<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Rivals
 */
class JoomleagueViewRivals extends JLGView
{
	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document	= Factory::getDocument();

		$model	= $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		
		$this->project=$model->getProject();
		$this->overallconfig=$model->getOverallConfig();
		if (!isset( $this->overallconfig['seperator']))
		{
			$this->overallconfig['seperator'] = "-";
		}
		$this->config=$config;
		$this->opos=$model->getOpponents();
		$this->team=$model->getTeam();
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_RIVALS_PAGE_TITLE'));
		if (!empty($this->team))
		{
			$titleInfo->team1Name = $this->team->name;
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty( $this->division ) && $this->division->id != 0)
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);

		parent::display($tpl);
	}
}
