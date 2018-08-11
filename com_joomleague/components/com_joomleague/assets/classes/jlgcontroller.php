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
defined('JPATH_BASE') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;

/**
 * Modifies BaseController to take extensions into account
 *
 * @author julien vonthron
 */
class JLGController extends BaseController
{
	/**
	 * Overrides method to first lookup into potential extension for the model.
	 */
	protected function createModel($name, $prefix = '', $config = array())
	{
		// Clean the model name
		$modelName	 = preg_replace( '/[^A-Z0-9_]/i', '', $name );
		$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );

		$result = JLGModel::getInstance($modelName, $classPrefix, $config);

		return $result;
	}

	/**
	 * Overrides method to first lookup into potential extension for the view.
	 */
	protected function createView($name, $prefix = '', $type = '', $config = array())
	{
	    $app   = Factory::getApplication();
	    
		$extensions = JoomleagueHelper::getExtensions($app->input->getInt('p'));

		foreach ($extensions as $e => $extension)
		{
			$result = null;

			// Clean the view name
			$viewName	 = preg_replace( '/[^A-Z0-9_]/i', '', $name );
			$classPrefix = preg_replace( '/[^A-Z0-9_]/i', '', $prefix );
			$viewType	 = preg_replace( '/[^A-Z0-9_]/i', '', $type );

			// Build the view class name
			$viewClassExtension = $classPrefix . $viewName . ucfirst($extension);

			if (!class_exists($viewClassExtension))
			{
				jimport('joomla.filesystem.path' );
				$path = Path::find(
					$this->paths['view'],
					$this->createFileName('view', array( 'name' => $viewName, 'type' => $viewType)));
				if ($path)
				{
					require_once $path;

					if (class_exists($viewClassExtension))
					{
						$result = new $viewClassExtension($config);

						return $result;
					}
				}
			}
			else
			{
				$result = new $viewClassExtension($config);

				return $result;
			}
		}

		// Still here ? Then the extension doesn't override this, use regular view
		return parent::createView($name, $prefix, $type, $config);
	}

	public static function getInstance($prefix, $config = array())
	{
		if (is_object(self::$instance))
		{
			return self::$instance;
		}

		$extensionHelper = new JoomleagueHelperExtensioncontroller;
		$extensionHelper->initExtensions();

		if (isset($config['base_path']))
		{
			parent::getInstance($prefix, $config);
		}
		else
		{
			self::getInstanceWithExtensions($prefix);
		}

		$extensionHelper->addModelPaths(self::$instance);
		$extensionHelper->addViewPaths(self::$instance);

		return self::$instance;
	}

	private static function getInstanceWithExtensions($prefix)
	{
		$extensionHelper = new JoomleagueHelperExtensioncontroller;

		// Get the environment configuration.
		$app = Factory::getApplication();
		$input = $app->input;
		$format = $input->getWord('format');
		$command = $input->get('task', 'display');

		// Check for array format.
		$filter = InputFilter::getInstance();

		if (is_array($command))
		{
			$command = $filter->clean(array_pop(array_keys($command)), 'cmd');
		}
		else
		{
			$command = $filter->clean($command, 'cmd');
		}

		$class = null;

		// Check for a controller.task command.
		// it's the only kind supported for now
		if (strpos($command, '.') !== false)
		{
			// Explode the controller.task command.
			list ($type, $task) = explode('.', $command);

			// Define the controller filename and path.
			$file = self::createFileName('controller', array('name' => $type, 'format' => $format));

			foreach ($extensionHelper->getExtensions() as $extension)
			{
				if (!$path = $extensionHelper->findFile($file, $extension))
				{
					continue;
				}

				require_once $path;

				$className = ucfirst($prefix) . 'Controller' . ucfirst($type);

				if (class_exists($className))
				{
					$class = $className;
					break;
				}
				elseif (class_exists($className . ucfirst($extension)))
				{
					$class = $className . ucfirst($extension);
					break;
				}
				else
				{
					throw new InvalidArgumentException(Text::_('COM_JOOMLEAGUE_WRONG_CLASSNAME_IN_CONTROLLER_FILE') . $className);
				}
			}

			if ($class)
			{
				// Reset the task without the controller context.
				$input->set('task', $task);

				self::$instance = new $class;

				return self::$instance;
			}
		}

		return parent::getInstance($prefix);
	}
}
