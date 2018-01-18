<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/pagination.php';

/**
 * View-Referees
 */
class JoomleagueViewReferees extends JLGView
{
	var $overallconfig = null;
	var $config = null;
	var $project = null;
	var $division = null;
	var $referees = null;
	var $pageTitle = null;

	public function display($tpl = null)
	{
		$model = $this->getModel();
		$this->overallconfig = $model->getOverallConfig();
		$this->config = $model->getTemplateConfig($this->getName());
		if (!$this->config)
		{
			$this->config = $model->getTemplateConfig('players');
		}
		$this->project = $model->getProject();
		$this->division = $model->getDivision($this->input->getInt('division', 0));
		$this->referees = $model->getReferees();
//		$this->positioneventtypes = $model->getPositionEventTypes();
		$this->setPageTitle();

		parent::display($tpl);
	}

	protected function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_REFEREES_PAGE_TITLE'));
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
		$this->pageTitle = JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document = Factory::getDocument();
		$document->setTitle($this->pageTitle);
	}

	function formattedBirthDay($referee)
	{
		if ($referee->birthday == '0000-00-00')
		{
			$birthdayStr = '-';
		}
		else
		{
			switch ($this->config['show_birthday']) {
				case 1:     // show Birthday and Age
					$birthdayStr = HTMLHelper::date($referee->birthday . ' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'),
							JoomleagueHelper::getTimezone($this->project, $this->overallconfig)) .
						'&nbsp;(' . JoomleagueHelper::getAge($referee->birthday, $referee->deathday) . ')';
					break;

				case 2:     // show Only Birthday
					$birthdayStr = HTMLHelper::date($referee->birthday . ' UTC', Text::_('COM_JOOMLEAGUE_GLOBAL_DAYDATE'),
						JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					break;

				case 3:     // show Only Age
					$birthdayStr = JoomleagueHelper::getAge($referee->birthday, $referee->deathday);
					break;

				case 4:     // show Only Year of birth
					$birthdayStr = HTMLHelper::date($referee->birthday . ' UTC', Text::_('%Y'),
						JoomleagueHelper::getTimezone($this->project, $this->overallconfig));
					break;

				default:
					$birthdayStr = '';
					break;
			}
		}
		return $birthdayStr;
	}
}
