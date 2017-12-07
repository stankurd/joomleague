<?php
/**
 * Joomleague
 * @subpackage	Module-Teamstaffs
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Helper\ModuleHelper;

defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$list = modJLGTeamStaffsHelper::getData($params);

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_teamstaffs/css/mod_joomleague_teamstaffs.css');

require ModuleHelper::getLayoutPath('mod_joomleague_teamstaffs', $params->get('layout', 'default'));