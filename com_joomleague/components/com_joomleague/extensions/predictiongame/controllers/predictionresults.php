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
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueControllerPredictionResults extends JLGController
{

	function display()
	{
		$this->showprojectheading();
		$this->showbackbutton();
		$this->showfooter();
	}

	function selectprojectround()
	{
	    $app = Factory::getApplication();
		$app->input->checkToken() or jexit(JText::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$post	= $app->input->post->getArray();
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />'; die();
		$pID	= $app->input->getVar('prediction_id',	null,	'post',	'int');
		
		// diddipoeler
        $pggroup	= $app->input->getVar('pggroup',	null,	'post',	'int');
		$pjID	= $app->input->getVar('p',	null,	'post',	'int');
		
        $rID	= $app->input->getVar('r',				null,	'post',	'int');
		$link = PredictionHelperRoute::getPredictionResultsRoute($pID,$rID,$pjID,NULL,'',$pggroup);
		$this->setRedirect($link);
	}

}
?>