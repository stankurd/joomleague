<?php
/**
 * Joomleague
 * @subpackage	Module-Ticker
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;
//HTMLHelper::_('behavior.framework'); // mootools
HTMLHelper::_('jquery.framework');

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_ticker/css/mod_joomleague_ticker.css');

$mode 			= $params->def("mode");
$results 		= $params->get('results');
$round 			= $params->get('round');
$ordering 		= $params->get('ordering');
$matchstatus	= $params->get('matchstatus');
$selectiondate 	= modJoomleagueTickerHelper::getSelectionDate($params->get('daysback'), $params->get('timezone', 'Europe/Warsaw'));
$bUseFav 		= $params->get('usefavteams');
$matches 		= modJoomleagueTickerHelper::getMatches($results, $params->get('p'), $params->get('teamid'), $selectiondate, $ordering, $round, $matchstatus,$bUseFav);

if(empty($matches) || count($matches) == 0)
{
	echo JText::_("MOD_JOOMLEAGUE_TICKER_NOMATCHES");
	return;
} else {
	$timezone 	= new DateTimeZone($params->get('timezone'));
	$utc 		= new DateTime();
	$offset 	= $timezone->getOffset($utc);
	$date 		= modJoomleagueTickerHelper::getCorrectDateFormat($params->get('dateformat'), $matches, $offset, $params->get('timezone'));
	if (count($matches)<$results)
	{
		$results=count($matches);
	}

	$tickerpause = $params->def("tickerpause");
	$scrollspeed = $params->def("scrollspeed");
	$scrollpause = $params->def("scrollpause");
	
	switch ($mode)
	{
		case 'T':
			include dirname(__FILE__).'/js/ticker.js';
			break;
		case 'V':
			include dirname(__FILE__).'/js/qscrollerv.js';
			$document->addScript(Uri::base().'modules/mod_joomleague_ticker/js/qscroller.js');
			break;
		case 'H':
			$document->addScript(Uri::base().'modules/mod_joomleague_ticker/js/qscrollerh.js');
			$document->addScript(Uri::base().'modules/mod_joomleague_ticker/js/qscroller.js');
			break;
	}
}
require ModuleHelper::getLayoutPath('mod_joomleague_ticker');
