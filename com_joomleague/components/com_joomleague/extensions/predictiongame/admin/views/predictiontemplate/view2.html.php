<?php

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

//jimport( 'joomla.application.component.view' );
jimport('joomla.form.form');

class JoomleagueViewPredictionTemplate extends JLGView
{
	protected $form;
	protected $item;
	protected $state;
	function display( $tpl = null )
	{
		$app	= JFactory::getApplication();
		$jinput = $app->input;
		$option = $jinput->getCmd('option');
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$lists = array();
		$starttime = microtime(); 
		$item = $this->get('Item');
		$this->item = $item;
        $templatepath = JLG_PATH_EXTENSION_PREDICTIONGAME.'/settings';
		//$templatepath = JPATH_COMPONENT_SITE.DS.'settings';
		$xmlfile = $templatepath.'/default/'.$predictiontemplate->template.'.xml';
        
        $app->enqueueMessage(JText::_('joomleagueViewTemplate xmlfile<br><pre>'.print_r($xmlfile,true).'</pre>'),'Notice');
        $jRegistry = new JRegistry;
		$jRegistry->loadString($predictionTemplate->params, 'ini');
		//$form = JForm::getInstance($predictionTemplate->template, $xmlfile,array('control'=> 'params'));
		//$form->bind($jRegistry);
		//$form->bind($item->params);
		//$jRegistry = new JRegistry;
//$jRegistry->loadString($predictionTemplate->params, 'ini');
		$form = JForm::getInstance($predictionTemplate->template, $xmlfile, 
									array('control'=> 'params'));
		$form->bind($jRegistry);
        // Assign the Data
		$this->form = $form;
        
		$script = $this->get('Script');
		$this->script = $script;
        
        //$this->prediction_id = $jinput->get('predid', 0, '');
        //$this->prediction_id = $jinput->request->get('predid', 0, 'INT');
		$this->prediction_id = $app->getUserState( "$option.prediction_id", '0' );
        //$app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' prediction_id<br><pre>'.print_r($this->prediction_id,true).'</pre>'),'Notice');
        //$this->prediction_id = $app->getUserState( "$option.predid", '0' );
//        $predictionGame = $model->getPredictionGame( $this->prediction_id );
		$this->predictionGame = $model->getPredictionGame( $this->prediction_id );

	}

	

  /**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{
		
        $jinput = JFactory::getApplication()->input;
        $jinput->set('hidemainmenu', true);
        $isNew = $this->item->id ? $this->title = JText::_('COM_SPORTSMANAGEMENT_PREDICTIONTEMPLATE_EDIT') : $this->title = JText::_('COM_SPORTSMANAGEMENT_PREDICTIONTEMPLATE_NEW');
        $this->icon = 'predtemplate';
        
        $this->item->name = $this->item->template;

        parent::addToolbar();

	}		
	
}
?>