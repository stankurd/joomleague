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

class JoomleagueControllerReferees extends JoomleagueController
{
	public function display($cachable = false, $urlparams = array())
	{
	    $app = Factory::getApplication();
		$viewName = $app->input->get('view', 'referees');
		$view = $this->getView($viewName);

		$this->addModelToView('project', $view);
		$this->addModelToView('referees', $view);

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}
}
