<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/views/person/view.html.php';

/**
 * Staff View
 */
class JoomleagueViewStaff extends JoomleagueViewPerson
{
	var $staffCareer = null;
	var $showEditIcon = null;
	var $history = null;
	var $games = null;
	var $teams = null;
	var $stats = null;
	var $staffStats = null;
	var $careerStats = null;

	function setViewSpecificParameters()
	{
		$model = $this->getModel();
		$current_round = $this->project->current_round;
		$this->projectPerson = $model->getTeamStaffByRound($current_round, $this->person->id);
		$this->staffCareer = $model->getStaffCareer();
		$this->showEditIcon = $model->getAllowed($this->config['edit_own_player']) && $model->hasEditPermission('teamstaff.edit');
		$this->stats = $model->getStaffStatTypes($current_round);
		$this->staffStats = $model->getStaffStats($current_round);
		$this->careerStats = $model->getStaffCareerStats($current_round);
		$this->extended = $this->getExtended($this->person->extended, 'teamstaff');
		$this->setPageTitle('About %1$s %2$s as a Staff member');
	}

	function getEditLinkAndImage()
	{
		$editInfo = new stdClass;
		$editInfo->link = JoomleagueHelperRoute::getStaffRoute($this->project->id, $this->projectPerson->project_team_id,
			$this->projectPerson->id, 'teamstaff.edit');
		$editInfo->image = HTMLHelper::image('media/com_joomleague/jl_images/edit.png', Text::_('COM_JOOMLEAGUE_STAFF_EDIT'),
			array('title' => Text::_('COM_JOOMLEAGUE_STAFF_EDIT')));
		return $editInfo;
	}
}
