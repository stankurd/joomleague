<?php
/**
 * Joomleague
*
* @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link			http://www.joomleague.at
*/
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;


/**
 * HTML View class
 */
class JoomleagueViewAbout extends JLGView
{

	public function display($tpl=null)
	{
		$app 		= Factory::getApplication();
		$uri		= Uri::getInstance();
		
		
		$this->request_url = $uri->toString();
		
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 */
	protected function addToolbar()
	{
	    JLToolbarHelper::title(Text::_('About'));
		JLToolbarHelper::custom('about.back','back','back','Back',false);
	}
}
