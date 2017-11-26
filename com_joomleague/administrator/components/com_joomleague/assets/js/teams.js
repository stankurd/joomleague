/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(task)
{
	document.adminForm.task.value=task;
	if (task == "teams.export") {
		Joomla.submitform(task, document.getElementById("adminForm"));
		document.adminForm.task.value="";
	} else {
     	Joomla.submitform(task, document.getElementById("adminForm"));
	}
};


function searchTeam(val, key) 
{	
	jQuery('#filter_search').val(val);
	jQuery('#adminForm').submit();
}