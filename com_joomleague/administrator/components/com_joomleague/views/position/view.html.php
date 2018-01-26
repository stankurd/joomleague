<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;
HTMLHelper::_('jquery.framework');
HTMLHelper::_('behavior.core');
/**
 * HTML View class
 */
class JoomleagueViewPosition extends JLGView
{

	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		
		$pk = $this->item->id;
		if(is_null($pk))
		{
			$pk = 0;
		}
		$uri = Uri::getInstance();
		$baseurl = Uri::root();
		$model = $this->getModel();
		$lists = array();
		
		$document = Factory::getDocument();
		$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/multiselect.js');
		
		// build the html select list for events
		$res = array();
		$res1 = array();
		$notusedevents = array();
		if($res = $model->getEventsPosition($pk))//prawa stroma
		{
			 $lists['position_events'] = HTMLHelper::_('select.genericlist',$res,'position_eventslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . max(10,count($res)) . '"','value','text',false,
					'multiselect_to');
		}
		else
		{
			$lists['position_events'] = '<select name="position_eventslist[]" id="multiselect_to" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		
		$res1 = $model->getEvents($pk);
		if($res = $model->getEventsPosition($pk))
		{
			if($res1 != "")
				foreach($res1 as $miores1)
				{
					$used = 0;
					foreach($res as $miores)
					{
						if($miores1->text == $miores->text)
						{
							$used = 1;
						}
					}
					if($used == 0)
					{
						$notusedevents[] = $miores1;
					}
				}
		}
		else
		{
			$notusedevents = $res1;
		}
		
		// build the html select list for events
		if(($notusedevents) && (count($notusedevents) > 0))//lewa strona
		{
			$lists['events'] = HTMLHelper::_('select.genericlist',$notusedevents,'eventslist[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . max(10,count($notusedevents)) . '"','value','text',
					false,'multiselect');
		}
		else
		{
			$lists['events'] = '<select name="eventslist[]" id="multiselect" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		unset($res);
		unset($res1);
		unset($notusedevents);
		
		// position statistics
		$position_stats = $model->getPositionStatsOptions($pk);
		
		if(! empty($position_stats))
		{
			$lists['position_statistic'] = HTMLHelper::_('select.genericlist',$position_stats,'position_statistic[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . max(10,count($position_stats)) . '"','value',
					'text',false,'multiselect2_to');
		}
		else
		{
			$lists['position_statistic'] = '<select name="position_statistic[]" id="multiselect2_to" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		
		$available_stats = $model->getAvailablePositionStatsOptions($pk);
		
		if(! empty($available_stats))
		{
			$lists['statistic'] = HTMLHelper::_('select.genericlist',$available_stats,'statistic[]',
					' style="width:250px; height:300px;" class="inputbox" multiple="true" size="' . max(10,count($available_stats)) . '"','value',
					'text',false,'multiselect2');
		}
		else
		{
			$lists['statistic'] = '<select name="statistic[]" id="multiselect2" style="width:250px; height:300px;" class="inputbox" multiple="true" size="10"></select>';
		}
		// build the html select list for parent positions
		$parents[] = HTMLHelper::_('select.option','',Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_IS_P_POSITION'));
		if($res = $model->getParentsPositions($pk))
		{
			$parents = array_merge($parents,$res);
		}
		$lists['parents'] = HTMLHelper::_('select.genericlist',$parents,'parent_id','class="inputbox" size="1"','value','text',$this->item->parent_id);
		unset($parents);
		$this->lists = $lists;
		
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
		
		// Set toolbar items for the page
		$text = $isNew ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		
		if($isNew)
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_ADD_NEW'),'jl-Positions');
			JLToolBarHelper::apply('position.apply');
			JLToolBarHelper::save('position.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('position.cancel');
		}
		else
		{
			JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_POSITION_EDIT'),'jl-Positions');
			JLToolBarHelper::apply('position.apply');
			JLToolBarHelper::save('position.save');
			JLToolBarHelper::divider();
			JLToolBarHelper::cancel('position.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
