<?php
/**
 * Joomleague
 * @subpackage	Module-SportstypeStatistics
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$sportstypes = $params->get('sportstypes');
$data = modJLGSportsHelper::getData($params);

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'modules/mod_joomleague_sports_type_statistics/css/mod_joomleague_sports_type_statistics.css');

// language file
$lang = JFactory::getLanguage();
$lang->load('com_joomleague');

require JModuleHelper::getLayoutPath('mod_joomleague_sports_type_statistics');