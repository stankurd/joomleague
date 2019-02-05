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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionMembers extends JLGView
{

  function display( $tpl = null )
	{


    if ( $this->getLayout() == 'default')
		{
			$this->_display( $tpl );
			return;
		}
		
		if ( $this->getLayout() == 'editlist')
		{
			$this->_editlist( $tpl );
			return;
		}
    
    parent::display($tpl);
		
	}

  function _editlist( $tpl = null )
	{
		$app			= Factory::getApplication();
		$db				= Factory::getDBO();
		$uri			= Uri::getInstance();
		//$model		= $this->getModel();
		// Get a refrence of the page instance in joomla
		$document	= Factory::getDocument();
		$option = $app->input->getCmd('option');
		$optiontext = strtoupper($app->input->getCmd('option').'_');
		$this->optiontext = $optiontext;
    /*
     	$baseurl    = Uri::root();
 		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.js');
 		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.Request.js');
 		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Observer.js');
 		$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/quickaddteam.js');
 		$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');
	*/	

    		
		$prediction_id		= (int) $app->getUserState( $option . 'prediction_id' );
		$prediction_name = $this->getModel()->getPredictionProjectName($prediction_id);
		$this->prediction_name = $prediction_name;
		
    $res_prediction_members = $this->getModel()->getPredictionMembers($prediction_id);
    
    if ( $res_prediction_members )
    {
    $lists['prediction_members']=HTMLHelper::_(	'select.genericlist',
										$res_prediction_members,
										'prediction_members[]',
										'class="inputbox" multiple="true" onchange="" size="15"',
										'value',
										'text');
    }
    else
    {
    $lists['prediction_members'] = '<select name="prediction_members[]" id="prediction_members" style="" class="inputbox" multiple="true" size="15"></select>';
    }
    
    $res_joomla_members = $this->getModel()->getJLUsers($prediction_id);
    if ( $res_joomla_members )
    {
    $lists['members']=HTMLHelper::_(	'select.genericlist',
										$res_joomla_members,
										'members[]',
										'class="inputbox" multiple="true" onchange="" size="15"',
										'value',
										'text');
    }
                    																
    $this->prediction_id = $prediction_id ;
    $this->lists = $lists;
    $this->request_url = $uri->toString();
    //$this->addToolbar();
		parent::display( $tpl );
	}	

	function _display( $tpl = null )
	{
 		$app = Factory::getApplication();
		$option = $app->input->getCmd('option');
		$document = Factory::getDocument();
    
		$optiontext = strtoupper($app->input->getCmd('option').'_');
		$this->optiontext = $optiontext;    
    
		$prediction_id		= (int) $app->getUserState( $option . 'prediction_id' );
//echo '#' . $prediction_id . '#<br />';
		$lists				= array();
		$db					= Factory::getDBO();
		$uri				= Uri::getInstance();
		$items				= $this->get( 'Data' );
		$total				= $this->get( 'Total' );
		$pagination			= $this->get( 'Pagination' );
		//$model				= $this->getModel();
		$filter_state		= $app->getUserStateFromRequest( $option . 'tmb_filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $app->getUserStateFromRequest( $option . 'tmb_filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmb_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $app->getUserStateFromRequest( $option . 'tmb_search',				'search',			'',				'string' );
		$search				= StringHelper::strtolower( $search );

		$baseurl    = Uri::root();
		//$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.js');
		//$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Autocompleter.Request.js');
		//$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/Observer.js');
		//$document->addScript($baseurl.'administrator/components/com_joomleague/assets/js/autocompleter/1_4/quickaddteam.js');
		//$document->addStyleSheet($baseurl.'administrator/components/com_joomleague/assets/css/Autocompleter.css');
		
		// state filter
		$lists['state']		= HTMLHelper::_( 'grid.state',  $filter_state );

		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		//build the html select list for prediction games
		$predictions[] = HTMLHelper::_( 'select.option', '0', '- ' . Text::_( 'COM_JOOMLEAGUE_GLOBAL_SELECT_PRED_GAME' ) . ' -', 'value', 'text' );
		if ( $res = $this->getModel()->getPredictionGames() ) { $predictions = array_merge( $predictions, $res ); }
		$lists['predictions'] = HTMLHelper::_(	'select.genericlist',
											$predictions,
											'prediction_id',
											'class="inputbox" onChange="this.form.submit();" ',
											'value',
											'text',
											$prediction_id
										);
		unset( $res );

		// Set toolbar items for the page
	
        $stylelink = '<link rel="stylesheet" href="'.Uri::root().'administrator/components/com_joomleague/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
	$document->addCustomTag($stylelink);
	
		JToolBarHelper::title( Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_TITLE' ), 'pred-cpanel' );

		JLToolBarHelper::custom( 'predictionmember.reminder', 'send.png', 'send_f2.png', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_SEND_REMINDER' ), true );
		JToolBarHelper::divider();
		
		if ( $prediction_id )
		{
		JLToolBarHelper::editList('predictionmember.edit');
		JLToolBarHelper::custom('predictionmember.editlist','upload.png','upload_f2.png',Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_BUTTON_ASSIGN'),false);
 		JToolBarHelper::divider();
 		}
		JLToolBarHelper::publish( 'predictionmember.publish', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_APPROVE' ) );
		JLToolBarHelper::unpublish( 'predictionmember.unpublish', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_REJECT' ) );
		JToolBarHelper::divider();

		//JToolBarHelper::addNew();
		//JToolBarHelper::divider();

		JLToolBarHelper::deleteList( '', 'predictionmember.remove' );
		JToolBarHelper::divider();

		//JLToolBarHelper::onlinehelp();
	
		$this->user = Factory::getUser();
		$this->lists = $lists;
		
		if ( $prediction_id )
		{
		$this->items = $items ;
		}
		
		$this->pagination = $pagination;
		$url=$uri->toString();
		$this->request_url = $url;
		//$this->addToolbar();
		parent::display( $tpl );
	}
		// Set toolbar items for the page
	/**	protected function addToolbar()
	
        $stylelink = '<link rel="stylesheet" href="'.Uri::root().'administrator/components/com_joomleague/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
	$document->addCustomTag($stylelink);
	
		JToolBarHelper::title( Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_TITLE' ), 'pred-cpanel' );

		JLToolBarHelper::custom( 'predictionmember.reminder', 'send.png', 'send_f2.png', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_SEND_REMINDER' ), true );
		JToolBarHelper::divider();
		
		if ( $prediction_id )
		{
		JLToolBarHelper::editList('predictionmember.edit');
		JLToolBarHelper::custom('predictionmember.editlist','upload.png','upload_f2.png',Text::_('COM_JOOMLEAGUE_ADMIN_PMEMBERS_BUTTON_ASSIGN'),false);
 		JToolBarHelper::divider();
 		}
		JLToolBarHelper::publish( 'predictionmember.publish', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_APPROVE' ) );
		JLToolBarHelper::unpublish( 'predictionmember.unpublish', Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBERS_REJECT' ) );
		JToolBarHelper::divider();

		//JToolBarHelper::addNew();
		//JToolBarHelper::divider();

		JLToolBarHelper::deleteList( '', 'predictionmember.remove' );
		JToolBarHelper::divider();

		//JLToolBarHelper::onlinehelp();
	}*/
}	
?>