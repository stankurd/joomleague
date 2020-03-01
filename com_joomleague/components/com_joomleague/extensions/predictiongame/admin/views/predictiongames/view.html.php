<?php
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

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
defined( '_JEXEC' ) or die( 'Restricted access' );
JLoader::register('JoomleagueHelper', JLG_PATH_ADMIN.'/helpers/joomleaguehelper.php');
jimport( 'joomla.application.component.view' );
jimport('joomla.html.parameter.element.timezones');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionGames extends JLGView
{
	protected $items;
	protected $pagination;
	protected $state;
	
	function display( $tpl = null )
	{
		$app = Factory::getApplication();
    	$jinput = $app->input;
    // Get a refrence of the page instance in joomla
	$document	= Factory::getDocument();
	$version = urlencode(JoomleagueHelper::getVersion());
    $option = $app->input->getCmd('option');
    $optiontext = strtoupper($app->input->getCmd('option').'_');
    $this->optiontext = $optiontext ;
    $edit = $jinput->get('edit');
	$copy = $jinput->get('copy');
		$prediction_id		= (int) $app->getUserState( $option . 'prediction_id' );
		//echo '#' . $prediction_id . '#<br />';
		$model = $this->getModel();
    
		$lists				= array();
		$db					= Factory::getDBO();
		$uri 				= Uri::getInstance();
		$items				= $this->get( 'Data' );
		$total				= $this->get( 'Total' );
		$pagination			= $this->get( 'Pagination' );
		$this->state 		= $this->get('State');
		$filter_state		= $app->getUserStateFromRequest( $option . 'pre_filter_state',		'filter_state',		'',			'word' );
		$filter_order		= $app->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',		'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$search				= $app->getUserStateFromRequest( $option . 'pre_search',				'search',			'',				'string' );
		$search				= StringHelper::strtolower( $search );

		// state filter
		$lists['state']		= HtmlHelper::_( 'grid.state',  $filter_state );
		//$lists['state'] = JoomleagueHelper::stateOptions($this->state->get('filter.state'));
		// table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// search filter
		$lists['search'] = $search;

		//build the html select list for prediction games
		$predictions[] = HTMLHelper::_( 'select.option', '0', '- ' . Text::_( 'Select Prediction Game' ) . ' -', 'value', 'text' );
		if ( $res = $this->getModel()->getPredictionGames() ) { $predictions = array_merge( $predictions, $res ); }
		$lists['predictions'] = HtmlHelper::_(	'select.genericlist',
											$predictions,
											'prediction_id',
											//'class="inputbox validate-select-required" ',
											'class="inputbox" onChange="this.form.submit();" ',
											//'class="inputbox" onChange="this.form.submit();" style="width:200px"',
											'value',
											'text',
											$prediction_id
										);
		unset( $res );

		// Set toolbar items for the page
        $stylelink = '<link rel="stylesheet" href="'.Uri::root().'administrator/components/com_joomleague/assets/css/jlextusericons.css'.'" type="text/css" />' ."\n";
		$document->addCustomTag($stylelink);
		$this->user = Factory::getUser();
		$this->lists = $lists ;
		$this->items = $items ;
		$this->dPredictionID = 	$prediction_id ;
		$this->pagination = $pagination ;
		
		if ( $prediction_id > 0 )
		{
			$this->predictionProjects = $this->getModel()->getChilds( $prediction_id );
			$this->predictionAdmins = $this->getModel()->getAdmins( $prediction_id ) ;
		}

		$url=$uri->toString();
		$this->request_url = $url;
		$this->edit = $edit;
		$this->copy = $copy;
    	$this->addToolbar();

		parent::display( $tpl );
	}
	protected function addToolbar()
	{
			ToolbarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_TITLE'),'pred-cpanel');

			JLToolBarHelper::publish('predictiongame.publish');
			JLToolBarHelper::unpublish('predictiongame.unpublish');
			JToolBarHelper::divider();

			JLToolBarHelper::addNew('predictiongame.add');
			//JLToolBarHelper::editList('predictiongames.edit');
			ToolBarHelper::custom( 'predictiongame.copy', 'copy.png', 'copy_f2.png', 'COM_JOOMLEAGUE_GLOBAL_COPY',false );
			ToolBarHelper::divider();
			//JToolBarHelper::deleteList( Text::_('JL_ADMIN_PGAMES_DELETE'));
			JLToolBarHelper::deleteList( Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_DELETE'), 'predictiongames.remove');
			ToolBarHelper::divider();
			ToolBarHelper::custom('predictiongame.rebuild','restore.png','restore_f2.png',Text::_('COM_JOOMLEAGUE_ADMIN_PGAMES_REBUILDS'),false);
			//JLToolBarHelper::archiveList('predictiongames.archive');
			//JLToolBarHelper::trash('predictiongames.trash');
			ToolBarHelper::divider();
			JLToolBarHelper::help('screen.joomleague',true);
	}
}
?>