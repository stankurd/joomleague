<?php
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die;

class JoomleagueController extends JLGController
{
	public function display($cachable = false, $urlparams = array())
	{
		$this->showprojectheading($cachable);
	}

	function showprojectheading($cachable = false)
	{
		parent::display();
	}

	function showbackbutton()
	{
		$viewName = $this->input->get('view', 'backbutton');
		$view = $this->getView($viewName);

		$this->addModelToView('project', $view);

		$view->display();
	}

	function showfooter()
	{
		parent::display();
	}

	protected function addModelToView($modelName, $view)
	{
		$model = $this->getModel($modelName, 'JoomleagueModel');
		if (!empty($model)) {
			$model->set('_name', $modelName);
			$view->setModel($model);
		}
		return $model;
	}
}
