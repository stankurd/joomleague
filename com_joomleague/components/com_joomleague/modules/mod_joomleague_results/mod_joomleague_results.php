<?php
/**
 * Joomleague
 * @subpackage	Module-Results
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

$list = modJLGResultsHelper::getData($params);

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_results/assets/css/mod_joomleague_results.css');
// add the js script
$baseurl = Uri::root();
$document->addScript($baseurl . 'administrator/components/com_joomleague/assets/js/depend.js');
require ModuleHelper::getLayoutPath('mod_joomleague_results', $params->get('layout', 'default'));