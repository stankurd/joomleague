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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Joomleague Component prediction Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueControllerPredictionRanking extends JoomleagueController
{
	function display()
	{
	    $app = Factory::getApplication();
	  // Get the view name from the query string
        $viewName = $app->input->getVar( "view", "predictionranking" );

        // Get the view
        $view = 		 $this->getView( $viewName );

        // Get the joomleague model
        $jl = $this->getModel( "joomleague", "JoomleagueModel" );
        $jl->set( "_name", "joomleague" );
        if (!Error::isError( $jl ) )
        {
            $view->setModel ( $jl );
        }
    // Get the joomleague model
		$sr = $this->getModel( 'prediction', 'JoomleagueModel' );
		$sr->set( '_name', 'prediction' );
		if ( !Error::isError( $sr ) )
		{
			$view->setModel ( $sr );
		}
		
		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if ( !Error::isError( $jl ) )
		{
			$view->setModel ( $jl );
		}
		
		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	function selectprojectround()
	{
	    Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_PRED_INVALID_TOKEN_REFUSED'));
	    $app = Factory::getApplication();
		$post	= $app->input->get('post');
		//echo '<br /><pre>~' . print_r($post,true) . '~</pre><br />';
		$pID	= $app->input->getVar('prediction_id',	'',	'post',	'int');
		$pggroup	= $app->input->getVar('pggroup',	null,	'post',	'int');
		$pggrouprank= $app->input->getVar('pggrouprank',null,	'post',	'int');
		$pjID	= $app->input->getVar('p',			'',	'post',	'int');
        
		$rID	= $app->input->getVar('round_id',		'',	'post',	'int');
		$set_pj	= $app->input->getVar('set_pj',		'',	'post',	'int');
		$set_r	= $app->input->getVar('set_r',		'',	'post',	'int');

		$link = PredictionHelperRoute::getPredictionRankingRoute($pID,$pjID,$rID,'',$pggroup,$pggrouprank);
        
		//echo '<br />' . $link . '<br />';
		$this->setRedirect($link);
	}

}
?>