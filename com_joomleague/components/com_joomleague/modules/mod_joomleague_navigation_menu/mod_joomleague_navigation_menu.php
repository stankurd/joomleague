<?php
/**
 * Joomleague
 * @subpackage	Module-NavigationMenu
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

HTMLHelper::_('behavior.framework');
$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_navigation_menu/css/mod_joomleague_navigation_menu.css');
$document->addScript(Uri::base().'modules/mod_joomleague_navigation_menu/js/mod_joomleague_navigation_menu.js');

$helper = new modJoomleagueNavigationMenuHelper($params);

$seasonselect	= $helper->getSeasonSelect();
$leagueselect	= $helper->getLeagueSelect();
$projectselect	= $helper->getProjectSelect();
$divisionselect = $helper->getDivisionSelect();
$teamselect		= $helper->getTeamSelect();

$defaultview   = $params->get('project_start');
$defaultitemid = $params->get('custom_item_id');
require ModuleHelper::getLayoutPath('mod_joomleague_navigation_menu', $params->get('layout', 'default'));
