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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );
jimport('joomla.form.form');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	Joomleague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionTemplate extends JLGView
{
	protected $form;
	protected $item;
	protected $state;
	function display( $tpl = null )
	{
		$app	= Factory::getApplication();
		$jinput = $app->input;
		
		//$this->form = $this->get('Form');
		//$this->item = $this->get('Item');
		//$this->state = $this->get('State');
		if ( $this->getLayout() == 'form' )
		{
			$this->_displayForm( $tpl );
			return;
		}

		//get the prediction template
		//$predictionTemplate =& $this->get( 'data' );

		parent::display( $tpl );
	}

	function _displayForm( $tpl )
	{
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		$prediction_id		= (int) $app->getUserState( $option . 'prediction_id' );
		$lists = array();
		$db	= Factory::getDBO();
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$model = $this->getModel();
        
		$predictionTemplate	= $this->get( 'data' );
		$app->setUserState($option.'template_help',$predictionTemplate->template);
        
		$predictionGame	= $model->getPredictionGame( $prediction_id );
		//$predictionGame		= $this->getModel()->getPredictionGame( $prediction_id );
		//$templatepath = JLG_PATH_EXTENSION_PREDICTIONGAME.DS.'settings';
		$defaultpath		= JLG_PATH_EXTENSION_PREDICTIONGAME.'/settings';
		$extensiontpath		= JLG_PATH_SITE . '/extensions/predictiongame';
		$isNew				= ( $predictionTemplate->id < 1 );

		// fail if checked out not by 'me'
		if ( $model->isCheckedOut( $user->get( 'id' ) ) )
		{
			$msg = Text::sprintf( 'DESCBEINGEDITTED', Text::_( 'COM_JOOMLEAGUE_ADMIN_PTMPL_THE_PTMPL' ), $predictionTemplate->name );
			$app->redirect( 'index.php?option=' . $option, $msg );
		}

		// Edit or Create?
		if ( !$isNew ) { $this->getModel()->checkout( $user->get( 'id' ) ); }

		
    
    $templatepath = JLG_PATH_EXTENSION_PREDICTIONGAME.'/settings';
    $xmlfile=$templatepath.'/default/'.$predictionTemplate->template.'.xml';
	$extensions = JoomleagueHelper::getExtensions(Factory::getApplication()->input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$extensiontpath =  JPATH_COMPONENT_SITE.'/extensions/'.$extension;
			if (is_dir($extensiontpath.'/settings/default'))
			{
				if (file_exists($extensiontpath.'/settings/default/'.$predictionTemplate->template.'.xml'))
				{
					$xmlfile=$extensiontpath.'/settings/default/'.$predictionTemplate->template.'.xml';
				}
			}
		}
        $jRegistry = new Registry;
		$jRegistry->loadString($predictionTemplate->params, 'ini');
		$form = Form::getInstance($predictionTemplate->template, $xmlfile, 
									array('control'=> 'params'));
		$form->bind($jRegistry);
		
		$this->request_url = $uri->toString();
		$this->template = $predictionTemplate;
		$this->form = $form;
		$this->user = $user;
		
 		$params = new JLParameter( $predictionTemplate->params, $xmlfile );
     	//$this->form = $this->get('form');
 		$this->predictionTemplate =	$predictionTemplate;
 		$this->predictionGame = $predictionGame ;
// 		$this->assignRef( 'pred_id',			$prediction_id );
// 		$this->params =	$params ; 
// 		$this->assignRef( 'lists',				$lists );
// 		$this->assignRef( 'user',				$user );
		$this->addToolbar();
		parent::display( $tpl );
	}

  /**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{
		// Set toolbar items for the page
		$edit=Factory::getApplication()->input->getVar('edit',true);
	
		JLToolBarHelper::save('predictiontemplate.save');
		JLToolBarHelper::apply('predictiontemplate.apply');

		if (!$edit)
		{
			JToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTION_TEMPLATE_ADD_NEW'));
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('predictiontemplate.cancel');
		}		
		else
		{		
			JToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTION_TEMPLATE_EDIT'),'FrontendSettings');
			JToolBarHelper::divider();
			// for existing items the button is renamed `close`
			JLToolBarHelper::cancel('predictiontemplate.cancel',Text::_('COM_JOOMLEAGUE_GLOBAL_CLOSE'));
		}
	}		
	
}
?>