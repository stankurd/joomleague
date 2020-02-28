<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Teaminfo
 */
class JoomleagueViewTeamInfo extends JLGView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel();
		$this->config = $model->getTemplateConfig( $this->getName() );
		$this->overallconfig = $model->getOverallConfig();
		$this->project = $model->getProject();
		$this->division = $model->getDivision($input->getInt('division',0));
		$this->team = $model->getTeamByProject();
		$this->club = $model->getClub() ;
		$this->seasons = $model->getSeasons( $this->config );
		$this->leaguerankoverview = $model->getLeagueRankOverview( $this->seasons );
		$this->leaguerankoverviewdetail = $model->getLeagueRankOverviewDetail( $this->seasons );
		if ( isset($this->project->id) )
		{
			$this->trainingData = $model->getTrainingData($this->project->id);
			$this->daysOfWeek = array(
				1 => Text::_('COM_JOOMLEAGUE_GLOBAL_MONDAY'),
				2 => Text::_('COM_JOOMLEAGUE_GLOBAL_TUESDAY'),
				3 => Text::_('COM_JOOMLEAGUE_GLOBAL_WEDNESDAY'),
				4 => Text::_('COM_JOOMLEAGUE_GLOBAL_THURSDAY'),
				5 => Text::_('COM_JOOMLEAGUE_GLOBAL_FRIDAY'),
				6 => Text::_('COM_JOOMLEAGUE_GLOBAL_SATURDAY'),
				7 => Text::_('COM_JOOMLEAGUE_GLOBAL_SUNDAY')
			);
		}
		
		$this->showediticon = $model->hasEditPermission('projectteam.edit',$this->team->project_team_id,'teaminfo');
		$this->extended = $this->getExtended($this->team->teamextended, 'team');

		$this->setPageTitle();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_TEAMINFO_PAGE_TITLE'));
		if (!empty($this->team))
		{
			$titleInfo->team1Name = $this->team->tname;
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
		$document	= Factory::getDocument();
		$document->setTitle($this->pagetitle);
	}
}
