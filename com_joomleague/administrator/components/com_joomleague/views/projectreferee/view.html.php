<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * HTML View class
 */
class JoomleagueViewProjectReferee extends JLGView
{

	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$model = $this->getModel();
		$project_id = $app->getUserState('com_joomleagueproject');
		
		$app = Factory::getApplication();
		$jinput = $app->input;
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$lists = array();
		// get the projectreferee data of the project_team
		$isNew = ($this->item->id < 1);
		
		// fail if checked out not by 'me'
		// @todo: fix
		/*
		 * if ($model->isCheckedOut ( $user->get ( 'id' ) )) {
		 * $msg = Text::sprintf ( 'DESCBEINGEDITTED', Text::_ (
		 * 'COM_JOOMLEAGUE_ADMIN_P_REF_THE_PREF' ), $this->item->name );
		 * $app->redirect ( 'index.php?option=com_joomleague', $msg );
		 * }
		 */
		
		// Edit or Create?
		if($isNew)
		{
			$this->item->order = 0;
		}
		
		// build the html select list for positions
		$refereepositions = array();
		$refereepositions[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_REF_POS'));
		if($res = $model->getRefereePositions())
		{
			$refereepositions = array_merge($refereepositions,$res);
		}
		$lists['refereepositions'] = HTMLHelper::_('select.genericlist',$refereepositions,'project_position_id','class="inputbox" size="1"','value','text',
				$this->item->project_position_id);
		unset($refereepositions);
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);
		
		$this->project = $project;
		$this->lists = $lists;
		
		$extended = $this->getExtended($this->item->extended,'projectreferee');
		$this->extended = $extended;
		
		$this->addToolbar();
		parent::display($tpl);
	}
	

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu',true);
		$user = Factory::getUser();
		$userId = $user->get('id');
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		
		JLToolBarHelper::title(Text::_('Edit referee data'),'jl-Referees');
		
		// Set toolbar items for the page
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		
		if($isNew)
		{
			JLToolBarHelper::apply('projectreferee.apply');
			JLToolBarHelper::save('projectreferee.save');
			JLToolBarHelper::cancel('projectreferee.cancel');
		}
		else
		{
			JLToolBarHelper::apply('projectreferee.apply');
			JLToolBarHelper::save('projectreferee.save');
			JLToolBarHelper::cancel('projectreferee.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
