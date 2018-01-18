<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/pagination.php';

/**
 * View-Roster
 */
class JoomleagueViewRoster extends JLGView
{

	public function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
	    $app 	= Factory::getApplication();
		$document = Factory::getDocument();
		$model = $this->getModel();
		$config=$model->getTemplateConfig($this->getName());
		
		$this->project=$model->getProject();
		$this->overallconfig=$model->getOverallConfig();
		//$this->staffconfig=$model->getTemplateConfig('teamstaff');
		$this->config=$config;

		$playerlayout =  $app->input->get( 'playerlayout', '' );
		$stafflayout =  $app->input->get( 'stafflayout', '' );
		
		if(!empty($playerlayout) && $playerlayout != $this->config['show_players_layout']) {
			$this->config['show_players_layout'] = $playerlayout;
		}
		if(!empty($stafflayout) && $stafflayout != $this->config['show_staff_layout']) {
			$this->config['show_staff_layout'] = $stafflayout;
		}
		
		$this->projectteam=$model->getProjectTeam();
		
		if ($this->projectteam)
		{
			$this->showediticon=$model->hasEditPermission('teamplayer.select');
			$team = $model->getTeam();
			$this->team=$team;
			$players = $model->getTeamPlayers();
			$this->rows=$players;
			// events
			if ($this->config['show_events_stats'])
			{
				$this->positioneventtypes=$model->getPositionEventTypes();
				$this->playereventstats=$model->getPlayerEventStats();
			}
			//stats
			if ($this->config['show_stats'])
			{
				$this->stats=$model->getProjectStats();
				$this->playerstats=$model->getRosterStats();
			}
			$this->stafflist=$model->getStaffList();
		}
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_ROSTER_PAGE_TITLE'));
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
