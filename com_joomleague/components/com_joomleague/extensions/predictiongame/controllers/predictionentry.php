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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Utilities\ArrayHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueControllerPredictionEntry extends JLGController
{
	
	function __construct()
	{
	    //$post = $app->input->post->getArray();
	    // Register Extra tasks
		//$this->registerTask( 'add',			'display' );
		//$this->registerTask( 'edit',		'display' );
		//$this->registerTask( 'apply',		'save' );
		//$this->registerTask( 'copy',		'copysave' );
		//$this->registerTask( 'apply',		'savepredictiongame' );
		parent::__construct();
	}
	

	function display($cachable, $urlparams = false)
	{
		// Get the view name from the query string
		$viewName = $app->input->getVar( 'view', 'editmatch' );
		$viewName = $app->input->getVar( 'view' );
//echo '<br /><pre>~' . print_r( $viewname, true ) . '~</pre><br />';

		// Get the view
		$view = $this->getView( $viewName );

		$this->showprojectheading();
		$this->showbackbutton();
		$this->showfooter();
		parent::display($cachable, $urlparams = false);
	}

	function register()
	{    
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
    
    //$app->enqueueMessage(Text::_('PredictionEntry Task -> '.$this->getTask()),'');
    
    Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_PRED_INVALID_TOKEN_REFUSED'));
		
		$msg	= '';
		$link	= '';
    	$post	= $app->input->post->getArray();
		$predictionGameID	= $app->input->post->getVar('prediction_id', '', 'int');
		$joomlaUserID		= $app->input->post->getVar('user_id', '', 'int');
		$approved			= $app->input->getVar('approved', 0, '', 'int');
				
		//$model		= $this->getModel('predictionentry');
		$model = $this->getModel('Prediction');
		$mdlPredictionEntry = BaseDatabaseModel::getInstance("PredictionEntry", "JoomleagueModel");
		$user		= Factory::getUser();
		$isMember	= $model->checkPredictionMembership();

		if ( ( $user->id != $joomlaUserID )  )
		{
			$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_1');			
			$link = Uri::getInstance()->toString();
		}
		else
		{
			if ($isMember)
			{
				$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_4');
				$link = Uri::getInstance()->toString();
			}
			else
			{
				//$post['registerDate'] = HTMLHelper::date(time(),'Y-m-d h:i:s');
                $post['registerDate'] = HTMLHelper::date($input = 'now', 'Y-m-d h:i:s', false);
				//if (!$model->store($post,'PredictionEntry'))
				//if (!$model->store($post))
				if (!$mdlPredictionEntry->store($post))
				{
					$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_5');
					$link = Uri::getInstance()->toString();
				}
				else
				{
					$cids = array();
					$cids[] = $mdlPredictionEntry->getDbo()->insertid();
					ArrayHelper::toInteger($cids);

					$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_MSG_2');
					if ($model->sendMembershipConfirmation($cids))
					{
						$msg .= ' - ';
						$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_MSG_3');
					}
					else
					{
						$msg .= ' - ';
						$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_6');
					}
					$params = array(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										's' => '1' );

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = Route::_('index.php?' . $query, false);
				}
			}
		}

		//echo '<br /><br />';
		//echo '#' . $msg . '#<br />'; 
		$this->setRedirect($link,$msg);
	}

	function select()
	{
		Session::checkToken() or jexit(Text::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$app = Factory::getApplication();
		$pID	= $app->input->post->getVar('prediction_id',	'',	'int');
		$uID	= $app->input->post->getVar('uid',			null,	'int');
		if (empty($uID))
		{$uID=null;}
		$link = PredictionHelperRoute::getPredictionTippEntryRoute($pID,$uID);
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

	function selectprojectround()
	{
		Session::checkToken() or jexit(Text::_('JL_PRED_INVALID_TOKEN_REFUSED'));
		$app = Factory::getApplication();
		$post	= $app->input->post->getArray();
		$pID	= $app->input->post->getVar('prediction_id',	null,	'int');
        
		//$pjID	= $app->input->post->getVar('project_id',	null,	'int');
		$pjID	= $app->input->post->getVar('p',	null,	'int');
        
		$rID	= $app->input->post->getVar('r',			null,	'int');
		$uID	= $app->input->post->getVar('uid',			null,	'int');
		$link = PredictionHelperRoute::getPredictionTippEntryRoute($pID,$uID,$rID,$pjID);
		$this->setRedirect($link);
	}

  /**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	function getModel($name = 'predictionentry', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	function addtipp()
	{
		Session::checkToken() or jexit(Text::_('JL_PRED_ENTRY_INVALID_TOKEN_PREDICTIONS_NOT_SAVED'));
		$app = Factory::getApplication();
		$optiontext = strtoupper($app->input->getCmd('option').'_');
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
		
		$msg	= '';
		$link	= '';

		$predictionGameID	= $app->input->post->getVar('prediction_id','','int');
		$joomlaUserID		= $app->input->post->getVar('user_id','','int');
		$memberID		= $app->input->post->getVar('memberID','','int');
		$round_id		= $app->input->post->getVar('round_id','','int');
		$pjID			= $app->input->post->getVar('pjID','','int');
		$set_r			= $app->input->post->getVar('set_r','','int');
		$set_pj			= $app->input->post->getVar('set_pj','','int');

		$model		= $this->getModel('predictionentry');
		$user		= Factory::getUser();
		$isMember	= $model->checkPredictionMembership();
		$allowedAdmin = $model->getAllowed();

		if ( ( ( $user->id != $joomlaUserID ) ) && ( !$allowedAdmin ) )
		{
			$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_1');
			$link = Uri::getInstance()->toString();
		}
		else
		{
			if ( ( !$isMember ) && ( !$allowedAdmin ) )
			{
				$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_2');
				$link = Uri::getInstance()->toString();
			}
			else
			{
				if ($pjID!=$set_pj)
				{
					$params = array	(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										'pj' => $set_pj
									);

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = Route::_('index.php?' . $query,false);
					$this->setRedirect($link);
				}

				if ( $round_id != $set_r )
				{
					$params = array	(	'option' => 'com_joomleague',
										'view' => 'predictionentry',
										'prediction_id' => $predictionGameID,
										'r' => $set_r,
										'pj' => $pjID
									);

					$query = JoomleagueHelperRoute::buildQuery($params);
					$link = Route::_('index.php?' . $query,false);
					$this->setRedirect($link);
				}
				$mdlPredictionEntry = BaseDatabaseModel::getInstance('PredictionEntry' , 'JoomleagueModel');
				//$model = $this->getModel('PredictionEntry');
				if ( !$mdlPredictionEntry->savePredictions($allowedAdmin) )
				{
					$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_ERROR_3');
					$link = Uri::getInstance()->toString();
				}
				else
				{
					$msg .= Text::_('COM_JOOMLEAGUE_PRED_ENTRY_CONTROLLER_MSG_1');
					$link = Uri::getInstance()->toString();
				}
			}
		}
		
    //echo '<br />' . $link . '<br />';
		//echo '<br />' . $msg . '<br />';
		
		$this->setRedirect($link,$msg);
	}

}
?>