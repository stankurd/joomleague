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
use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
//require_once dirname(__FILE__).'/connectors/joomleague.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$app = Factory::getApplication();
$input = $app->input;
$document = Factory::getDocument();
// add the js script
$baseurl = Uri::root();
$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/depend.js');
HTMLHelper::_('bootstrap.tooltip');
$ajax= $input->post->get('ajaxCalMod',0,'default');
$ajaxmod= $input->post->get('ajaxmodid',0,'default');
if(!$params->get('cal_start_date')){
	$year = $input->get('year',date('Y'));    /*if there is no date requested, use the current month*/
	$month  = $input->get('month',date('m'));
	$day  = $input->get('day',0);
}
else{
	$startDate= new Date($params->get('cal_start_date'));
	$year = $input->get('year', $startDate->format('%Y'));
	$month  = $input->get('month', $startDate->format('%m'));
	$day  = $ajax? '' : $input->get('day', $startDate->format('%d'));
}
$helper = new modJLCalendarHelper;
$doc = Factory::getDocument();
$lightbox    = $params->get('lightbox', 1);

HTMLHelper::_('behavior.framework');
HTMLHelper::_('behavior.modal');
if ($lightbox ==1 && (!isset($_GET['format']) OR ($_GET['format'] != 'pdf'))) {
	$doc->addScriptDeclaration(";
        jQuery(function($) {
          jQuery('a.jlcmodal".$module->id."').each(function(el) {
            el.click, (function(e) {
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
	$doc->addScript( Uri::base().'modules/mod_joomleague_calendar/assets/js/mod_joomleague_calendar.js' );
	$doc->addScriptDeclaration(';
    var calendar_baseurl=\''. Uri::base() . '\';
      ');
	$doc->addStyleSheet(Uri::base().'modules/mod_joomleague_calendar/assets/css/mod_joomleague_calender.css');
	define('JLC_MODULESCRIPTLOADED', 1);
}
$calendar = $helper->showCal($params,$year,$month,$ajax,$module->id);

require ModuleHelper::getLayoutPath('mod_joomleague_calendar', $params->get('layout', 'default'));
