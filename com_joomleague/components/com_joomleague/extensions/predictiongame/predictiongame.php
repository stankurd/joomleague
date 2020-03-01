<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\Table\Table;

defined('_JEXEC') or die( 'Restricted access' );
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}
JLoader::register('JoomleagueTable',JLG_PATH_ADMIN.'/administrator/components/com_joomleague/tables/jltables.php');
JLoader::register('JoomleagueHelper',JPATH_SITE.'/administrator/components/com_joomleague/helpers/joomleague.php');
define('JLG_PATH_EXTENSION_PREDICTIONGAME',  JPATH_SITE.'/components/com_joomleague/extensions/predictiongame');
Table::addIncludePath(JLG_PATH_EXTENSION_PREDICTIONGAME.'/admin/tables');
require_once JLG_PATH_EXTENSION_PREDICTIONGAME . '/helpers/route.php' ;
require_once JPATH_ROOT.'/components/com_joomleague/joomleague.core.php';
require_once JLG_PATH_SITE.'/assets/classes/jlgcontroller.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgcontrolleradmin.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgmodel.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jlgview.php' ;
require_once JLG_PATH_SITE.'/assets/classes/jllanguage.php' ;

?>
