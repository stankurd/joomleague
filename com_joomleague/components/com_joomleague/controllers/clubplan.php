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

class JoomleagueControllerClubPlan extends JoomleagueController
{
	public function display($cachable = false, $urlparams = array())
	{
		$viewName = $this->input->get('view','clubplan');
		$startdate = $this->input->get('startdate',null);
		$enddate = $this->input->get('enddate',null);
		$view = $this->getView($viewName);

		$this->addModelToView('joomleague', $view);
		$clubPlanModel = $this->addModelToView('clubplan', $view);
		$clubPlanModel->setStartDate($startdate);
		$clubPlanModel->setEndDate($enddate);

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}
}
