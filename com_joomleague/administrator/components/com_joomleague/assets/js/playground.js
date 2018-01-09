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
Joomla.submitbutton = function(task) {
	var res = true;
	var validator = document.formvalidator;
	var form = jQuery('#adminForm');

	if (task == 'playground.cancel') {
		Joomla.submitform(task);
		return;
	}

	// do field validation
	if (validator.validate(form.name) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_CSJS_NO_NAME'));
		form.name.focus();		
		res = false;
	} else if (validator.validate(form.short_name) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PLAYGROUND_CSJS_NO_S_NAME'));
		form.short_name.focus();		
		res = false;
	} 
	
	if (res) {
		Joomla.submitform(task);
	} else {
		return false;
	}	
}
})();