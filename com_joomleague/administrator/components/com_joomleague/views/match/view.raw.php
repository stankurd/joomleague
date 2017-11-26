<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;


/**
 * HTML View class
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 */

class JoomleagueViewMatch extends JLGView
{
	public function display($tpl = null)
	{
		$result=Factory::getApplication()->input->get('result');
		echo $result;
	}

	function _displaySaveSubst($tpl=null)
	{
		$result=Factory::getApplication()->input->get('result');
		echo $result;
	}

}
