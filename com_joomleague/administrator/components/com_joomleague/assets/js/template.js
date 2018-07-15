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
	var form = jQuery('#adminForm')
	if (task == 'template.cancel') {
		Joomla.submitform(task);
		return;
	}

	// do field validation
	if (validator.validate(form)) {
		Joomla.submitform(task);
		res = false;
		}
	if (res) {
	if (document.formvalidator.isValid(form)) {
		Joomla.submitform(task);
		return true;
	} else {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_TEMPLATE_CSJS_WRONG_VALUES'));
	}
	return false;
}
})();