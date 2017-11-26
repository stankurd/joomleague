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
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewPersons extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		//JHtml::_('behavior.calendar');

		$app = Factory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$params = ComponentHelper::getParams($option);

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// build the html select list for positions
		$positionsList = array();
		$positionsList[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_POSITION'));
		$positions = BaseDatabaseModel::getInstance('person','joomleaguemodel')->getPositions();
		if($positions)
		{
			$positions = array_merge($positionsList,$positions);
		}
		$lists['positions'] = $positions;
		unset($positionsList);

		// build the html options for nation
		$nations = array();
		$nations[] = JHtml::_('select.option','0',JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_NATION'));
		if($res = Countries::getCountryOptions())
		{
			$nations = array_merge($nations,$res);
		}
		$lists['nation'] = $nations;
		unset($nations);

		$this->config = Factory::getConfig();
		$this->lists = $lists;
		$this->component_params = $params;
		$this->params = $params;

		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		//JHtml::_('bootstrap.framework');

		$baseurl = Uri::root();
		$document = Factory::getDocument();
		$document->addScript($baseurl.'media/com_joomleague/bootstrap-editable/js/bootstrap-editable.js');
		$document->addStyleSheet($baseurl.'media/com_joomleague/bootstrap-editable/css/bootstrap-editable.css');

		$this->addToolbar();
		parent::display($tpl);
	}


	/**
	 * Displays a calendar control field with optional onupdate js handler
	 *
	 * @param	string The date value
	 * @param	string The name of the text field
	 * @param	string The id of the text field
	 * @param	string The date format
	 * @param	string js function to call on date update
	 * @param	array Additional html attributes
	 */
	function calendar($value,$name,$id,$format = '%Y-%m-%d',$attribs = null,$onUpdate = null,$i = null)
	{
		if(is_array($attribs))
		{
			$attribs = ArrayHelper::toString($attribs);
		}
		$document = Factory::getDocument();
		$document->addScriptDeclaration(
				'window.addEvent(\'domready\',function() {Calendar.setup({
	        inputField     :    "' . $id . '",    // id of the input field
	        ifFormat       :    "' . $format . '",     // format of the input field
	        button         :    "' . $id . '_img", // trigger for the calendar (button ID)
	        align          :    "Tl",          // alignment (defaults to "Bl")
	        onUpdate       :    ' . ($onUpdate ? $onUpdate : 'null') . ',
	        singleClick    :    true
    	});});');

		$html[] = "<div class='input-append'";
		$html[] = '<input class="input-small" onchange="document.getElementById(\'cb' . $i . '\').checked=true" type="text" name="' . $name . '" id="' .
				 $id . '" value="' . htmlspecialchars($value,ENT_COMPAT,'UTF-8') . '" />' . $html[] = '<button type="button" class="btn" alt="" id="' . $id . '_img" /><span class="icon-calendar"></span></button>';
		$html[] = "</div>";
		return implode("\n",$html);
	}


	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
		// Set toolbar items for the page
		JLToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_PERSONS_TITLE'),'user');
		JLToolBarHelper::addNew('person.add');
		JLToolBarHelper::publishList('persons.publish');
		JLToolBarHelper::unpublishList('persons.unpublish');
		JLToolBarHelper::divider();
		JLToolBarHelper::apply('persons.saveshort');
		JLToolBarHelper::custom('persons.import','upload','upload','COM_JOOMLEAGUE_GLOBAL_CSV_IMPORT',false);
		JLToolBarHelper::archiveList('persons.export','COM_JOOMLEAGUE_GLOBAL_XML_EXPORT');
		JLToolBarHelper::deleteList('COM_JOOMLEAGUE_ADMIN_PERSONS_DELETE_WARNING','persons.remove');
		JLToolBarHelper::divider();
		JLToolBarHelper::help('screen.joomleague',true);
	}
}
