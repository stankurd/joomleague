<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		Kurt Norgaz
 */
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
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$uri = JUri::getInstance();
		$user = JFactory::getUser();
		$model = $this->getModel();

		$lists = array();

		$project_id = $app->getUserState('com_joomleagueproject');
		$projectteam_id = $app->getUserState('com_joomleagueprojectteam_id');

		$mdlProject = JModelLegacy::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		$mdlProjectteam = JModelLegacy::getInstance('projectteam','JoomleagueModel');
		$projectteam = $mdlProjectteam->getItem($projectteam_id);

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		// build the html select list for positions
		$selectedvalue = $this->item->project_position_id;
		$projectpositions = array();
		$projectpositions[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_FUNCTION'));
		if($res = $model->getProjectPositions())
		{
			$projectpositions = array_merge($projectpositions,$res);
		}
		$lists['projectpositions'] = JHtml::_('select.genericlist',$projectpositions,'project_position_id','size="1"','value','text',$selectedvalue);
		unset($projectpositions);

		$matchdays = JoomleagueHelper::getRoundsOptions($project->id,'ASC',false);

		$lists['injury_date']	 = JHtml::_('select.genericlist',$matchdays,'injury_date',
				'size="1"','value','text',$this->item->injury_date);
		$lists['injury_end']	= JHtml::_('select.genericlist',$matchdays,'injury_end',
				'size="1"','value','text',$this->item->injury_end );
		
		// suspension details
		$myoptions = array();
		$myoptions[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = JHtml::_('select.option','1',JText::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['suspension'] = JHtml::_('select.radiolist',$myoptions,'suspension','size="1"','value','text',$this->item->suspension);
		unset($myoptions);

		$lists['suspension_date'] = JHtml::_('select.genericlist',$matchdays,'suspension_date','size="1"','value','text',$this->item->suspension_date);
		$lists['suspension_end'] = JHtml::_('select.genericlist',$matchdays,'suspension_end','size="1"','value','text',$this->item->suspension_end);

		// away details
		$myoptions = array();
		$myoptions[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_NO'));
		$myoptions[] = JHtml::_('select.option','1',JText::_('COM_JOOMLEAGUE_GLOBAL_YES'));
		$lists['away'] = JHtml::_('select.radiolist',$myoptions,'away','size="1"','value','text',$this->item->away);
		unset($myoptions);

		$lists['away_date'] = JHtml::_('select.genericlist',$matchdays,'away_date','size="1"','value','text',$this->item->away_date);
		$lists['away_end'] = JHtml::_('select.genericlist',$matchdays,'away_end','size="1"','value','text',$this->item->away_end);

		//$extended = $this->getExtended($this->item->extended,'teamstaff');
		//$this->extended = $extended;

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
		$app = JFactory::getApplication();
		$jinput = $app->input;

		$jinput->set('hidemainmenu',true);
		$user = JFactory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);

		$params = JComponentHelper::getParams('com_joomleague');
		$name = JoomleagueHelper::formatName(null,$this->item->firstname,$this->item->nickname,$this->item->lastname,
				JoomleagueHelper::defaultNameFormat());
		$text = $isNew ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_ADMIN_TEAMSTAFF_TITLE') . ': ' . $name;
		JToolBarHelper::title($text);

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
		JToolBarHelper::help('screen.joomleague',true);
	}
}
