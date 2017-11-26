<?php
/**
 * Joomleague
 * @subpackage	Module-Calendar
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * Based upon Blog Calendar 1.2.2.1 
 * @author	Justo Gonzaled de Rivera
 * @license	GNU/GPL
 * 
 * Modified by Johncage for uw with Joomleague
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

JHtml::_('behavior.tooltip');
$ajax= JRequest::getVar('ajaxCalMod',0,'default','POST');
$ajaxmod= JRequest::getVar('ajaxmodid',0,'default','POST');
if(!$params->get('cal_start_date')){
	$year = JRequest::getVar('year',date('Y'));    /*if there is no date requested, use the current month*/
	$month  = JRequest::getVar('month',date('m'));
	$day  = JRequest::getVar('day',0);
}
else{
	$startDate= new JDate($params->get('cal_start_date'));
	$year = JRequest::getVar('year', $startDate->format('%Y'));
	$month  = JRequest::getVar('month', $startDate->format('%m'));
	$day  = $ajax? '' : JRequest::getVar('day', $startDate->format('%d'));
}
$helper = new modJLCalendarHelper;
$doc = JFactory::getDocument();
$lightbox    = $params->get('lightbox', 1);

JHtml::_('behavior.framework');
JHtml::_('behavior.modal');
if ($lightbox ==1 && (!isset($_GET['format']) OR ($_GET['format'] != 'pdf'))) {
	$doc->addScriptDeclaration(";
      window.addEvent('domready', function() {
          $$('a.jlcmodal".$module->id."').each(function(el) {
            el.addEvent('click', function(e) {
              new Event(e).stop();
              SqueezeBox.fromElement(el);
            });
          });
      });
      ");
}
$inject_container = ($params->get('inject', 0)==1)?$params->get('inject_container', 'joomleague'):'';
$doc->addScriptDeclaration(';
    jlcinjectcontainer['.$module->id.'] = \''.$inject_container.'\';
    jlcmodal['.$module->id.'] = \''.$lightbox.'\';
      ');

if (!defined('JLC_MODULESCRIPTLOADED')) {
	$doc->addScript( JUri::base().'modules/mod_joomleague_calendar/assets/js/mod_joomleague_calendar.js' );
	$doc->addScriptDeclaration(';
    var calendar_baseurl=\''. JUri::base() . '\';
      ');
	$doc->addStyleSheet(JUri::base().'modules/mod_joomleague_calendar/assets/css/mod_joomleague_calender.css');
	define('JLC_MODULESCRIPTLOADED', 1);
}
$calendar = $helper->showCal($params,$year,$month,$ajax,$module->id);

require JModuleHelper::getLayoutPath('mod_joomleague_calendar');
