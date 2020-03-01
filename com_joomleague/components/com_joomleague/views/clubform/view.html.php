<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Language\Text;

/**
 * HTML View class
 */
class JoomleagueViewClubform extends BaseHtmlView
{
	protected $form;
	protected $item;
	protected $state;
	protected $return_page;

	public function display($tpl = null)
	{
		$app = factory::getApplication();
		$input = $app->input;
		$user = factory::getUser();
		
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');
		$this->return_page = $this->get('ReturnPage');
		
		if (empty($this->item->id))
		{
			$authorised = $user->authorise('core.create', 'com_joomleague');
		}
		else
		{
			$authorised = $this->item->params->get('access-edit');
		}
		
		if ($authorised !== true)
		{
			Error::raiseError(403, Text::_('JERROR_ALERTNOAUTHOR'));
		
			return false;
		}
		
		$extended = $this->getExtended($this->item->extended, 'club');
		$this->extended = $extended;
		
		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			Error::raiseError(500,implode("\n",$errors));
			return false;
		}
		
		parent::display($tpl);	
	}

	
	function getExtended($data='', $file, $format='ini')
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
	
		$xmlfile = JLG_PATH_ADMIN.'/assets/extended/'.$file.'.xml';
		// extension management
		$extensions = JoomleagueHelper::getExtensions($input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$JLGPATH_EXTENSION = JPATH_COMPONENT_SITE.'/extensions/'.$extension.'/admin';
			//General extension extended xml
			$file = $JLGPATH_EXTENSION.'/assets/extended/'.$file.'.xml';
			if(file_exists(Path::clean($file))) {
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
