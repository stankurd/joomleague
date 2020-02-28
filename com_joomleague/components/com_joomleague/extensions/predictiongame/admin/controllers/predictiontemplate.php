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
use Joomla\Utilities\ArrayHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );
require_once( JPATH_COMPONENT . DS . 'controllers' . DS . 'joomleague.php' );

/**
 * Joomleague Prediction Template Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueControllerPredictionTemplate extends JoomleagueController
{

	function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('save','save');
		$this->registerTask('apply','save');
		$this->registerTask('reset','remove');
	}

	function display($cachable = false, $urlparams = false)
	{
		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
		$app->enqueueMessage(Text::_('PredictionTemplate Task -> '.$this->getTask()),'');
    
	 	$model=$this->getModel('predictiontemplates');
		$viewType=$document->getType();
		$view=$this->getView('predictiontemplates',$viewType);
		$view->setModel($model,true);	// true is for the default model;
		
		$prediction_id1	= $app->input->getVar( 'prediction_id', '-1', '', 'int' ); echo 'CPT-A' . $prediction_id1 . 'CPT-A<br />';
		$prediction_id2	= (int) $app->getUserState( 'com_joomleague' . 'prediction_id' ); //echo 'CPT-B' . $prediction_id2 . 'CPT-B<br />';

		if ( $prediction_id1 > (-1) )
		{
			$app->setUserState( 'com_joomleague' . 'prediction_id', (int) $prediction_id1 );
		}
		else
		{
			$app->setUserState( 'com_joomleague' . 'prediction_id', (int) $prediction_id2 );
		}
		$prediction_id	= (int) $app->getUserState( 'com_joomleague' . 'prediction_id' ); //echo 'CPT-C' . $prediction_id . 'CPT-C<br />';

		switch( $this->getTask() )
		{
			case 'add'	 :
			{
				$app->input->set( 'hidemainmenu',	0 );
				$app->input->set( 'layout',		'form' );
				$app->input->set( 'view',		'predictiontemplate' );
				$app->input->set( 'edit',		false );

				// Checkout the project
				$model = $this->getModel( 'predictiontemplate' );
				//$model->checkout();
			} break;
			case 'edit'	:
			{
			$model=$this->getModel('predictiontemplate');
			$viewType=$document->getType();
			$view=$this->getView('predictiontemplate',$viewType);
			$view->setModel($model,true);	// true is for the default model;
                    
				$app->input->set( 'hidemainmenu',	0 );
				$app->input->set( 'layout',			'form' );
				$app->input->set( 'view',			'predictiontemplate');
				$app->input->set( 'edit',			true );
				
				//$model = $this->getModel( 'predictiontemplate' );
				
			} break;

		}

		parent::display($cachable, $urlparams);
	}

	public function save()
	{
	Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
	$app = Factory::getApplication();
    $optiontext = strtoupper($app->input->getCmd('option').'_');
    $option = $app->input->getCmd('option');	
	$document = Factory::getDocument();
    $app->enqueueMessage(Text::_('PredictionTemplate Task -> '.$this->getTask()),'');
    
		$msg	= '';
		$post=$app->input->post->getArray();
		$cid = $app->input->post->get('cid',array(0),'array');
//echo 'CPT-C~' . count( $cid ) . '~CPT-C<br />';
//echo 'CPT-A<pre>' . print_r( $post, true ) . '</pre>CPT-A<br />';

		if ( count( $cid ) == 1 ) // We were in the edit mode of only one template
		{
			$post['id'] = (int) $cid[0];
			$model = $this->getModel( 'predictiontemplate' );
			if ( $model->store( $post ) )
			{
				$msg .= Text::_( 'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_SAVED' );
			}
			else
			{
				$msg .= Text::_( 'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_SAVED_ERROR' ) . $index . ": " . $model->getError();
			}
			// Check the table in so it can be edited.... we are done with it anyway
			$model->checkin();
		}
/*
		else
		{
			for ($index = 0; $index < count($cid); $index++)
			{
				$model			= $this->getModel( 'predictiontemplate' );
				$post['id']		= (int) $cid[$index];
				$model->setId($post['id']);
				$template 		= $model->getData();
				$templatepath	= JLG_PATH_EXTENSION_PREDICTIONGAME . '/settings';
				$xmlfile 		= $templatepath . '/default/' . $template->template;
				$jlParams 		= new JLParameter( $template->params, $xmlfile );
				$results		= array();
				$params 		= null;
				$name			= "params";
				foreach ($jlParams->getGroups() as $group => $groups)
				{
					foreach ($jlParams->_xml[$group]->children() as $param)
					{
						if(!in_array($param->attributes('name'), $template->params))
						{
							$post['params'][$param->attributes('name')] = $param->attributes('default');
						}
					}
				}
				if ( $model->store( $post ) )
				{
					$msg = Text::_( 'templates rebuild' );
				}
				else
				{
					$msg = Text::_( 'Error rebuild template ' ) . $index . ": " . $model->getError();
					break;
				}

				// Check the table in so it can be edited.... we are done with it anyway
				$model->checkin();
			}
		}
*/

		if ( $this->getTask() == 'save' )
		{
			$link = 'index.php?option=com_joomleague&view=predictiontemplates&task=predictiontemplate.display';
		    //$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		}
		else
		{
		    //$link = 'index.php?option=com_joomleague&controller=predictiontemplate&task=edit&cid[]=' . $post['id'];
		    
			$link = 'index.php?option=com_joomleague&task=predictiontemplate.edit&cid[]=' . $post['id'];
		}

		//echo $link . '<br />';
		//echo $msg . '<br />';
		$this->setRedirect( $link, $msg );
	}

	function remove()
	{
	Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
	$app = Factory::getApplication();
    $option = $app->input->getCmd('option');
    $optiontext = strtoupper($app->input->getCmd('option').'_');
	$document = Factory::getDocument();
    $app->enqueueMessage(Text::_('PredictionTemplate Task -> '.$this->getTask()),'');
    
		$msg = '';
		$cid = $app->input->post->getVar( 'cid', array(), 'array' );
		ArrayHelper::toInteger( $cid );

		if ( count( $cid ) < 1 )
		{
			$app->enqueueMessage(Text::_( $optiontext.'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_DEL_ITEM' ), 'error');
		}

		$model = $this->getModel( 'predictiontemplate' );

		if ( $model->delete( $cid ) )
		{
			$msg .= Text::_( $optiontext.'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_DEL_ITEM_MSG' );
		}
		else
		{
			$msg .= Text::_( $optiontext.'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_DEL_ITEM_ERROR' ) . $model->getError();
		}

		$link = 'index.php?option=com_joomleague&view=predictiontemplates&task=predictiontemplate.display';
		//echo $msg;
		$this->setRedirect( $link, $msg );
	}

	function cancel()
	{
	Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
	$app = Factory::getApplication();
    $option = $app->input->getCmd('option');
    $optiontext = strtoupper($app->input->getCmd('option').'_');
	$document = Factory::getDocument();
    $app->enqueueMessage(Text::_('PredictionTemplate Task -> '.$this->getTask()),'');
    
		$msg = '';
		// Checkin the template
		//$model = $this->getModel( 'predcitiontemplates' );
		$model = $this->getModel( 'predictiontemplate' );
		$model->checkin();

		$link = 'index.php?option=com_joomleague&view=predictiontemplates&task=predictiontemplate.display';
		$this->setRedirect( $link, $msg );
	}

	function masterimport()
	{
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$msg			= '';
		$templateid		= $app->input->post->getVar( 'templateid', 0, 'int' );
		//$projectid	= $app->input->post->getVar( 'project_id', 0, 'int' );
		$prediction_id	= $app->input->post->getVar( 'prediction_id', 0, 'int' );

		$model = $this->getModel( 'predictiontemplate' );

		if ( $model->import( $templateid, $prediction_id ) )
		{
			$msg = Text::_( 'COM_JOOMLEAGUE_ADMIN_PTMPL_CTRL_TMPL_IMPORTED' );
		}
		else
		{
			$msg = Text::_( 'Error importing prediction template' ) . $model->getError();
		}
		//$this->setRedirect( 'index.php?option=com_joomleague&view=predictiontemplates', $msg );
		//$link = 'index.php?option=com_joomleague&view=predictiontemplates';
		
		$link = 'index.php?option=com_joomleague&view=predictiontemplates&task=predictiontemplate.display';
		//echo $link . '<br />';
		//echo $msg . '<br />';
		$this->setRedirect( $link, $msg );
	}

}
?>