<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * DatabaseTool Controller
 *
 * @author	Kurt Norgaz
 */
class JoomleagueControllerDatabaseTool extends JLGControllerAdmin
{

	public function __construct()
	{
		parent::__construct();

		$this->registerTask('repair','repair');
		$this->registerTask('optimize','optimize');
	}

	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	public function optimize()
	{
		$model=$this->getModel('databasetools');
		if ($model->optimize())
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_OPTIMIZE');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_OPTIMIZE').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=databasetools';
		$this->setRedirect($link,$msg);
	}

	public function repair()
	{
		$model=$this->getModel('databasetools');
		if ($model->repair())
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_REPAIR');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_REPAIR').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=databasetools';
		$this->setRedirect($link,$msg);
	}
}
