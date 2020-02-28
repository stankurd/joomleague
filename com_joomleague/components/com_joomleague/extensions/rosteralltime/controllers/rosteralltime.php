<?php
use Joomla\CMS\Factory;

 defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JoomleagueControllerRosterAllTime extends JoomleagueController
{
    public function display($cachable = false, $urlparams = array())
    {
        /*
        $app = Factory::getApplication();
		$viewName = $app->input->get('view', 'rosteralltime');
		$view = $this->getView($viewName);

		$this->addModelToView('joomleague', $view);
		$this->addModelToView('rosteralltime', $view);
		*/
        $app = Factory::getApplication();
        // Get the view name from the query string
        $viewName = $app->input->getWord( 'view', 'rosteralltime' );
        
        // Get the view
        $view = $this->getView( $viewName );
        
		// Get the joomleague model
		$jl = $this->getModel( 'project', 'JoomleagueModel' );
		$jl->set( '_name', 'project' );
		if (!Error::isError( $jl ) )
		{
		    $view->setModel ( $jl );
		}
		
		// Get the joomleague model
		$sr = $this->getModel( 'rosteralltime', 'JoomleagueModel' );
		$sr->set( '_name', 'rosteralltime' );
		if ( !Error::isError( $sr ) )
		{
		    $view->setModel ( $sr );
		}
		$this->showprojectheading();
		$this->showroster();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
        
}
}
?>