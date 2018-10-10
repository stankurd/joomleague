<?php
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

/**
 * this file perform the basic init and includes for joomleague
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

if (version_compare(phpversion(), '7.0.0', '<')===true) {
	echo  '<div style="font:12px/1.35em arial, helvetica, sans-serif;"><div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;"><h3 style="margin:0; font-size:1.7em; font-weight:normal; text-transform:none; text-align:left; color:#2f2f2f;">'.JText::_("COM_JOOMLEAGUE_INVALID_PHP1").'</h3></div>'.JText::_("COM_JOOMLEAGUE_INVALID_PHP2").'</div>';
	return;
}

if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
define('JLG_PATH_SITE',  JPATH_SITE.'/components/com_joomleague');
define('JLG_PATH_ADMIN', JPATH_SITE.'/administrator/components/com_joomleague');
require_once JLG_PATH_ADMIN.'/defines.php';

require_once JLG_PATH_SITE.'/assets/classes/jlgcontroller.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgcontrolleradmin.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgmodel.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgview.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jllanguage.php' ;

require_once JLG_PATH_SITE.'/helpers/route.php';
require_once JLG_PATH_SITE.'/helpers/countries.php';
require_once JLG_PATH_SITE.'/helpers/extraparams.php';
require_once JLG_PATH_SITE.'/helpers/joomleague.php';
require_once JLG_PATH_SITE.'/helpers/ranking.php';
require_once JLG_PATH_SITE.'/helpers/html.php';

require_once JLG_PATH_ADMIN.'/helpers/joomleaguehelper.php';
require_once JLG_PATH_ADMIN.'/tables/jltable.php';
JLoader::register('JoomleagueHelper', JLG_PATH_ADMIN.'/helpers/joomleaguehelper.php');

Table::addIncludePath(JLG_PATH_ADMIN.'/tables');

require_once JLG_PATH_ADMIN.'/helpers/plugins.php';

$app = Factory::getApplication();
$task = $app->input->get('task');
$option = $app->input->get('option');
if($task != '' && $option == 'com_joomleague')  {
	
	if (!Factory::getUser()->authorise($task, 'com_joomleague')) {
		// @todo: should this be visible for the normal user? if not then it can be changed
		// display the task which is not handled by the access.xml
		
		$ignoreTask = array(
			'clubform.cancel','clubform.edit','clubform.save',
			'projectteamform.cancel','projectteamform.edit','projectteamform.save'	
		);
		
		if (in_array($task, $ignoreTask)) {
			// pass as they are handled by the controller
		} else {
		    $app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'warning');
		}
	}
}
