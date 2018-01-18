<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/pagination.php';
require_once JLG_PATH_SITE.'/models/matrix.php';
require_once JLG_PATH_SITE.'/models/results.php';
require_once JLG_PATH_SITE.'/views/results/view.html.php';

jimport('joomla.filesystem.file');
jimport('joomla.html.pane');

/**
 * View-ResultsMatrix
 */
class JoomleagueViewResultsmatrix extends JoomleagueViewResults  {

	public function display($tpl = null)
	{
		//JHtml::_('behavior.framework');
		$app = Factory::getApplication();
		$params = $app->getParams();
		// get a reference of the page instance in joomla
		$document = Factory::getDocument();
		$uri = Uri::getinstance();
		// add the css files
		$version = urlencode(JoomleagueHelper::getVersion());
		$css = 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);
		// add the matrix model
		$matrixmodel = new JoomleagueModelMatrix();
		// add the matrix config file
		$matrixconfig = $matrixmodel->getTemplateConfig('matrix');

		// add the results model
		$resultsmodel	= new JoomleagueModelResults();
		$project = $resultsmodel->getProject();
		
		// add some javascript
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript( Uri::base(true).'/components/com_joomleague/assets/js/results.js?v='.$version );
		// add the results config file
		$resultsconfig = $resultsmodel->getTemplateConfig('results');
		
		$mdlRound = BaseDatabaseModel::getInstance("Round", "JoomleagueModel");
		$roundcode = $mdlRound->getRoundcode($resultsmodel->roundid);
		$rounds = JoomleagueHelper::getRoundsOptions($project->id, 'ASC', true);
		
		
		if (!isset($resultsconfig['switch_home_guest'])){$resultsconfig['switch_home_guest']=0;}
		if (!isset($resultsconfig['show_dnp_teams_icons'])){$resultsconfig['show_dnp_teams_icons']=0;}
		if (!isset($resultsconfig['show_results_ranking'])){$resultsconfig['show_results_ranking']=0;}
		$resultsconfig['show_matchday_dropdown']=0;
		// merge the 2 config files
		$config = array_merge($matrixconfig, $resultsconfig);

		$this->project=$resultsmodel->getProject();
		$this->overallconfig=$resultsmodel->getOverallConfig();
		$this->config=array_merge($this->overallconfig, $config);
		$this->tableconfig=$matrixconfig;
		$this->params=$params;
		$this->showediticon=$resultsmodel->getShowEditIcon();
		$this->division=$resultsmodel->getDivision();

		$this->divisionid=$matrixmodel->getDivisionID();
		$this->division=$matrixmodel->getDivision();
		$this->teams=$matrixmodel->getTeamsIndexedByPtid($matrixmodel->getDivisionID());
		$this->results=$matrixmodel->getMatrixResults($matrixmodel->getProject()->id);
		$this->favteams=$matrixmodel->getFavTeams();

		$this->matches=$resultsmodel->getMatches();
		$this->round=$resultsmodel->roundid;
		$this->roundid=$resultsmodel->roundid;
		$this->roundcode=$roundcode;
		
		$options = $this->getRoundSelectNavigation($rounds);

		$this->matchdaysoptions=$options;
		$this->currenturl=JoomleagueHelperRoute::getResultsMatrixRoute($resultsmodel->getProject()->slug, $this->roundid);
		$this->rounds=$resultsmodel->getRounds();
		$this->favteams=$resultsmodel->getFavTeams($this->project);
		$this->projectevents=$resultsmodel->getProjectEvents();
		$this->model=$resultsmodel;
		$this->isAllowed=$resultsmodel->isAllowed();
		$this->action=$uri->toString();

		// Set page title
		if ($this->params->get('what_to_show_first', 0) == 0) {
			$prefix = Text::_('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE').' & ' . Text :: _('COM_JOOMLEAGUE_MATRIX_PAGE_TITLE');
			$pageTitleFormat = $resultsconfig["page_title_format"];
		}
		else
		{
			$prefix = Text::_('COM_JOOMLEAGUE_MATRIX_PAGE_TITLE').' & ' . Text :: _('COM_JOOMLEAGUE_RESULTS_PAGE_TITLE');
			$pageTitleFormat = $matrixconfig["page_title_format"];
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

	function getRoundSelectNavigation($rounds)
	{
		$options = array();
		foreach ($rounds as $r)
		{
			$link = JoomleagueHelperRoute::getResultsMatrixRoute($this->project->slug, $r->value);
			$options[] = HTMLHelper::_('select.option', $link, $r->text);
		}
		return $options;
	}
}
