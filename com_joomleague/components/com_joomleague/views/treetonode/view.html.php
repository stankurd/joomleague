<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Treetonode
 */
class JoomleagueViewTreetonode extends JLGView
{
	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document= Factory::getDocument();

		$model = $this->getModel();
		$project = $model->getProject();
		//no treeko !!!
		$config = $model->getTemplateConfig('tree');
		
		$this->project=$model->getProject();
		$this->overallconfig=$model->getOverallConfig();
		$this->config=$config;
		$this->node=$model->getTreetonode();
		
		$this->roundname=$model->getRoundName();
		$this->model=$model;
		
		// Set page title
		///TODO: treeto name, no project name
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_TREETO_PAGE_TITLE'));
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$division = $model->getDivision(Factory::getApplication()->input->getInt('division',0));
		if (!empty( $division ) && $division->id != 0)
		{
			$titleInfo->divisionName = $division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}
}
