/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(task) {
	if (task == 'position.cancel') {
		Joomla.submitform(task);
		return;
	}
	var form = jQuery('#adminForm');
	var validator = document.formvalidator;
	
	if (validator.isValid(form)) {
		var mylist = jQuery('#position_eventslist');
		for ( var i = 0; i < mylist.length; i++) {
			mylist[i].selected = true;
		}
		var mylist = jQuery('#position_statistic');
		for ( var i = 0; i < mylist.length; i++) {
			mylist[i].selected = true;
		}
		Joomla.submitform(task);
		return true;   
    }
    else {
    	var msg = new Array();
		// do field validation
		if (validator.validate(form.name) === false) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_POSITION_CSJS_NEEDS_NAME'));
		}
		if (validator.validate(form['sports_type_id']) === false
				&& form['sports_type_id'].disabled != true) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_POSITION_CSJS_NEEDS_SPORTSTYPE'));
		}
        alert (msg.join('\n'));
    }
};
