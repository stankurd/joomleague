<?php
/**
 * Joomleague
 * @subpackage	Module-Playgroundplan
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

$list = modJLGPlaygroundplanHelper::getData($params);

if (empty($list)) {
	return;
}

$document = Factory::getDocument();
$document->addStyleSheet(Uri::base().'modules/mod_joomleague_playgroundplan/css/mod_joomleague_playgroundplan.css');

$mode = $params->def("mode");

switch ($mode)
	{
	case 0:
		$document->addScript(Uri::base().'modules/mod_joomleague_playgroundplan/js/qscroller.js');
		require_once dirname(__FILE__).'/js/ticker.js';
		break;
	case 1:
		break;
}

require ModuleHelper::getLayoutPath('mod_joomleague_playgroundplan', $params->get('layout', 'default'));