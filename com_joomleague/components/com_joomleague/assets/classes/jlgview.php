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

use Joomla\Registry\Registry;
use Joomla\CMS\Factory;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class JLGView extends BaseHtmlView
{
	protected $input;


	public function __construct($config = array())
	{
		parent::__construct($config);

		$app  	= Factory::getApplication();
		$input = $app->input;

		$this->input = Factory::getApplication()->input;

		// Guess the context as Option.ModelName.
		if (empty($this->context)) {
			$this->context = strtolower('com_joomleague'.'.'.$this->getName());
		}
		if($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
	}


	/**
	 * Sets an entire array of search paths for templates or resources.
	 *
	 * @access protected
	 * @param string $type The type of path to set, typically 'template'.
	 * @param string|array $path The new set of search paths.  If null or
	 * false, resets to the current directory only.
	 */
	function _setPath($type, $path)
	{
		$option     = ApplicationHelper::getComponentName();
		$app		= Factory::getApplication();
		$input		= $app->input;

		$extensions	= JoomleagueHelper::getExtensions($input->getInt('p'));
		if (!count($extensions))
		{
			return parent::_setPath($type, $path);
		}

		// clear out the prior search dirs
		$this->_path[$type] = array();

		// actually add the user-specified directories
		$this->_addPath($type, $path);

		// add extensions paths
		if (strtolower($type) == 'template')
		{
			foreach ($extensions as $e => $extension)
			{
				$JLGPATH_EXTENSION =  JPATH_COMPONENT_SITE.'/extensions/'.$extension;

				// set the alternative template search dir
				if (isset($app))
				{
					if ($app->isClient('administrator')) {
						$this->_addPath('template', $JLGPATH_EXTENSION.'/admin/views/'.$this->getName().'/tmpl');
					}
					else {
						$this->_addPath('template', $JLGPATH_EXTENSION.'/views/'.$this->getName().'/tmpl');
					}

					// always add the fallback directories as last resort
					$option = preg_replace('/[^A-Z0-9_\.-]/i', '', $option);
					$fallback = JPATH_THEMES.'/'.$app->getTemplate().'/html/'.$option.'/'.$extension.'/'.$this->getName();
					$this->_addPath('template', $fallback);
				}
			}
		}
	}

	public function display($tpl = null )
	{
		$app		= Factory::getApplication();
		$input		= $app->input;
		$option 	= $input->get('option');
		$document	= Factory::getDocument();
		$version 	= urlencode(JoomleagueHelper::getVersion());
		//$baseurl    = Uri::root('true');
		
		// support for global client side lang res
		//JHtml::_('behavior.formvalidator');
		// Load the modal behavior script.
		//JHtml::_('behavior.modal', 'a.modal');

		$lang 		= Factory::getLanguage();
		$jllang 	= new JLLanguage();
		$jllang->setLanguage($lang);

		$props 		= $jllang->getProperties();
		$strings 	= $props['strings'];
		foreach ($strings as $key => $value) {
			if($app->isClient('administrator')) {
				if(strpos($key, 'COM_JOOMLEAGUE_ADMIN_'.strtoupper($this->getName()).'_CSJS') !== false) {
					JText::script($key, true);
				}
			} else {
				if(strpos($key, 'COM_JOOMLEAGUE_'.strtoupper($this->getName()).'_CSJS_')  !== false) {
					JText::script($key, true);
				}
			}
		}

		if ($app->isAdmin()) {
			// include backend.css
			$file = JPATH_COMPONENT.'/assets/css/backend.css';
			if(file_exists(JPath::clean($file))) {
				$document->addStyleSheet($this->baseurl.'/components/'.$option.'/assets/css/backend.css?v='.$version);
			}
		} else {
			// General Joomleague CSS include
			$file = JPATH_COMPONENT.'/assets/css/joomleague.css';
			if(file_exists(JPath::clean($file))) {
				$document->addStyleSheet($this->baseurl.'/components/'.$option.'/assets/css/joomleague.css?v='.$version);
			}
		}
		// Genereal CSS include per view
		$file = JPATH_COMPONENT.'/assets/css/'.$this->getName().'.css';
		if(file_exists(JPath::clean($file))) {
			//add css file
			$document->addStyleSheet($this->baseurl.'/components/'.$option.'/assets/css/'.$this->getName().'.css?v='.$version);
		}
		// General Joomleague JS include
		$file = JPATH_COMPONENT.'/assets/js/joomleague.js';
		if(file_exists(JPath::clean($file))) {
			$js = $this->baseurl.'/components/'.$option.'/assets/js/joomleague.js?v='.$version;
			$document->addScript($js);
		}
		// General JS include per view
		$file = JPATH_COMPONENT.'/assets/js/'.$this->getName().'.js';
		if(file_exists(JPath::clean($file))) {
			$js = $this->baseurl.'/components/'.$option.'/assets/js/'.$this->getName().'.js?v='.$version;
			$document->addScript($js);
		}

		// extension management
		$extensions = JoomleagueHelper::getExtensions($input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$JLGPATH_EXTENSION =  JPATH_COMPONENT_SITE.'/extensions/'.$extension;

			// General extension CSS include
			$file = $JLGPATH_EXTENSION.'/assets/css/'.$extension.'.css';
			if(file_exists(JPath::clean($file))) {
				$document->addStyleSheet($this->baseurl.'/components/'.$option.'/extensions/'.$extension.'/assets/css/'.$extension.'.css?v='.$version);
			}
			// CSS override
			$file = $JLGPATH_EXTENSION.'/assets/css/'.$this->getName().'.css';
			if(file_exists(JPath::clean($file))) {
				// add css file
				$document->addStyleSheet($this->baseurl.'/components/'.$option.'/extensions/'.$extension.'/assets/css/'.$this->getName().'.css?v='.$version);
			}
			// General extension JS include
			$file = $JLGPATH_EXTENSION.'/assets/js/'.$extension.'.js';
			if(file_exists(JPath::clean($file))) {
				// add js file
				$document->addScript(  $this->baseurl . '/components/'.$option.'/extensions/'.$extension.'/assets/js/'.$extension.'.js?v='.$version);
			}
			// JS override
			$file = $JLGPATH_EXTENSION.'/assets/js/'.$this->getName().'.js';
			if(file_exists(JPath::clean($file))) {
				// add js file
				$document->addScript($this->baseurl.'/components/'.$option.'/extensions/'.$extension.'/assets/js/'.$this->getName().'.js?v='.$version);
			}

			// Only for admin side
			if($app->isClient('administrator')) {
				$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/admin';

				// General extension CSS include
				$file = $JLGPATH_EXTENSION.'/assets/css/'.$extension.'.css';
				if(file_exists(JPath::clean($file))) {
					$document->addStyleSheet($this->baseurl.'/../components/'.$option.'/extensions/'.$extension.'/admin/assets/css/'.$extension.'.css?v='.$version);
				}
				// CSS override
				$file = $JLGPATH_EXTENSION.'/assets/css/'.$this->getName().'.css';
				if(file_exists(JPath::clean($file))) {
					// add css file
					$document->addStyleSheet($this->baseurl.'/../components/'.$option.'/extensions/'.$extension.'/admin/assets/css/'.$this->getName().'.css?v='.$version);
				}
				// General extension JS include
				$file = $JLGPATH_EXTENSION.'/assets/js/'.$extension.'.js';
				if(file_exists(JPath::clean($file))) {
					// add js file
					$document->addScript($this->baseurl.'/../components/'.$option.'/extensions/'.$extension.'/admin/assets/js/'.$extension.'.js?v='.$version);
				}
				// JS override
				$file = $JLGPATH_EXTENSION.'/assets/js/'.$this->getName().'.js';
				if(file_exists(JPath::clean($file))) {
					// add js file
					$document->addScript($this->baseurl.'/../components/'.$option.'/extensions/'.$extension.'/admin/assets/js/'.$this->getName().'.js?v='.$version);
				}
			}
		}
		parent::display($tpl);
	}


	/**
	 * support for extensions which can overload extended data
	 * @param string $data
	 * @param string $file
	 * @return object
	 */
	function getExtended($data = '', $file, $projectId = null)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;

		$xmlfile = JLG_PATH_ADMIN.'/assets/extended/'.$file.'.xml';

		// extension management
		$extensions = JoomleagueHelper::getExtensions($projectId ?: $input->getInt('p'));

		foreach ($extensions as $e => $extension) {
			$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/admin';
			//General extension extended xml
			$file = $JLGPATH_EXTENSION.'/assets/extended/'.$file.'.xml';
			if(file_exists(JPath::clean($file))) {
				$xmlfile = $file;
				break; //first extension file will win
			}
		}

		if (is_array($data)) {
			$data = json_encode($data);
		}

		// Convert the extended field to an array.
		$registry = new Registry;
		$registry->loadString($data);

		/*
		 * extended data
		*/
		$extended = Form::getInstance('extended', $xmlfile,array('control'=> 'extended'),false);
		$extended->bind($registry);

		return $extended;
	}
}
