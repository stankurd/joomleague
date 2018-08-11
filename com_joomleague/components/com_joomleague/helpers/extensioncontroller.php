<?php
/**
 * @see JLGView
 * @see JLGModel
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

class JoomleagueHelperExtensioncontroller
{
	private $input;

	private $extensions;

	public function __construct()
	{
		$this->input = Factory::getApplication()->input;
	}

	public function findFile($filename, $extension)
	{
	    $path = Factory::getApplication()->isClient('administrator')
			? JLG_PATH_SITE . '/extensions/' . $extension . '/admin/controllers/' . $filename
			: JLG_PATH_SITE . '/extensions/' . $extension . '/controllers/' . $filename;

		return file_exists($path) ? $path : false;
	}

	public function getExtensions()
	{
		if (!$this->extensions)
		{
			$this->extensions = JoomleagueHelper::getExtensions($this->input->getInt('p', 0));
		}

		return $this->extensions;
	}

	public function initExtensions()
	{
		$lang = Factory::getLanguage();
		$app = Factory::getApplication();

		foreach ($this->getExtensions() as $extension)
		{
			$extensionpath = JLG_PATH_SITE . '/extensions/' . $extension;

			$mainfile = $extensionpath . '/'. $extension . '.php';

			if (file_exists($mainfile))
			{
				//e.g example.php
				require_once $mainfile;
			}

			$lang_path = $app->isClient('administrator')
				? $extensionpath . '/admin'
				: $extensionpath;
			// language file
			$lang->load('com_joomleague_' . $extension, $lang_path);
		}
	}

	public function addModelPaths($controller)
	{
		$app = Factory::getApplication();

		foreach ($this->getExtensions() as $extension)
		{
		    $path= $app->isClient('administrator')
				? JLG_PATH_SITE . '/extensions/' . $extension . '/admin/models'
				: JLG_PATH_SITE . '/extensions/' . $extension . '/models';

			if (file_exists($path))
			{
				$controller->addModelPath($path, 'JoomleagueModel');
			}
		}
	}

	public function addViewPaths($controller)
	{
		$app = Factory::getApplication();

		foreach ($this->getExtensions() as $extension)
		{
		    $path= $app->isClient('administrator') 
				? JLG_PATH_SITE . '/extensions/' . $extension . '/admin/views'
				: JLG_PATH_SITE . '/extensions/' . $extension . '/views';

			if (file_exists($path))
			{
				$controller->addViewPath($path, 'JoomleagueView');
			}
		}
	}
}
