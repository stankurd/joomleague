<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * HTML View class
 *
 * @author	Kurt Norgaz
 */
class JoomleagueViewJLXMLImports extends JLGView
{
	/**
	 * The list of available timezone groups to use.
	 *
	 * @var    array
	 */
	protected static $zones = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
	
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$option 	= $app->input->get('option');
		
		if ($this->getLayout()=='form')
		{
			$this->_displayForm($tpl);
			return;
		}

		if ($this->getLayout()=='info')
		{
			$this->_displayInfo($tpl);
			return;
		}

		if ($this->getLayout()=='selectpage')
		{
			$this->_displaySelectpage($tpl);
			return;
		}

		// Set toolbar items for the page
		ToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_1_3'),'generic.png');
		ToolBarHelper::help('screen.joomleague',true);

		$uri 	= Uri::getInstance();
		$config = ComponentHelper::getParams('com_media');
		$post	= $app->input->post->getArray();
		$files	= $app->input->get('files');

		$this->request_url = $uri->toString();
		$this->config = $config;

		parent::display($tpl);
	}

	private function _displayForm($tpl)
	{
		$mtime			= microtime();
		$mtime 			= explode(" ",$mtime);
		$mtime			= $mtime[1] + $mtime[0];
		$starttime		= $mtime;
		$option			= 'com_joomleague';
		$app		    = Factory::getApplication();
		$document		= Factory::getDocument();
		$db				= Factory::getDbo();
		$uri			= Uri::getInstance();
		$model			= BaseDatabaseModel::getInstance('jlxmlimport', 'joomleaguemodel');
		$data			= $model->getData();
		$uploadArray	= $app->getUserState($option.'uploadArray',array());
		$tzValue  		= isset($data['project']->timezone) ? $data['project']->timezone: null;
		$zones = DateTimeZone::listIdentifiers();				
		$options = array();
		$options[]	= HTMLHelper::_('select.option', '', '- '.Text::_( 'SELECT_TIMEZONE' ).' -');
		foreach ($zones as $zone) {
			if (strpos($zone,"/")===false && strpos($zone,"UTC")===false)  continue;
			if (strpos($zone,"Etc")===0) continue;
			$options[]	= HTMLHelper::_('select.option', $zone, $zone);
		}
		$lists['timezone']= HTMLHelper::_('select.genericlist', $options, 'timezone', ' class="inputbox"', 'value', 'text', $tzValue);
		// build the html select booleanlist for published
		$publishedValue  		= isset($data['project']->published) ? $data['project']->published: null;
		$lists['published']=HTMLHelper::_('select.booleanlist','published',' ',$publishedValue);
		
		$countries=new Countries();
		$this->uploadArray=$uploadArray;
		$this->starttime=$starttime;
		$this->countries=$countries->getCountries();
		$this->request_url=$uri->toString();
		$this->xml=$data;
		$this->leagues=$model->getLeagueList();
		$this->seasons=$model->getSeasonList();
		$this->sportstypes=$model->getSportsTypeList();
		$this->admins=$model->getUserList(true);
		$this->editors=$model->getUserList(false);
		$this->templates=$model->getTemplateList();
		$this->teams=$model->getTeamList();
		$this->clubs=$model->getClubList();
		$this->events=$model->getEventList();
		$this->positions=$model->getPositionList();
		$this->parentpositions=$model->getParentPositionList();
		$this->playgrounds=$model->getPlaygroundList();
		$this->persons=$model->getPersonList();
		$this->statistics=$model->getStatisticList();
		$this->OldCountries=$model->getCountryByOldid();
		$this->import_version=$model->import_version;
		$this->lists=$lists;
		
		// Set toolbar items for the page
		ToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_2_3'),'generic.png');
		//                       task    image  mouseover_img           alt_text_for_image              check_that_standard_list_item_is_checked
		ToolbarHelper::custom('jlxmlimport.insert','upload','upload',Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_START_BUTTON'), false); // --> bij clicken op import wordt de insert view geactiveerd
		ToolBarHelper::back();
		ToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}

	private function _displayInfo($tpl)
	{
		$app 		= Factory::getApplication();
		$mtime 		= microtime();
		$mtime		= explode(" ",$mtime);
		$mtime		= $mtime[1] + $mtime[0];
		$starttime	= $mtime;
		$model 		= BaseDatabaseModel::getInstance('jlxmlimport', 'JoomleagueModel');
		$post		= $app->input->post->getArray();
		
		// Set toolbar items for the page
		ToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_3_3'),'generic.png');
		//JLToolBarHelper::back();
		ToolBarHelper::help('screen.joomleague',true);

		$this->starttime=$starttime;
		$this->importData=$model->importData($post);
		$this->postData=$post;

		parent::display($tpl);
	}

	private function _displaySelectpage($tpl)
	{
		$app 	    = Factory::getApplication();
		$option 	= $app->input->get('option');
		$document 	= Factory::getDocument();
		$db 		= Factory::getDbo();
		$uri 		= Uri::getInstance();
		$model 		= BaseDatabaseModel::getInstance('JLXMLImport', 'JoomleagueModel');
		$lists 		= array();

		$this->request_url=$uri->toString();
		$this->selectType=$app->getUserState($option.'selectType');
		$this->recordID=$app->getUserState($option.'recordID');

		switch ($this->selectType)
		{
			case '10':   { // Select new Club
						$this->clubs=$model->getNewClubListSelect();
						$clublist=array();
						$clublist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=HTMLHelper::_(	'select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertNewClub(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '9':   { // Select Club & Team
						$this->clubsteams=$model->getClubAndTeamListSelect();
						$clubteamlist=array();
						$clubteamlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB_AND_TEAM'));
						$clubteamlist=array_merge($clubteamlist,$this->clubsteams);
						$lists['clubsteams']=HTMLHelper::_(	'select.genericlist',$clubteamlist,'teamID','class="inputbox select-team" onchange="javascript:insertClubAndTeam(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '8':	{ // Select Statistics
						$this->statistics=$model->getStatisticListSelect();
						$statisticlist=array();
						$statisticlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_STATISTIC'));
						$statisticlist=array_merge($statisticlist,$this->statistics);
						$lists['statistics']=HTMLHelper::_('select.genericlist',$statisticlist,'statisticID','class="inputbox select-statistic" onchange="javascript:insertStatistic(\''.$this->recordID.'\')" ');
						unset($statisticlist);
						}
						break;

			case '7':	{ // Select ParentPosition
						$this->parentpositions=$model->getParentPositionListSelect();
						$parentpositionlist=array();
						$parentpositionlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PARENT_POSITION'));
						$parentpositionlist=array_merge($parentpositionlist,$this->parentpositions);
						$lists['parentpositions']=HTMLHelper::_('select.genericlist',$parentpositionlist,'parentPositionID','class="inputbox select-parentposition" onchange="javascript:insertParentPosition(\''.$this->recordID.'\')" ');
						unset($parentpositionlist);
						}
						break;

			case '6':	{ // Select Position
						$this->positions=$model->getPositionListSelect();
						$positionlist=array();
						$positionlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_POSITION'));
						$positionlist=array_merge($positionlist,$this->positions);
						$lists['positions']=HTMLHelper::_('select.genericlist',$positionlist,'positionID','class="inputbox select-position" onchange="javascript:insertPosition(\''.$this->recordID.'\')" ');
						unset($positionlist);
						}
						break;

			case '5':	{ // Select Event
						$this->events=$model->getEventListSelect();
						$eventlist=array();
						$eventlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_EVENT'));
						$eventlist=array_merge($eventlist,$this->events);
						$lists['events']=HTMLHelper::_('select.genericlist',$eventlist,'eventID','class="inputbox select-event" onchange="javascript:insertEvent(\''.$this->recordID.'\')" ');
						unset($eventlist);
						}
						break;

			case '4':	{ // Select Playground
						$this->playgrounds=$model->getPlaygroundListSelect();
						$playgroundlist=array();
						$playgroundlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PLAYGROUND'));
						$playgroundlist=array_merge($playgroundlist,$this->playgrounds);
						$lists['playgrounds']=HTMLHelper::_('select.genericlist',$playgroundlist,'playgroundID','class="inputbox select-playground" onchange="javascript:insertPlayground(\''.$this->recordID.'\')" ');
						unset($playgroundlist);
						}
						break;

			case '3':	{ // Select Person
						$this->persons=$model->getPersonListSelect();
						$personlist=array();
						$personlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PERSON'));
						$personlist=array_merge($personlist,$this->persons);
						$lists['persons']=HTMLHelper::_('select.genericlist',$personlist,'personID','class="inputbox select-person" onchange="javascript:insertPerson(\''.$this->recordID.'\')" ');
						unset($personlist);
						}
						break;

			case '2':	{ // Select Club
						$this->clubs=$model->getClubListSelect();
						$clublist=array();
						$clublist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=HTMLHelper::_('select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertClub(\''.$this->recordID.'\')" ');
						unset($clublist);
						}
						break;

			case '1':
			default:	{ // Select Team
						$this->teams=$model->getTeamListSelect();
						$this->clubs=$model->getClubListSelect();
						$teamlist=array();
						$teamlist[]=HTMLHelper::_('select.option',0,Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_TEAM'));
						$teamlist=array_merge($teamlist,$this->teams);
						$lists['teams']=HTMLHelper::_('select.genericlist',$teamlist,'teamID','class="inputbox select-team" onchange="javascript:insertTeam(\''.$this->recordID.'\')" ','value','text',0);
						unset($teamlist);
						}
						break;
		}

		$this->lists=$lists;
		// Set page title
		$pageTitle=Text::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ASSIGN_TITLE');
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}
}
