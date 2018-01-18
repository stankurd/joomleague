<?php
/**
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_joomleague/joomleague.core.php';
require_once JPATH_COMPONENT.'/controller.php';

require_once JLG_PATH_SITE.'/helpers/extensioncontroller.php';
require_once JLG_PATH_ADMIN.'/controllers/jlgform.php';
require_once JLG_PATH_ADMIN.'/models/jlgitem.php';
require_once JLG_PATH_ADMIN.'/models/jlglist.php';

// Component Helper
jimport('joomla.application.component.helper');

//load classes
JLoader::registerPrefix('Joomleague', JPATH_COMPONENT);

//Load plugins
PluginHelper::importPlugin('Joomleague');

//application
$app = Factory::getApplication();

// Get an instance of the controller prefixed by Joomleague
$controller = JLGController::getInstance('Joomleague');

// Perform the Request task
$input = $app->input;
$controller->execute($input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
