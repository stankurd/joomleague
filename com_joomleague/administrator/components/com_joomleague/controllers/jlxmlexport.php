<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;


/**
 * JLXMLExport Controller
 *
 * @author	Kurt Norgaz
 */
class JoomleagueControllerJLXMLExport extends JoomleagueController
{

	public function __construct()
	{
		parent::__construct();
	}
	
	
	function export() {
		
		$model = $this->getModel('jlxmlexport');
		$export = $model->exportData();
		
		if ($export){
			echo $export;
		}
	}

}
