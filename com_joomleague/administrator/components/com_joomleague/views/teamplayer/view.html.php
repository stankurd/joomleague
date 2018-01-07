<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTeamPlayer extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$model = $this->getModel();
		$lists = array();

		$project_id = $app->getUserState('com_joomleagueproject');
		$project_team_id = $app->getUserState('com_joomleagueproject_team_id');

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = BaseDatabaseModel::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($project_team_id);

		// get the project_player data of the project_team
		// $project_player = $this->get('Data');
		$isNew = ($this->item->id < 1);

		// fail if checked out not by 'me'
		// @todo: fix!
		/*
		 * if ($model->isCheckedOut($user->get('id')))
		 * {
		 * $msg = Text::sprintf('DESCBEINGEDITTED',
		 * Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYER_THEPLAYER'),
		 * $project_player->name);
		 * $app->redirect('index.php?option=com_joomleague', $msg);
		 * }
		 *
		 * // Edit or Create?
		 * if ($isNew)
		 * {
		 * $project_player->order = 0;
		 * }
		 */

		// build the html select list for positions
		$selectedvalue = $this->item->project_position_id;
		$projectpositions = array();
		$projectpositions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION'));
		if($res = $model->getProjectPositions())
		{
			$projectpositions = array_merge($projectpositions,$res);
		}
		$lists['projectpositions'] = HTMLHelper::_('select.genericlist',$projectpositions,'project_position_id','class="inputbox" size="1"','value','text',
				$selectedvalue);
		unset($projectpositions);

		$matchdays = JoomleagueHelper::getRoundsOptions($project->id,'ASC',false);

		// injury details
		$myoptions = array();
		$myoptions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = HTMLHelper::_('select.option','1',Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['injury'] = HTMLHelper::_('select.radiolist',$myoptions,'injury','class="inputbox" size="1"','value','text',$this->item->injury);
		unset($myoptions);

		$lists['injury_date'] = HTMLHelper::_('select.genericlist',$matchdays,'injury_date','class="inputbox" size="1"','value','text',
				$this->item->injury_date);

		$lists['injury_end'] = HTMLHelper::_('select.genericlist',$matchdays,'injury_end','class="inputbox" size="1"','value','text',
				$this->item->injury_end);

		// suspension details
		$myoptions = array();
		$myoptions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = HTMLHelper::_('select.option','1',Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['suspension'] = HTMLHelper::_('select.radiolist',$myoptions,'suspension','class="radio" size="1"','value','text',$this->item->suspension);
		unset($myoptions);

		$lists['suspension_date'] = HTMLHelper::_('select.genericlist',$matchdays,'suspension_date','class="inputbox" size="1"','value','text',
				$this->item->suspension_date);

		$lists['suspension_end'] = HTMLHelper::_('select.genericlist',$matchdays,'suspension_end','class="inputbox" size="1"','value','text',
				$this->item->suspension_end);

		// away details
		$myoptions = array();
		$myoptions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = HTMLHelper::_('select.option','1',Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['away'] = HTMLHelper::_('select.radiolist',$myoptions,'away','class="inputbox" size="1"','value','text',$this->item->away);
		unset($myoptions);

		$lists['away_date'] = HTMLHelper::_('select.genericlist',$matchdays,'away_date','class="inputbox" size="1"','value','text',$this->item->away_date);

		$lists['away_end'] = HTMLHelper::_('select.genericlist',$matchdays,'away_end','class="inputbox" size="1"','value','text',$this->item->away_end);

		$extended = $this->getExtended($this->item->extended,'teamplayer');
		$this->extended = $extended;

		$this->project = $project;

		// $this->teamws = $teamws;
		$this->projectteam = $projectteam;

		$this->lists = $lists;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		// Set toolbar items for the page
		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = ComponentHelper::getParams($option);
		$name = JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,
				JoomleagueHelper::defaultNameFormat());
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_ADMIN_TEAMPLAYER_TITLE').': '.$name;
		JLToolBarHelper::title($text);

		if($isNew)
		{
			JLToolBarHelper::apply('teamplayer.apply');
			JLToolBarHelper::save('teamplayer.save');
			JLToolBarHelper::cancel('teamplayer.cancel');
		}
		else
		{
			JLToolBarHelper::apply('teamplayer.apply');
			JLToolBarHelper::save('teamplayer.save');
			JLToolBarHelper::cancel('teamplayer.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
