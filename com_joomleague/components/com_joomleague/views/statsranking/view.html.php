<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-StatsRanking
 */
class JoomleagueViewStatsRanking extends JLGView
{
	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document = Factory::getDocument();

		// read the config-data from template file
		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());
		
		$this->project=$model->getProject();
		$this->division=$model->getDivision();
		$this->teamid=$model->getTeamId();
		$teams = $model->getTeamsIndexedById();
		if ( $this->teamid != 0 )
		{
			foreach ( $teams AS $k => $v)
			{
				if ($k != $this->teamid)
				{
					unset( $teams[$k] );
				}
			}
		}

		$this->teams=$teams;
		$this->overallconfig=$model->getOverallConfig();
		$this->config=$config;
		$this->favteams=$model->getFavTeams();
		$this->stats=$model->getProjectUniqueStats();
		$this->playersstats=$model->getPlayersStats();
		$this->limit=$model->getLimit();
		$this->limitstart=$model->getLimitStart();
		$this->multiple_stats = count($this->stats) > 1;

		$prefix = Text::_('COM_JOOMLEAGUE_STATSRANKING_PAGE_TITLE');
		if ( $this->multiple_stats )
		{
			$prefix .= " - " . Text::_( 'COM_JOOMLEAGUE_STATSRANKING_TITLE' );
		}
		else
		{
			// Next query will result in an array with exactly 1 statistic id
			$sid = array_keys($this->stats);
			// Take the first result then.
			$prefix .= " - " . $this->stats[$sid[0]]->name;
		}

		// Set page title
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
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}
}
