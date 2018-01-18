<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Matrix
 */
class JoomleagueViewMatrix extends JLGView
{
	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document= Factory::getDocument();

		$model=$this->getModel();
		$config=$model->getTemplateConfig($this->getName());
		$project=$model->getProject();
		
		$this->model=$model;
		$this->project=$project;
		$this->overallconfig=$model->getOverallConfig();
		$this->config=$config;
		$this->divisionid=$model->getDivisionID();
		$this->roundid=$model->getRoundID();
		$this->division=$model->getDivision();
		$this->round=$model->getRound();
		$this->teams=$model->getTeamsIndexedByPtid($model->getDivisionID());
		$this->results=$model->getMatrixResults($model->projectid);
		
		if ($project->project_type == 'DIVISIONS_LEAGUE' && !$this->divisionid)
		{
			$divisions = $model->getDivisions();
			$this->divisions=$divisions;
		}
		
		if(!is_null($project)) {
			$this->favteams=$model->getFavTeams();
		}
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_MATRIX_PAGE_TITLE'));
		if (!empty($this->round))
		{
			$titleInfo->roundName = $this->round->name;
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
