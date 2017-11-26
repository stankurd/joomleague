<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

/**
 * HTML View class
 *
 * @author	Kurt Norgaz
 */
class JoomleagueViewJLXMLImport extends JLGView
{
	function display( $tpl = null )
	{
		// Set toolbar items for the page
		JLToolBarHelper::title(JText::_('JoomLeague XML Import'), 'generic.png');
		JLToolBarHelper::back();
		#JLToolBarHelper::save( 'save', 'Import' );
		JLToolBarHelper::help('screen.joomleague', true );

		$db		= Factory::getDbo();
		$uri	= Uri::getInstance();

		#$user = Factory::getUser();
		#$config = JFactory::getConfig();
		$config = ComponentHelper::getParams('com_media');

		#$this->user=JFactory::getUser();
		$this->request_url=$uri->toString();
		#$this->user=$user;
		$this->config=$config;

		parent::display( $tpl );
	}
}
