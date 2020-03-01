<?php
use Joomla\CMS\MVC\Controller\AdminController;

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
 

class JoomleagueControllerpredictiontemplates extends AdminController
{
  

    
    /**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'predictiontemplate', $prefix = 'JoomleagueModel', $config = Array() ) 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
}