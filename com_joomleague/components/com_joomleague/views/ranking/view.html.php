<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/pagination.php';
require_once JLG_PATH_ADMIN.'/models/jlgitem.php';
require_once JLG_PATH_ADMIN.'/models/jlglist.php';
require_once JLG_PATH_ADMIN.'/models/divisions.php';

/**
 * View-Ranking
 */
class JoomleagueViewRanking extends JLGView {
	
	public function display($tpl = null) 
	{
		$this->model = $model = $this->getModel();
		$this->overallconfig = $model->getOverallConfig();
		$this->config = $model->getTemplateConfig($this->getName());
		$this->tableconfig = $this->config;
		$this->project = $model->getProject();
		$this->divisions = $model->getDivisions();
		$this->division = $model->getDivision($this->input->getInt('division', 0));
		$rounds = JoomleagueHelper::getRoundsOptions($this->project->id, 'ASC', true);
		$model->setProjectId($this->project->id);
		$model->computeRanking();
		$this->round = $model->round;
		$this->part = $model->part;
		$this->rounds = $rounds;
		$this->type = $model->type;
		$this->from = $model->from;
		$this->to = $model->to;
		$this->divLevel = $model->divLevel;
		$this->currentRanking = $model->currentRanking;
		$this->previousRanking = $model->previousRanking;
		$this->homeRanking = $model->homeRank;
		$this->awayRanking = $model->awayRank;
		$this->current_round = $model->current_round;
		$this->previousgames = $model->getPreviousGames();
		$this->teams=$model->getTeamsIndexedByPtid();

		$fromMatchDay[] = HTMLHelper::_('select.option', '0', JText::_('COM_JOOMLEAGUE_RANKING_FROM_MATCHDAY'));
		$fromMatchDay = array_merge($fromMatchDay, $rounds);
		$toMatchDay[] = HTMLHelper::_('select.option', '0', JText::_('COM_JOOMLEAGUE_RANKING_TO_MATCHDAY'));
		$toMatchDay = array_merge($toMatchDay, $rounds);
		$opp_arr = array();
		$opp_arr[] = HTMLHelper::_('select.option', "0", JText::_('COM_JOOMLEAGUE_RANKING_FULL_RANKING'));
		$opp_arr[] = HTMLHelper::_('select.option', "1", JText::_('COM_JOOMLEAGUE_RANKING_HOME_RANKING'));
		$opp_arr[] = HTMLHelper::_('select.option', "2", JText::_('COM_JOOMLEAGUE_RANKING_AWAY_RANKING'));

		$this->lists['frommatchday'] = $fromMatchDay;
		$this->lists['tomatchday'] = $toMatchDay;
		$this->lists['type'] = $opp_arr;

		if (!isset ($config['colors']))
		{
			$config['colors'] = '';
		}

		$this->colors = $model->getColors($config['colors']);
		//		$this->result=$model->getTeamInfo());
		//		$this->pageNav=$model->pagenav( "ranking", count( $rounds ), $sr->to ) );
		//		$this->pageNav2=$model->pagenav2( "ranking", count( $rounds ), $sr->to ) );

		$uri = Uri::getinstance();
		$this->action = $uri->__toString();

		$this->setPageTitle();
		$this->setStyleSheet();

		parent::display($tpl);
	}

	function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(JText::_('COM_JOOMLEAGUE_RANKING_PAGE_TITLE'));
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty($this->division ) && $this->division->id != 0)
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pageTitle = JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document = Factory :: getDocument();
		$document->setTitle($this->pageTitle);
	}

	function setStyleSheet()
	{
		$document = Factory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$css = 'components/com_joomleague/assets/css/tabs.css?v='.$version;
		$document->addStyleSheet($css);
	}
}
