/**
* Joomleague
 *
 * @copyright	Copyright (C) 20052016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * @description	this function copies the value of the first found form field
 * 				to all other fields with the same name
 *
 */

Joomla = window.Joomla || {};

(function() {
	'use strict';

Joomla.submitbutton = function(task) {
	var res = true;
	var validator = document.formvalidator;
	var form = jQuery('#adminForm');

	if (task == 'match.cancel') {
		Joomla.submitform(task);
		return;
	}
	if (task == 'match.saveroster') {
		Joomla.submitform(task);
		return;
	}
	if (task == 'match.saveroster2') {
		Joomla.submitform(task);
		return;
	}
	if (res) {
		Joomla.submitform(task);
	} else {
		return false;
	}
}
})();	