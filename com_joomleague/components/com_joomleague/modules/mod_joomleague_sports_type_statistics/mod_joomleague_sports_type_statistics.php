<?php
/**
 * Joomleague
 * @subpackage	Module-SportstypeStatistics
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

$sportstypes = $params->get('sportstypes');
$data = modJLGSportsHelper::getData($params);

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_sports_type_statistics/css/mod_joomleague_sports_type_statistics.css');

// language file
$lang = Factory::getLanguage();
$lang->load('com_joomleague');

require ModuleHelper::getLayoutPath('mod_joomleague_sports_type_statistics', $params->get('layout', 'default'));