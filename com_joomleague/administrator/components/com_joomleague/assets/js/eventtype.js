/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(task) {
	var res = true;
	var validator = document.formvalidator;
	var form = jQuery('#adminForm');
	
	if (task == 'eventtype.cancel') {
		Joomla.submitform(task);
		return;
	}

	// do field validation
	if (validator.validate(form.name) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_EVENTTYPE_CSJS_NAME_REQUIRED'));
		form.name.focus();
		res = false;
	}
	
	if (res) {
		Joomla.submitform(task);
	} else {
		return false;
	}
};

function updateEventIcon(path) {
	var icon = jQuery('#image');
	icon.src = '<?php echo JUri::root(); ?>' + path;
	icon.alt = path;
	icon.value = path;
	var logovalue = jQuery('#icon');
	logovalue.value = path;
}
