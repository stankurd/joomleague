<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

//require_once JPATH_COMPONENT.'/helpers/pagination.php';

/**
 * Clubinfo view
 */
class JoomleagueViewClubInfo extends JLGView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel();
		$this->club = $model->getClub() ;
		$this->config = $model->getTemplateConfig( $this->getName() );
		$this->project = $model->getProject();
		$this->division = $model->getDivision($input->getInt('division',0));
		$this->overallconfig = $model->getOverallConfig();
		$this->teams = $model->getTeamsByClubId();
		$this->playgrounds = $model->getPlaygrounds();
		$this->address_string = $model->getAddressString();
		$this->mapconfig = $model->getMapConfig(); // Loads the project-template -settings for the GoogleMap
		
		$this->showediticon = $model->hasEditPermission('club.edit',$this->club->id,'clubinfo');
		$this->extended = $this->getExtended($this->club->extended, 'club');
		$this->setPageTitle();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_CLUBINFO_PAGE_TITLE'));
		if (!empty($this->club))
		{
			$titleInfo->clubName = $this->club->name;
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
		$this->pagetitle = JoomleagueHelper::formatTitle($titleInfo, $this->config['page_title_format']);
		$document = Factory::getDocument();
		$document->setTitle($this->pagetitle);
	}
}
