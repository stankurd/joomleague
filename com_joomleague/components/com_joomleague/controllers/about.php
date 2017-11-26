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
defined('_JEXEC') or die;

class JoomleagueControllerAbout extends JLGController
{
	public function display($cachable = false, $urlparams = array())
	{
		$viewName = $this->input->get('view', 'about');
		$view = $this->getView( $viewName );

		$this->addModelToView('joomleague', $view);
		$this->addModelToView('version', $view);

		$view->display();
	}
}
