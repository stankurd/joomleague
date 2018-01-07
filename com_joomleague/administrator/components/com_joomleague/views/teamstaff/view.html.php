<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		Kurt Norgaz
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewTeamStaff extends JLGView
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
		$projectteam_id = $app->getUserState('com_joomleagueprojectteam_id');

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = BaseDatabaseModel::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($projectteam_id);

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		// build the html select list for positions
		$selectedvalue = $this->item->project_position_id;
		$projectpositions = array();
		$projectpositions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_FUNCTION'));
		if($res = $model->getProjectPositions())
		{
			$projectpositions = array_merge($projectpositions,$res);
		}
		$lists['projectpositions'] = HTMLHelper::_('select.genericlist',$projectpositions,'project_position_id','size="1"','value','text',$selectedvalue);
		unset($projectpositions);

		$matchdays = JoomleagueHelper::getRoundsOptions($project->id,'ASC',false);

		$lists['injury_date']	 = HTMLHelper::_('select.genericlist',$matchdays,'injury_date',
				'size="1"','value','text',$this->item->injury_date);
		$lists['injury_end']	= HTMLHelper::_('select.genericlist',$matchdays,'injury_end',
				'size="1"','value','text',$this->item->injury_end );
		
		// suspension details
		$myoptions = array();
		$myoptions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = HTMLHelper::_('select.option','1',Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['suspension'] = HTMLHelper::_('select.radiolist',$myoptions,'suspension','size="1"','value','text',$this->item->suspension);
		unset($myoptions);

		$lists['suspension_date'] = HTMLHelper::_('select.genericlist',$matchdays,'suspension_date','size="1"','value','text',$this->item->suspension_date);
		$lists['suspension_end'] = HTMLHelper::_('select.genericlist',$matchdays,'suspension_end','size="1"','value','text',$this->item->suspension_end);

		// away details
		$myoptions = array();
		$myoptions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = HTMLHelper::_('select.option','1',Text::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['away'] = HTMLHelper::_('select.radiolist',$myoptions,'away','size="1"','value','text',$this->item->away);
		unset($myoptions);

		$lists['away_date'] = HTMLHelper::_('select.genericlist',$matchdays,'away_date','size="1"','value','text',$this->item->away_date);
		$lists['away_end'] = HTMLHelper::_('select.genericlist',$matchdays,'away_end','size="1"','value','text',$this->item->away_end);

		$extended = $this->getExtended($this->item->extended,'teamstaff');
		$this->extended = $extended;

		$this->project = $project;
		$this->projectteam = $projectteam;
		$this->lists = $lists;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		$app = Factory::getApplication();
		$jinput = $app->input;

		$jinput->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$params = ComponentHelper::getParams('com_joomleague');
		$name = JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,
				JoomleagueHelper::defaultNameFormat());
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_TITLE') . ': ' . $name;
		JLToolbarHelper::title($text);

		if($isNew)
		{
			JLToolBarHelper::apply('teamstaff.apply');
			JLToolBarHelper::save('teamstaff.save');
			JLToolBarHelper::cancel('teamstaff.cancel');
		}
		else
		{
			JLToolBarHelper::apply('teamstaff.apply');
			JLToolBarHelper::save('teamstaff.save');
			JLToolBarHelper::cancel('teamstaff.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolbarHelper::help('screen.joomleague',true);
	}
}
