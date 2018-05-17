<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Update Controller
 *
 * @author		Kurt Norgaz
 */
class JoomleagueControllerUpdate extends JoomleagueController
{

	public function __construct()
	{
		// Register Extra tasks
		parent::__construct();
	}

	public function update()
	{
		ToolbarHelper::back(Text::_('COM_JOOMLEAGUE_BACK_UPDATELIST'),Route::_('index.php?option=com_joomleague&view=updates'));
		$input = $this->input;
		$post = $input->post->getArray();
		$file_name=$input->getString('file_name');
		$path=explode('/',$file_name);

		if (count($path) > 1)
		{
			$filepath=JPATH_COMPONENT_SITE.'/extensions/'.$path[0].'/admin/install/'.$path[1];
		}
		else
		{
			$filepath=JPATH_COMPONENT_ADMINISTRATOR.'/assets/updates/'.$path[0];
		}
		$model=$this->getModel('updates');
		echo Text::sprintf('Updating from file [ %s ]','<b>'.Path::clean($filepath).'</b>');
		if (File::exists($filepath))
		{
			$model->loadUpdateFile($filepath,$file_name);
		}
		else
		{
			echo Text::_('Update file not found!');
		}
	}
}
