<?php
// no direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

class JoomleagueHelpersView
{
	function load($viewName, $layoutName='default', $viewFormat='html', $vars=null)
	{
		// Get the application
		$app = Factory::getApplication();

		$app->input->set('view', $viewName);

        // Register the layout paths for the view
	    $paths = new SplPriorityQueue;
	    $paths->insert(JPATH_COMPONENT . '/views/' . strtolower($viewName) . '/tmpl', 'normal');
	 
	    $viewClass  = 'JLGView' . ucfirst($viewName) . ucfirst($viewFormat);
	    $modelClass = 'JoomleagueModel' . ucfirst($viewName);

	    if (false === class_exists($modelClass))
	    {
	      $modelClass = 'JoomleagueModelsDefault';
	    }

	    $view = new $viewClass(new $modelClass, $paths);

	    $view->setLayout($layoutName);
	    
		if(isset($vars)) 
		{
			foreach($vars as $varName => $var) 
			{
				$view->$varName = $var;
			}
		}

		return $view;
	}

	function getHtml($view, $layout, $item, $data)
	{
		$objectView = joomleagueHelpersView::load($view, $layout, 'phtml');
  		$objectView->$item = $data;

  		ob_start();
  		echo $objectView->render();
  		$html = ob_get_contents();
  		ob_clean();

  		return $html;
	}
}
