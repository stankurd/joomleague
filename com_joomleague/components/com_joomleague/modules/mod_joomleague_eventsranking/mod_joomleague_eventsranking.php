<?php
/**
 * Joomleague
 * @subpackage	Module-Eventsranking
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

// Load the standard events from the component language file
$language = Factory::getLanguage();
$language->load('com_joomleague', JPATH_SITE);

$list = modJLGEventsrankingHelper::getData($params);

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_eventsranking/css/mod_joomleague_eventsranking.css');

require ModuleHelper::getLayoutPath('mod_joomleague_eventsranking', $params->get('layout', 'default'));