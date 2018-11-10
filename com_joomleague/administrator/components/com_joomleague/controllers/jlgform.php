<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * @author		Julien Vonthron
 *
 * base: jlgcontroller.php
 */

defined('_JEXEC') or die;

/**
 * JLG - form controller
 */
class JLGControllerForm extends FormController
{

	/**
	 * Override CreateModel so we can point to the JLGModel
	 * @see BaseController::createModel()
	 */
	protected function createModel($name, $prefix = '', $config = array())
	{
		$modelName = preg_replace('/[^A-Z0-9_]/i','',$name);
		$classPrefix = preg_replace('/[^A-Z0-9_]/i','',$prefix);

		$result = JLGModel::getInstance($modelName,$classPrefix,$config); // pointing to JLGModel

		return $result;
	}


	/**
	 * override default createView function
	 * @see BaseController::createView()
	 */
	protected function createView($name, $prefix = '', $type = '', $config = array())
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		$extensions = JoomleagueHelper::getExtensions($input->getInt('p'));

		foreach ($extensions as $e => $extension)
		{
			$result = null;

			// Clean the view name
			$viewName = preg_replace('/[^A-Z0-9_]/i','',$name);
			$classPrefix = preg_replace('/[^A-Z0-9_]/i','',$prefix);
			$viewType = preg_replace('/[^A-Z0-9_]/i','',$type);

			// Build the view class name
			$viewClassExtension = $classPrefix.$viewName.ucfirst($extension);  // adding $extension

			if (!class_exists($viewClassExtension))
			{
				jimport('joomla.filesystem.path');
				$path = Path::find($this->paths['view'],$this->createFileName('view',array(
					'name' => $viewName,
					'type' => $viewType
				)));

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

		return parent::createView($name,$prefix,$type,$config);
	}


	public static function getInstance($prefix, $config = array())
	{
		if (is_object(self::$instance))
		{
			return self::$instance;
		}

		$extensionHelper = new JoomleagueHelperExtensioncontroller();
		$extensionHelper->initExtensions();

		if (isset($config['base_path'])) {
			parent::getInstance($prefix,$config);
		} else {
			self::getInstanceWithExtensions($prefix);
		}

		$extensionHelper->addModelPaths(self::$instance);
		$extensionHelper->addViewPaths(self::$instance);

		return self::$instance;
	}


	/**
	 *
	 */
	private static function getInstanceWithExtensions($prefix)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		$extensionHelper = new JoomleagueHelperExtensioncontroller();

		// Get the environment configuration.
		$format = $input->getWord('format');
		$command = $input->getCmd('task', 'display');

		// Check for array format.
		$filter = InputFilter::getInstance();

		if (is_array($command)) {
			$command = $filter->clean(array_pop(array_keys($command)),'cmd');
		} else {
			$command = $filter->clean($command,'cmd');
		}

		$class = null;

		// Check for a controller.task command.
		// it's the only kind supported for now
		if (strpos($command,'.') !== false) {
			// Explode the controller.task command.
			list($type,$task) = explode('.',$command);

			// Define the controller filename and path.
			$file = self::createFileName('controller',array(
					'name' => $type,
					'format' => $format
			) );

			foreach ($extensionHelper->getExtensions() as $extension)
			{
				if (!$path = $extensionHelper->findFile($file,$extension))
				{
					continue;
				}

				require_once $path;

				$className = ucfirst($prefix).'Controller'.ucfirst($type);

				if (class_exists($className)) {
					$class = $className;
					break;
				} elseif (class_exists($className.ucfirst($extension)))
				{
					$class = $className.ucfirst($extension);
					break;
				} else {
					throw new InvalidArgumentException(Text::_('COM_JOOMLEAGUE_WRONG_CLASSNAME_IN_CONTROLLER_FILE'));
				}
			}

			if ($class) {
				// Reset the task without the controller context.
				$input->getCmd('task',$task);

				self::$instance = new $class();

				return self::$instance;
			}
		}

		return parent::getInstance($prefix);
	}
}
