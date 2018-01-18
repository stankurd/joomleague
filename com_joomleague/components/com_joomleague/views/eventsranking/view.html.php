<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Eventsranking
 */
class JoomleagueViewEventsRanking extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document = Factory::getDocument();

		// read the config-data from template file
		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());

		$this->project=$model->getProject();
		$this->division=$model->getDivision();
		$this->matchid=$model->matchid;
		$this->overallconfig=$model->getOverallConfig();
		$this->config=$config;
		$this->teamid=$model->getTeamId();
		$this->teams=$model->getTeamsIndexedById();
		$this->favteams=$model->getFavTeams();
		$this->eventtypes=$model->getEventTypes();
		$this->limit=$model->getLimit();
		$this->limitstart=$model->getLimitStart();
		$this->pagination=$this->get('Pagination');
		$this->eventranking=$model->getEventRankings($this->limit);
		// @todo: check
		$this->multiple_events = count($this->eventtypes) > 1;
		
		$prefix = Text::_('COM_JOOMLEAGUE_EVENTSRANKING_PAGE_TITLE');
		
		if ( $this->multiple_events )
		{
			$prefix .= " - " . Text::_( 'COM_JOOMLEAGUE_EVENTSRANKING_TITLE' );
		}
		else
		{
			// Next query will result in an array with exactly 1 statistic id
			$evid = array_keys($this->eventtypes);
			
			// Selected one valid eventtype, so show its name
			// @todo: check
			if ($evid) {
				$prefix .= " - " . Text::_($this->eventtypes[$evid[0]]->name);
			} else {
				$prefix .= '';
			}
			
		}

		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo($prefix);
		if (!empty($this->teamid) && array_key_exists($this->teamid, $this->teams))
		{
			$titleInfo->team1Name = $this->teams[$this->teamid]->name;
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
