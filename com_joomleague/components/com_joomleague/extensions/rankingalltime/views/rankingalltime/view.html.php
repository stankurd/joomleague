<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

require_once (JPATH_COMPONENT . '/helpers/pagination.php');
//require_once (JLG_PATH_ADMIN . '/models/divisions.php');
//require_once (JLG_PATH_SITE . '/models/project.php');
JLoader::register('JoomleagueModelProject', JLG_PATH_SITE . '/models/project.php');
JLoader::register('JoomleagueModelDiovisions', JLG_PATH_ADMIN . '/models/divisions.php');



class JoomleagueViewRankingAllTime extends JLGView
{

    function display($tpl = null)
    {
        $app = Factory::getApplication();
        // Get a refrence of the page instance in joomla
        $document = Factory::getDocument();
        $uri = Uri::getInstance();
        $this->project->id = $app->input->getInt('p', 0);
        $mdlproject = new JoomleagueModelProject();
        $model = $this->getModel();
        $this->overallconfig = $mdlproject->getOverallConfig();
        $this->config = $mdlproject->getTemplateConfig($this->getName());
        $this->tableconfig = $this->config;
        $this->project = $mdlproject->getProject();
        $this->projectName = $this->leaguename;
        $this->projectids = $model->getAllProject();
        $project_ids = implode(",", $this->projectids);
        $this->project_ids = $project_ids;
        $this->teams = $model->getAllTeamsIndexedByPtid($project_ids);
        $this->matches = $model->getAllMatches($project_ids);
        $this->ranking = $model->getAllTimeRanking();
        $this->tableconfig = $model->getAllTimeParams();
        $this->config = $model->getAllTimeParams();
        $this->currentRanking = $model->getCurrentRanking();
        $this->action = $uri->__toString();
        $this->lists = $lists;
        $this->show_debug_info = ComponentHelper::getParams('com_joomleague')->get('show_debug_info', 0);

        $this->colors = $model->getColors($this->config['colors']);
        $this->setPageTitle();
        $this->setStyleSheet();
        parent::display($tpl);
    }
    function setPageTitle()
    {
        $titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_RANKINGALLTIME_PAGE_TITLE'));
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
?>