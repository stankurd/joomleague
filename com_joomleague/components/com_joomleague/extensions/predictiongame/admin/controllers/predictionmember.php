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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
require_once(JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php');

/**
 * Joomleague Prediction Member Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueControllerPredictionMember extends JoomleagueController
{

	function __construct($config = array())
	{
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('add',	'display');
		$this->registerTask('edit',	'display');
		$this->registerTask('apply',	'save');
		$this->registerTask('reminder',	'sendReminder');
	}

	public function display($cachable = false, $urlparams = false)
	{	
		$app = Factory::getApplication();	
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
    
		$app->enqueueMessage(Text::_('PredictionMember Task -> '.$this->getTask()),'');
    
	 	$model=$this->getModel('predictionmembers');
		$viewType=$document->getType();
		$view=$this->getView('predictionmembers',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1	= $app->input->getVar('prediction_id','-1','','int');
		$prediction_id2	= (int)$app->getUserState('com_joomleague' . 'prediction_id');

		if ($prediction_id1 > (-1))
		{
			$app->setUserState('com_joomleague' . 'prediction_id',(int)$prediction_id1);
		}
		else
		{
			$app->setUserState('com_joomleague' . 'prediction_id',(int)$prediction_id2);
		}
		$prediction_id	= (int)$app->getUserState('com_joomleague' . 'prediction_id');

		switch($this->getTask())
		{
			case 'add' :
			{
				$app->input->set('hidemainmenu',	1);
				$app->input->set('layout',			'form');
				$app->input->set('view',			'predictionmember');
				$app->input->set('edit',			false);

				// Checkout the project
				$model = $this->getModel('predictionmember');
				$model->checkout();
			} break;
			case 'edit' :
			{
				$app->input->set('hidemainmenu',	1);
				$app->input->set('layout',			'form');
				$app->input->set('view',			'predictionmember');
				$app->input->set('edit',			true);

				// Checkout the project
				$model = $this->getModel('predictionmember');
				$model->checkout();
			} break;

      case 'editlist' :
			{
				$app->input->set('hidemainmenu',	0);
				$app->input->set('layout',			'editlist');
				$app->input->set('view',			'predictionmembers');
				$app->input->set('edit',			false);
			}
			break;
		}

		parent::display();

	}

	// remove the prediction_member(s) in cid and remove also the tipps associated with the deleted prediction_person(s)
	function remove()
	{
		$app = Factory::getApplication();
		$post	= $app->input->post->getArray();
		//echo '<pre>'; print_r($post); echo '</pre>';
		$option = $app->input->getCmd('option');
		$optiontext = strtoupper($app->input->getCmd('option').'_');
    
		$d		= ' - ';
		$msg	= '';
		$cid	= $app->input->post->getVar('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$prediction_id	= $app->input->post->getInt('prediction_id',(-1));
		//echo '<pre>'; print_r($cid); echo '</pre>';

		if (count($cid) < 1)
		{
			$app->enqueueMessage(Text::_($optiontext.'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_DEL_ITEM'), 'error');
		}

		$model = $this->getModel('predictionmember');

		if (!$model->deletePredictionResults($cid,$prediction_id))
		{
			$msg .= $d . Text::_($optiontext.'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_DEL_MSG') . $model->getError();
		}
		$msg .= $d . Text::_($optiontext.'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_DEL_PRESULTS');

		if (!$model->deletePredictionMembers($cid))
		{
			$msg .= Text::_($optiontext.'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_DEL_PMEMBERS_MSG') . $model->getError();
		}

		$msg .= $d . Text::_($optiontext.'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_DEL_PMEMBERS');

		$link = 'index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display';
		//echo $msg;
		$this->setRedirect($link,$msg);
	}

	// send a reminder mail to make a tipp on needed prediction games to selected members
	function sendReminder()
	{
		$app = Factory::getApplication();
		JLToolBarHelper::title( Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_SEND_REMINDER_MAIL' ), 'generic.png' );
		JLToolBarHelper::back( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_BACK', 'index.php?option=com_joomleague&view=predictionmembers' );

		echo 'This will send an email to all members of the prediction game with reminder option enabled. Are you sure?';
		$post		= $app->input->post->getArray();
		$cid		= $app->input->post->getVar( 'cid', array(0), 'array' );
		$pgmid		= $app->input->post->getVar( 'prediction_id', array(0), 'array' );
		$post['id'] = (int) $cid[0];
		$post['predgameid'] = (int) $pgmid[0];
		echo '<pre>'; print_r($post); echo '</pre>';


		if ( $post['predgameid'] == 0 )
		{
			$app->enqueueMessage(Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_SELECT_ERROR' ), 'error' );
		}
		$msg		= '';
		$d			= ' - ';

		$model = $this->getModel( 'predictionmember' );
		$model->sendEmailtoMembers($cid,$pgmid);

		$link = 'index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display';
		//echo $msg;
		$this->setRedirect( $link, $msg );
	}

	function publish()
	{
		$app = Factory::getApplication();
		$cids = $app->input->post->getVar( 'cid', array(), 'array' );
		ArrayHelper::toInteger( $cids );
		$predictionGameID	= $app->input->post->getVar( 'prediction_id', '', 'int' );

		if ( count( $cids ) < 1 )
		{
			$app->enqueueMessage(Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_SEL_MEMBER_APPR' ), 'error');
		}

		$model = $this->getModel( 'predictionmember' );
		if( !$model->publish( $cids, 1, $predictionGameID ) )
		{
			echo "<script> alert( '" . $model->getError(true) . "' ); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display' );
	}

	function unpublish()
	{		
		$app = Factory::getApplication();
		$cids = $app->input->post->getVar( 'cid', array(), 'array' );
		ArrayHelper::toInteger( $cids );
		$predictionGameID	= $app->input->post->getVar( 'prediction_id', '', 'int' );

		if ( count( $cids ) < 1 )
		{
			$app->enqueueMessage(Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_CTRL_SEL_MEMBER_REJECT' ), 'error' );
		}

		$model = $this->getModel( 'predictionmember' );
		if ( !$model->publish( $cids, 0, $predictionGameID ) )
		{
			echo "<script> alert( '" . $model->getError(true)  ."' ); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display' );
	}

function cancel()
	{
		// Checkin the project
		$model=$this->getModel('predictionmember');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display');
	}
    
function save()
	{
		//Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$post = $app->input->post->getArray();
		$cid = $app->input->post->getVar('cid',array(0),'array');
		$post['id'] = (int) $cid[0];
        $app->enqueueMessage(Text::_('PredictionMember Task save -> '.'<pre>'.print_r($post,true).'</pre>'),'');
		$model = $this->getModel('predictionmember');
		if ($model->store($post))
		{
			$msg=Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONMEMBER_CTRL_SAVED');
		}
		else
		{
			$msg = Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIONMEMBER_CTRL_ERROR_SAVE');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if ($this->getTask()=='save')
		{
			$link='index.php?option=com_joomleague&view=predictionmembers';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=predictionmember.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}
  
    
  function save_memberlist()
  {
  $model = $this->getModel('predictionmembers');
  $save_memberlist = $model->save_memberlist();
  
  $msg = Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBER_LIST_SAVED');
  $link = 'index.php?option=com_joomleague&view=predictionmembers&task=predictionmember.display';
	$this->setRedirect($link,$msg);
  
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
	function getModel($name = 'predictionmember', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
  
  
}
?>