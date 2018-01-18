<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_SITE.'/views/person/view.html.php';

/**
 * Referee View
 */
class JoomleagueViewReferee extends JoomleagueViewPerson
{
	var $refereeCareer = null;
	var $showEditIcon = null;
	var $games = null;
	var $teams = null;

	public function setViewSpecificParameters()
	{
		$model = $this->getModel();
		$this->projectPerson = $model->getProjectReferee();
		$this->refereeCareer = $model->getRefereeCareer();
		$this->showEditIcon = $model->hasEditPermission('projectreferee.edit');
		if ($this->config['show_gameshistory'])
		{
			$this->games = $model->getGames();
			$this->teams = $model->getTeamsIndexedByPtid();
		}
		$this->extended = $this->getExtended($this->person->extended, 'projectreferee');
		$this->setPageTitle('About %1$s %2$s as a Referee');
	}

	public function getEditLinkAndImage()
	{
		$editInfo = new stdClass;
		$editInfo->link = JoomleagueHelperRoute::getRefereeRoute($this->project->id, $this->projectPerson->id,
			'projectreferee.edit');
		$editInfo->image = HTMLHelper::image('media/com_joomleague/jl_images/edit.png', Text::_('COM_JOOMLEAGUE_REFEREE_EDIT'),
			array('title' => Text::_('COM_JOOMLEAGUE_REFEREE_EDIT')));
		return $editInfo;
	}
}
