<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;


/**
 * About Controller
 */
class JoomleagueControllerAbout extends JoomleagueController
{
	protected $view_list = 'about';
	
	
	public function __construct()
	{
		parent::__construct();
		
		$input 	= Factory::getApplication()->input;
		$task 		= $input->getCmd('task');
	}
	
	
	public function display($cachable = false, $urlparams = false)
	{
	    $input = Factory::getApplication()->input;
		$hideMainMenu = $input->get('hidemainmenu',0);
		
		parent::display();
	}
	
	public function back()
	{
	
		$link = 'index.php?option=com_joomleague';
		$this->setRedirect($link);
	}
	
		
}
