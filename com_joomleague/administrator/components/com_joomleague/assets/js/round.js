/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(task) {
	var res = true;
	var validator = document.formvalidator;
	var form = $('adminForm');

	if (task == 'round.cancel') {
		Joomla.submitform(task);
		return;
	}

	// do field validation

	if (validator.validate(form.roundcode) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_ROUND_CSJS_NO_ROUNDCODE'));
		res = false;
	}
	else if (validator.validate(form.name) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_ROUND_CSJS_NO_NAME'));
		res = false;
	}
	
	if (res) {
		Joomla.submitform(task);
	} else {
		return false;
	}
}