<?php
/**
 * Joomleague
 * @subpackage	Module-Teamplayers
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$list = modJLGTeamPlayersHelper::getData($params);

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_teamplayers/css/mod_joomleague_teamplayers.css');

require ModuleHelper::getLayoutPath('mod_joomleague_teamplayers', $params->get('layout', 'default'));