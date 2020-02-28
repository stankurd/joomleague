<?php
use Joomla\CMS\Factory;

 defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerRankingAllTime extends JoomleagueController
{
    public function display($cachable = false, $urlparams = array())
    {
		$viewName = $this->input->get('view', 'rankingalltime');
		$view = $this->getView($viewName);

		$this->addModelToView('joomleague', $view);
		$this->addModelToView('rankingalltime', $view);

		$this->showprojectheading();
		$this->showranking();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
        
    }
/*
    function showrankingalltime( )
    {
        // Get the view name from the query string
        $viewName = Factory::getApplication()->input->getVar( "view", "rankingalltime" );

        // Get the view
        $view =  $this->getView( $viewName );

        // Get the joomleague model
        $jl = $this->getModel( "joomleague", "JoomleagueModel" );
        $jl->set( "_name", "joomleague" );
        if (!Error::isError( $jl ) )
        {
            $view->setModel ( $jl );
        }

        // Get the joomleague model
        $sr = $this->getModel( "rankingalltime", "JoomleagueModel" );
        $sr->set( "_name", "rankingalltime" );
        if (!Error::isError( $sr ) )
        {
            $view->setModel ( $sr );
        }

        $view->display();
    }*/
}
