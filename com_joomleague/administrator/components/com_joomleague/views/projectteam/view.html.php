<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewProjectteam extends JLGView
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;

		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$project_id = $app->getUserState('com_joomleagueproject');
		$uri = Uri::getInstance();
		$user = Factory::getUser();

		$model = $this->getModel();
		$lists = array();

		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project = $mdlProject->getItem($project_id);

		// build the html select list for days of week
		if($trainingData = $model->getTrainingData($this->item->id))
		{
			$daysOfWeek = array(
					0 => Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT'),
					1 => Text::_('COM_JOOMLEAGUE_GLOBAL_MONDAY'),
					2 => Text::_('COM_JOOMLEAGUE_GLOBAL_TUESDAY'),
					3 => Text::_('COM_JOOMLEAGUE_GLOBAL_WEDNESDAY'),
					4 => Text::_('COM_JOOMLEAGUE_GLOBAL_THURSDAY'),
					5 => Text::_('COM_JOOMLEAGUE_GLOBAL_FRIDAY'),
					6 => Text::_('COM_JOOMLEAGUE_GLOBAL_SATURDAY'),
					7 => Text::_('COM_JOOMLEAGUE_GLOBAL_SUNDAY')
			);
			$dwOptions = array();
			foreach($daysOfWeek as $key=>$value)
			{
				$dwOptions[] = HTMLHelper::_('select.option',$key,$value);
			}
			foreach($trainingData as $td)
			{
				$lists['dayOfWeek'][$td->id] = HTMLHelper::_('select.genericlist',$dwOptions,'dw_' . $td->id,'class="input-medium"','value','text',
						$td->dayofweek);
			}
			unset($daysOfWeek);
			unset($dwOptions);
		}

		if($project->project_type == 'DIVISIONS_LEAGUE') // No divisions
		{
			// build the html options for divisions
			$division[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_DIVISION'));
			$mdlDivisions = BaseDatabaseModel::getInstance('divisions','JoomLeagueModel');
			if($res = $mdlDivisions->getDivisions($project_id))
			{
				$division = array_merge($division,$res);
			}
			$lists['divisions'] = $division;

			unset($res);
			unset($divisions);
		}

		$extended = $this->getExtended($this->item->extended,'projectteam');
		$this->extended = $extended;

		// $this->imageselect = $imageselect;
		$this->project = $project;
		$this->lists = $lists;
		$this->trainingData = $trainingData;

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_P_TEAM_TITLE').': '.$this->item->name);
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'projectteam.apply'],
		        ['save', 'projectteam.save'],
		    ],
		    'btn-success'
		    );
		//JLToolBarHelper::apply('projectteam.apply');
		//JLToolBarHelper::save('projectteam.save');
		JLToolBarHelper::cancel('projectteam.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
