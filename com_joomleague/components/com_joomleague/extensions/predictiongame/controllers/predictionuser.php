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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Controller\FormController;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
//class JoomleagueControllerPredictionUsers extends JLGController
class JoomleagueControllerPredictionUsers extends FormController
{

	function display()
	{
		$this->showprojectheading();
		$this->showbackbutton();
		$this->showfooter();
	}

	function cancel()
	{
		Factory::getApplication()->redirect(str_ireplace('&layout=edit','',Uri::getInstance()->toString()));
	}

	function select()
	{
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_PRED_INVALID_TOKEN_REFUSED'));
		$pID	= $app->input->getVar('prediction_id','','post',	'int');
		$uID	= $app->input->getVar('uid',null,	'post',	'int');
		if (empty($uID)){$uID=null;}
		$link = PredictionHelperRoute::getPredictionMemberRoute($pID,$uID);
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

	function savememberdata()
	{
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_PRED_USERS_INVALID_TOKEN_MEMBER_NOT_SAVED'));
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$optiontext = strtoupper($app->input->getCmd('option').'_');
		$document = Factory::getDocument();
        
		$msg	= '';
		$link	= '';

		$post	= $app->input->post->getArray();
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$predictionGameID	= $app->input->post->getVar('prediction_id','','int');
		$joomlaUserID		= $app->input->post->getVar('user_id','','int');

		$model			= $this->getModel('predictionusers');
		$user			= Factory::getUser();
		$isMember		= $model->checkPredictionMembership();
		$allowedAdmin	= $model->getAllowed();

		if ( ( ( $user->id != $joomlaUserID ) ) && ( !$allowedAdmin ) )
		{
			$msg .= Text::_('COM_JOOMLEAGUE_PRED_USERS_CONTROLLER_ERROR_1');
			$link = Uri::getInstance()->toString();
		}
		else
		{
			if ((!$isMember) && (!$allowedAdmin))
			{
				$msg .= Text::_('COM_JOOMLEAGUE_PRED_USERS_CONTROLLER_ERROR_2');
				$link = Uri::getInstance()->toString();
			}
			else
			{
				if (!$model->savememberdata())
				{
					$msg .= Text::_('COM_JOOMLEAGUE_PRED_USERS_CONTROLLER_ERROR_3');
					$link = Uri::getInstance()->toString();
				}
				else
				{
					$msg .= Text::_('COM_JOOMLEAGUE_PRED_USERS_CONTROLLER_MSG_1');
					$link = Uri::getInstance()->toString();
				}
			}
		}

		echo '<br />';
		//echo '' . $link . '<br />';
		//echo '' . $msg . '<br />';
		$this->setRedirect($link,$msg);
	}
/*
	function selectprojectround()
	{
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_PRED_INVALID_TOKEN_REFUSED'));
		$app = Factory::getApplication();
		$post	= $app->input->post->getArray();
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$pID	= $app->input->getVar('prediction_id',	'',	'post',	'int');
		$pjID	= $app->input->getVar('project_id',	'',	'post',	'int');
		//$rID	= $app->input->getVar('round_id',		'',	'post',	'int');
		$uID	= $app->input->getVar('uid',		0,	'post',	'int');
		$set_pj	= $app->input->getVar('set_pj',		'',	'post',	'int');
		$set_r	= $app->input->getVar('set_r',	'',	'post',	'int');
		if ($set_r!=$rID){$rID=$set_r;}
		if ($set_pj!=$pjID){$pjID=$set_pj;}
		if (empty($pjID)){$pjID=null;}
		if (empty($uID)){$uID=null;}
		//$link = JoomleagueHelperRoute::getPredictionResultsRoute($pID,$rID,$pjID,'#jl_top');
		$link = PredictionHelperRoute::getPredictionMemberRoute($pID,$uID,null,$pjID);
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}*/
	function selectprojectround()
	{
	    Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));
	    // Reference global application object
	    $app = Factory::getApplication();
	    // JInput object
	    $jinput = $app->input;
	    $pID = $jinput->getVar('prediction_id','0');
	    $pggroup = $jinput->getVar('pggroup','0');
	    $pggrouprank = $jinput->getVar('pggrouprank','0');
	    $pjID = $jinput->getVar('pj','0');
	    $rID = $jinput->getVar('r','0');
	    $set_pj = $jinput->getVar('set_pj','0');
	    $set_r = $jinput->getVar('set_r','0');
	    
	    $link = PredictionHelperRoute:::getPredictionMemberRoute($pID,$uID,null,$pjID,$pggroup ,$rID );
	    //echo '<br />' . $link . '<br />';
	    $this->setRedirect($link);
	}

}
?>