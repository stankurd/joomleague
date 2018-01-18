<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Teams
 */
class JoomleagueViewTeams extends JLGView
{
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->overallconfig = $model->getOverallConfig();
		$this->config = $model->getTemplateConfig($this->getName());
		$this->project = $model->getProject();
		$this->division = $model->getDivision();
		$this->teams = $model->getTeams();
		$this->setPageTitle();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_TEAMS_TITLE'));
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
		$document= Factory::getDocument();
		$document->setTitle($this->pagetitle);
	}
}
