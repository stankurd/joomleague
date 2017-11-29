<?php
/**
 * Joomleague
 * @subpackage	Module-Logo
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/helper.php';
require_once JPATH_SITE.'/components/com_joomleague/joomleague.core.php';

$p = $params->get('p');
$list = modJLGLogoHelper::getData($params);

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'modules/mod_joomleague_logo/css/mod_joomleague_logo.css');

require JModuleHelper::getLayoutPath('mod_joomleague_logo');