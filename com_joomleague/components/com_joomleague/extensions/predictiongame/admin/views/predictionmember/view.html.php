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
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined( '_JEXEC' ) or die( 'Restricted access' );
require_once JLG_PATH_SITE.'/assets/classes/jlgview.php' ;

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.01a
 */

class JoomleagueViewPredictionMember extends JLGView
{
	function display( $tpl = null )
	{
		$app	= Factory::getApplication();

		if ( $this->getLayout() == 'form' )
		{
			$this->_displayForm( $tpl );
			return;
		}

		//get the predictionuser
		$predictionuser = $this->get( 'data' );

		parent::display( $tpl );
	}

	function _displayForm( $tpl )
	{
		$app= Factory::getApplication();
		$option = $app->input->getCmd('option');
		$db	= Factory::getDBO();
		$uri	= Uri::getInstance();
		$user 	= Factory::getUser();
		$model	= $this->getModel();

		$lists = array();
		//get the member data
		$predictionuser	= $this->get( 'data' );
		$isNew			= ( $predictionuser->id < 1 );

		

		// Edit or Create?
		if ( !$isNew )
		{
			$model->checkout( $user->get( 'id' ) );
		}
		else
		{
			// initialise new record
			$predictionuser->order = 0;
		}

		
       
        //build the html select list for parent positions
		$parents[] = HTMLHelper::_( 'select.option', '0', '- ' . Text::_( 'Prediction Group' ) . ' -' );
		if ( $res = $model->getPredictionGroups() )
		{
			$parents = array_merge( $parents, $res );
		}
		$lists['parents'] = HTMLHelper::_(	'select.genericlist', $parents, 'group_id', 'class="inputbox" size="1"', 'value', 'text',
										$predictionuser->group_id );
		unset( $parents );

		$this->lists = $lists;
		$this->form = $this->get('form');
		$this->predictionuser = $predictionuser;

		parent::display( $tpl );
	}

}
?>