/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
Joomla = window.Joomla || {};

(function(Joomla) {
	'use strict';

	document.addEventListener('DOMContentLoaded', function() {
Joomla.submitbutton = function(task)
{
	document.adminForm.task.value=task;
	if (task == "leagues.export") {
		Joomla.submitform(task, document.getElementById("adminForm"));
		document.adminForm.task.value="";
	} else {
    	Joomla.submitform(task, document.getElementById("adminForm"));
	}
};
	});

})(Joomla);
