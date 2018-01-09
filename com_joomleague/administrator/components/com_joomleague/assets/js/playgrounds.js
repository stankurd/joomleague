/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
Joomla = window.Joomla || {};

(function() {
	'use strict';
Joomla.submitbutton = function(task)
{
	document.adminForm.task.value=task;
	if (task == "playgrounds.export") {
		Joomla.submitform(task, document.getElementById("adminForm"));
		document.adminForm.task.value="";
	} else {
  		Joomla.submitform(task, document.getElementById("adminForm"));
	}
};

function searchPlayground(val, key) 
{	
	jQuery('#filter_search').val(val);
	jQuery('#adminForm').submit();
}
})();