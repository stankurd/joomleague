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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * HTML View class
 *
 * @author	Zoltan Koteles
 */
class JoomleagueViewJLXMLExport extends JLGView
{
	public function display($tpl = null)
	{
		// Set toolbar items for the page
		ToolbarHelper::title(Text::_('JoomLeague XML Export'),'generic.png');

		$db = Factory::getDbo();

		parent::display($tpl);
	}
}
