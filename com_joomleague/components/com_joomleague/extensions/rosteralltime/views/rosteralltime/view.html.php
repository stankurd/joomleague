<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT.DS.'helpers'.DS.'pagination.php');

//jimport('joomla.application.component.view');

class JoomleagueViewRosteralltime extends JLGView
{

	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
	    $app 	= Factory::getApplication();
		$document = Factory::getDocument();
		$model = $this->getModel();
		$user = Factory::getUser();
		$config = $model->getTemplateConfig($this->getName());
		$state = $this->get('State');
		$items = $this->get('Items');		
		$pagination	= $this->get('Pagination');
		$this->config = $config;
        $this->team = $model->getTeam();
        $this->rows = $model->getTeamPlayers();
        $this->playerposition = $model->getPlayerPosition();
        $this->project = $model->getProject();
        $this->positioneventtypes = $model->getPositionEventTypes();
        $this->items = $items;
        $this->state = $state;
        $this->user = $user;
        $this->pagination = $pagination;
        // Set page title
        $titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_ROSTERALLTIME_PAGE_TITLE'));
        if (!empty($this->team))
        {
            if ( $this->config['show_team_shortform'] == 1 && !empty($this->team->short_name))
            {
                $titleInfo->team1Name = $this->team->name ." [". $this->team->short_name . "]";
            }
            else
            {
                $titleInfo->team1Name = $this->team->name;
            }
        }
        else
        {
            $titleInfo->team1Name = "Project team does not exist";
        }
        if (!empty($this->project))
        {
            $titleInfo->projectName = $this->project->name;
            $titleInfo->leagueName = $this->project->league_name;
            $titleInfo->seasonName = $this->project->season_name;
        }
        $division = $model->getDivision($app->input->getInt('division',0));
        if (!empty( $division ) && $division->id != 0)
        {
            $titleInfo->divisionName = $division->name;
        }
        $this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
        $document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}

}
?>