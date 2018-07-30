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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;


class JLGModel extends BaseDatabaseModel
{
	/**
	 * Overrides method to try to load model from extension if it exists
	 */
	public static function getInstance($type, $prefix = '', $config = array() )
	{
		$app  	= Factory::getApplication();
		$extensions = JoomleagueHelper::getExtensions($app->input->getInt('p'));

		foreach ($extensions as $e => $extension)
		{
			$modelType = preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$modelClass	= $prefix . ucfirst($modelType) . ucfirst($extension);
			$result		= false;

			if (!class_exists($modelClass))
			{
				jimport('joomla.filesystem.path');

				$path = Path::find(parent::addIncludePath(null, $prefix), self::_createFileName('model', array('name' => $type)));

				if (!$path)
				{
					$path = Path::find(parent::addIncludePath(null, ''), self::_createFileName('model', array('name' => $type)));
				}

				if ($path)
				{
					require_once $path;

					if (class_exists($modelClass))
					{
						$result = new $modelClass($config);

						return $result;
					}
				}
			}
			else
			{
				$result = new $modelClass($config);

				return $result;
			}
		}

		// Still here ? Then the extension doesn't override this, use regular way
		return parent::getInstance($type, $prefix, $config);
	}
}
