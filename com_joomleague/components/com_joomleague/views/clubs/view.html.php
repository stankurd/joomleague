<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * Clubs view
 */
class JoomleagueViewClubs extends JLGView
{
	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->config = $model->getTemplateConfig($this->getName());
		$this->project = $model->getProject();
		$this->division = $model->getDivision() ;
		$this->overallconfig = $model->getOverallConfig();
		$this->clubs = $model->getClubs();
		$this->setPageTitle();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_CLUBS_PAGE_TITLE'));
		if (!empty( $this->club ) )
		{
			$titleInfo->clubName = $this->club->name;
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty($this->division))
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document= Factory::getDocument();
		$document->setTitle($this->pagetitle);
	}
}
