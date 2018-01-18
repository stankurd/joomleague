<?php
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/pagination.php';
require_once JLG_PATH_SITE.'/models/ranking.php';
require_once JLG_PATH_SITE.'/models/results.php';
require_once JLG_PATH_SITE.'/views/results/view.html.php';

jimport('joomla.filesystem.file');
jimport('joomla.html.pane');
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * View-ResultsRanking
 */
class JoomleagueViewResultsranking extends JoomleagueViewResults {

	public function display($tpl = null)
	{
		//JHtml::_('behavior.framework');
		$app = Factory::getApplication();
		$params = $app->getParams();
		// get a reference of the page instance in joomla
		$document = Factory :: getDocument();
		$uri = URI::getinstance();
		// add the css files
		$version = urlencode(JoomleagueHelper::getVersion());
		$css		= 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);
		// add some javascript
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript( Uri::base(true).'/components/com_joomleague/assets/js/results.js?v='.$version);
		// add the ranking model
		$rankingmodel = new JoomleagueModelRanking();
		$project = $rankingmodel->getProject();
		// add the ranking config file
		$rankingconfig = $rankingmodel->getTemplateConfig('ranking');
		$rankingmodel->computeRanking();
		// add the results model
		$resultsmodel	= new JoomleagueModelResults();
    $division_id = $resultsmodel->getDivisionID();
    	
		// add the results config file

		$mdlRound = BaseDatabaseModel::getInstance("Round", "JoomleagueModel");
		$roundcode = $mdlRound->getRoundcode($rankingmodel->round);
		$rounds = JoomleagueHelper::getRoundsOptions($project->id, 'ASC', true);

		$resultsconfig = $resultsmodel->getTemplateConfig('results');
		if (!isset($resultsconfig['switch_home_guest'])){$resultsconfig['switch_home_guest']=0;}
		if (!isset($resultsconfig['show_dnp_teams_icons'])){$resultsconfig['show_dnp_teams_icons']=0;}
		if (!isset($resultsconfig['show_results_ranking'])){$resultsconfig['show_results_ranking']=0;}

		// merge the 2 config files
		$config = array_merge($rankingconfig, $resultsconfig);

		$this->model=$rankingmodel;
		$this->project=$resultsmodel->getProject();
		$this->overallconfig=$resultsmodel->getOverallConfig();
		$this->config=array_merge($this->overallconfig, $config);
		$this->tableconfig=$rankingconfig;
		$this->params=$params;
		$this->showediticon=$resultsmodel->getShowEditIcon();
		$this->division=$resultsmodel->getDivision();
		$this->divisions=$rankingmodel->getDivisions();
		$this->divLevel=$rankingmodel->divLevel;
		$this->matches=$resultsmodel->getMatches();
		$this->round=$resultsmodel->roundid;
		$this->roundid=$resultsmodel->roundid;
		$this->roundcode=$roundcode;
		$options = $this->getRoundSelectNavigation($rounds, $division_id); 			
    $this->matchdaysoptions = $options;
		$this->currenturl=JoomleagueHelperRoute::getResultsRankingRoute($resultsmodel->getProject()->slug, $this->round);
		$this->rounds=$resultsmodel->getRounds();
		$this->favteams=$resultsmodel->getFavTeams($this->project);
		$this->projectevents=$resultsmodel->getProjectEvents();
		$this->model=$resultsmodel;
		$this->isAllowed=$resultsmodel->isAllowed();

		$this->type=$rankingmodel->type;
		$this->from=$rankingmodel->from;
		$this->to=$rankingmodel->to;

		$this->currentRanking=$rankingmodel->currentRanking;
		$this->previousRanking=$rankingmodel->previousRanking;
		$this->homeRanking=$rankingmodel->homeRank;
		$this->awayRanking=$rankingmodel->awayRank;
		$this->current_round=$rankingmodel->current_round;
		$this->teams=$rankingmodel->getTeamsIndexedByPtid($division_id);
		$this->previousgames=$rankingmodel->getPreviousGames();

		$this->action =$uri->toString();
		//rankingcolors
		if (!isset ($this->config['colors'])) {
			$this->config['colors'] = "";
		}
		$this->colors=$rankingmodel->getColors($this->config['colors']);

		// Set page title
		if ($this->params->get('what_to_show_first', 0) == 0) {
			$prefix = Text::_('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE').' & ' . Text :: _('COM_JOOMLEAGUE_RANKING_PAGE_TITLE');
			$pageTitleFormat = $resultsconfig["page_title_format"];
		}
		else
		{
			$prefix = Text::_('COM_JOOMLEAGUE_RANKING_PAGE_TITLE').' & ' . Text :: _('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE');
			$pageTitleFormat = $rankingconfig["page_title_format"];
		}
		$titleInfo = JoomleagueHelper::createTitleInfo($prefix);
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
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $pageTitleFormat);
		$document->setTitle($this->pagetitle);
		
		/*
		//build feed links
		$feed = 'index.php?option=com_joomleague&view=results&p='.$this->project->id.'&format=feed';
		$rss = array('type' => 'application/rss+xml', 'title' => Text::_('COM_JOOMLEAGUE_RESULTS_RSSFEED'));

		// add the links
		$document->addHeadLink(JRoute::_($feed.'&type=rss'), 'alternate', 'rel', $rss);
		*/
		JLGView::display($tpl);
	}

	public function getRoundSelectNavigation($rounds, $division_id=0)
	{
		$currentUrl = JoomleagueHelperRoute::getResultsRankingRoute($this->project->slug, $this->roundid, $division_id);
		$options = array();
		foreach ($rounds as $r)
		{
			$link = JoomleagueHelperRoute::getResultsRankingRoute($this->project->slug, $r->id, $division_id);
			$options[] = HTMLHelper::_('select.option', $link, $r->roundcode);
		}
		return HTMLHelper::_('select.genericlist', $options, 'select-round', 'onchange="joomleague_changedoc(this);"',	'value', 'text', $currentUrl);
	}
	
}
