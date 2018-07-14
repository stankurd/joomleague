<?php // no direct access

use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\Filesystem\Path;
use Joomla\String\Normalise;

defined( '_JEXEC' ) or die( 'Restricted access' ); 
 
class JoomleagueModelDefault extends JModelBase
{
	function __construct()
	{
		$app = Factory::getApplication();
		$component = ApplicationHelper::getComponentName();
		$component = preg_replace('/[^A-Z0-9_\.-]/i', '', $component);
	
		if (isset($paths))
		{
			$paths->insert(JPATH_THEMES . '/' . $app->getTemplate() . '/html/' . $component . '/' . $this->getName(), 2);
		}
	
		parent::__construct();
	}



/**
 * Modifies a property of the object, creating it if it does not already exist.
 *
 * @param   string  $property  The name of the property.
 * @param   mixed   $value     The value of the property to set.
 *
 * @return  mixed  Previous value of the property.
 *
 * @since   11.1
 */
public function set($property, $value = null)
{
	$previous = isset($this->$property) ? $this->$property : null;
	$this->$property = $value;

	return $previous;
}

public function get($property, $default = null)
{
	return isset($this->$property) ? $this->$property : $default;
}

/**
 * Build a query, where clause and return an object
 *
 */



/**
 * Build query and where for protected _getList function and return a list
 *
 * @return array An array of results.
 */
public function listItems()
{
	$query = $this->_buildQuery();
	$query = $this->_buildWhere($query);

	$list = $this->_getList($query, $this->limitstart, $this->limit);

	return $list;
}

/**
 * Gets an array of objects from the results of database query.
 *
 * @param   string   $query       The query.
 * @param   integer  $limitstart  Offset.
 * @param   integer  $limit       The number of records.
 *
 * @return  array  An array of results.
 *
 * @since   11.1
 */
protected function _getList($query, $limitstart = 0, $limit = 0)
{
	$db = Factory::getDBO();
	$db->setQuery($query, $limitstart, $limit);
	$result = $db->loadObjectList();

	return $result;
}

/**
 * Returns a record count for the query
 *
 * @param   string  $query  The query.
 *
 * @return  integer  Number of rows for query
 *
 * @since   11.1
 */
protected function _getListCount($query)
{
	$db = Factory::getDBO();
	$db->setQuery($query);
	$db->execute();

	return $db->getNumRows();
}

/* Method to get model state variables
 *
* @param   string  $property  Optional parameter name
* @param   mixed   $default   Optional default value
*
* @return  object  The property where specified, the state object where omitted
*
* @since   11.1
*/
public function getState($property = null, $default = null)
{
	if (!$this->__state_set)
	{
		// Protected method to auto-populate the model state.
		$this->populateState();

		// Set the model state set flag to true.
		$this->__state_set = true;
	}

	return $property === null ? $this->state : $this->state->get($property, $default);
}

/**
 * Get total number of rows for pagination
 */
function getTotal()
{
	if ( empty ( $this->_total ) )
	{
		$query = $this->_buildQuery();
		$this->_total = $this->_getListCount($query);
	}

	return $this->_total;
}

/**
 * Generate pagination
 */
function getPagination()
{
	// Lets load the content if it doesn't already exist
	if (empty($this->_pagination))
	{
		$this->_pagination = new Pagination( $this->getTotal(), $this->getState($this->_view.'_limitstart'), $this->getState($this->_view.'_limit'),null,Route::_('index.php?view='.$this->_view.'&layout='.$this->_layout));
	}

	return $this->_pagination;
}

protected $_output = null;

protected $_template = null;

protected $_path = array('template' => array(), 'helper' => array());

protected $_layoutExt = 'php';



public function loadTemplate($tpl = null)
{
	// Clear prior output
	$this->_output = null;

	$template = Factory::getApplication()->getTemplate();
	$layout = $this->getLayout();

	// Create the template file name based on the layout
	$file = isset($tpl) ? $layout . '_' . $tpl : $layout;

	// Clean the file name
	$file = preg_replace('/[^A-Z0-9_\.-]/i', '', $file);
	$tpl = isset($tpl) ? preg_replace('/[^A-Z0-9_\.-]/i', '', $tpl) : $tpl;

	// Load the language file for the template
	$lang = Factory::getLanguage();
	$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
	|| $lang->load('tpl_' . $template, JPATH_THEMES . "/$template", null, false, true);

	// Prevents adding path twise
	if (empty($this->_path['template']))
	{
		// Adding template paths
		$this->paths->top();
		$defaultPath = $this->paths->current();
		$this->paths->next();
		$templatePath = $this->paths->current();
		$this->_path['template'] = array($defaultPath, $templatePath);
	}

	// Load the template script
	jimport('joomla.filesystem.path');
	$filetofind = $this->_createFileName('template', array('name' => $file));
	$this->_template = Path::find($this->_path['template'], $filetofind);

	// If alternate layout can't be found, fall back to default layout
	if ($this->_template == false)
	{
		$filetofind = $this->_createFileName('', array('name' => 'default' . (isset($tpl) ? '_' . $tpl : $tpl)));
		$this->_template = Path::find($this->_path['template'], $filetofind);
	}

	if ($this->_template != false)
	{
		// Unset so as not to introduce into template scope
		unset($tpl);
		unset($file);

		// Never allow a 'this' property
		if (isset($this->this))
		{
			unset($this->this);
		}

		// Start capturing output into a buffer
		ob_start();

		// Include the requested template filename in the local scope
		// (this will execute the view logic).
		include $this->_template;

		// Done with the requested template; get the buffer and
		// clear it.
		$this->_output = ob_get_contents();
		ob_end_clean();

		return $this->_output;
	}
	else
	{
		throw new Exception(Text::sprintf('JLIB_APPLICATION_ERROR_LAYOUTFILE_NOT_FOUND', $file), 500);
	}
}

protected function _createFileName($type, $parts = array())
{
	$filename = '';

	switch ($type)
	{
		case 'template':
			$filename = strtolower($parts['name']) . '.' . $this->_layoutExt;
			break;

		default:
			$filename = strtolower($parts['name']) . '.php';
			break;
	}

	return $filename;
}

public function getName()
{
	if (empty($this->_name))
	{
		$classname = get_class($this);
		$viewpos = strpos($classname, 'View');
		if ($viewpos === false)
		{
			throw new Exception(Text::_('JLIB_APPLICATION_ERROR_VIEW_GET_NAME'), 500);
		}

		$lastPart = substr($classname, $viewpos + 4);
		$pathParts = explode(' ', Normalise::fromCamelCase($lastPart));

		if (!empty($pathParts[1]))
		{
			$this->_name = strtolower($pathParts[0]);
		}
		else
		{
			$this->_name = strtolower($lastPart);
		}
	}

	return $this->_name;
}

}

