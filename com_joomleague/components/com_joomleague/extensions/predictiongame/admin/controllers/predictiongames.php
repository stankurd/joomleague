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


// Check to ensure this file is included in Joomla!
use Joomla\CMS\MVC\Controller\AdminController;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');
require_once(JPATH_COMPONENT.'/controllers/joomleague.php');

/**
 * Joomleague PredictionGames Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */

 

class JoomleagueControllerpredictionGames extends JLGControllerAdmin
{
  
   
    /**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'predictiongame', $prefix = 'joomleaguetModel', $config = Array() ) 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}