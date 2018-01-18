<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Playground
 */
class JoomleagueViewPlayground extends JLGView
{
	public function display($tpl = null)
	{
		$this->model = $model = $this->getModel();
		$this->address_string = $model->getAddressString();
		$this->mapconfig = $model->getMapConfig();  // Loads the project-template -settings for the GoogleMap
		$this->config = $model->getTemplateConfig($this->getName());
		$this->games = $model->getNextGames(0, $this->config['show_referee']);
		$this->gamesteams = $model->getTeamsFromMatches($this->games);
		$this->playground = $model->getPlayground() ;
		$this->teams = $model->getTeams();
		$this->project = $model->getProject();
		$this->overallconfig = $model->getOverallConfig();
		$this->extended = $this->getExtended($this->playground->extended, 'playground');
		$this->setPageTitle();

		$document= Factory::getDocument();
		$document->addCustomTag('<meta property="og:title" content="' . $this->playground->name .'"/>');
		$document->addCustomTag('<meta property="og:street-address" content="' . $this->address_string .'"/>');
		parent::display($tpl);
	}


	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_PLAYGROUND_PAGE_TITLE'));
		if (!empty($this->playground->name))
		{
			$titleInfo->playgroundName = $this->playground->name;
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$app = Factory::getApplication();
		$input = $app->input;
		$division = $this->model->getDivision($input->getInt('division', 0));
		if (!empty($division) && $division->id != 0)
		{
			$titleInfo->divisionName = $division->name;
		}
		$this->pagetitle = JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document= Factory::getDocument();
		$document->setTitle(Text::_('COM_JOOMLEAGUE_PLAYGROUND_TITLE'));
		$document->setTitle($this->pagetitle);
	}
}
