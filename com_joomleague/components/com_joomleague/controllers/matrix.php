<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class JoomleagueControllerMatrix extends JoomleagueController
{
    public function display($cachable = false, $urlparams = false)
    {
        $this->showprojectheading();
        $this->showmatrix();
        $this->showbackbutton();
        $this->showfooter();
    }

    public function showmatrix( )
    {
        $app = Factory::getApplication();
        // Get the view name from the query string
        $viewName = $app->input->getWord( "view", "matrix" );

        // Get the view
        $view = $this->getView( $viewName );

        // Get the joomleague model
        $jl = $this->getModel( "joomleague", "JoomleagueModel" );
        $jl->set( "_name", "joomleague" );
        if (!Error::isError( $jl ) )
        {
            $view->setModel ( $jl );
        }

        // Get the joomleague model
        $sm = $this->getModel( "matrix", "JoomleagueModel" );
        $sm->set( "_name", "matrix" );
        if (!Error::isError( $sm ) )
        {
            $view->setModel ( $sm );
        }

        $view->display();
    }
}
