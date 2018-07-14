<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;
//JHtml::_('behavior.tabstate');

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_joomleague')) {
		throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
}

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);


require_once JPATH_ROOT.'/components/com_joomleague/joomleague.core.php';
// Require the base controller
require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_COMPONENT.'/helpers/jlparameter.php';
require_once JLG_PATH_ADMIN.'/helpers/jltoolbarhelper.php';
require_once JLG_PATH_ADMIN.'/controllers/jlgform.php';
require_once JLG_PATH_ADMIN.'/models/jlgitem.php';
require_once JLG_PATH_ADMIN.'/models/jlglist.php';
require_once JLG_PATH_SITE.'/helpers/extensioncontroller.php';

//load classes
//JLoader::registerPrefix('Joomleague', JPATH_COMPONENT_ADMINISTRATOR);
//Load plugins
PluginHelper::importPlugin('Joomleague');

//Load styles and javascripts
//JoomleagueHelpersStyle::load();

//application
$app = Factory::getApplication();
$input = $app->input;

$filter = InputFilter::getInstance();
$task = 'display';
$command  = $input->get('task', 'display');
if (is_array($command))
{
	$command = $filter->clean(array_pop(array_keys($command)), 'cmd');
}
else
{
	$command = $filter->clean($command, 'cmd');
}
if (strpos($command, '.') !== false)
{
	list ($type, $task) = explode('.', $command);
}
$controller	= JLGController::getInstance('joomleague');
$controller->execute($task);
$controller->redirect();
