/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(pressbutton) {
	var res = true;
	var validator = document.formvalidator;
	var form = jQuery('#adminForm');

	if (pressbutton == 'person.cancel') {
		Joomla.submitform(pressbutton);
		if(window.parent.SqueezeBox) {
			window.parent.SqueezeBox.close();
		}
		return;
	}

	// do field validation
	if (validator.validate(form.lastname) === false) {
		alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PERSON_CSJS_NO_NAME'));
		res = false;
	}
	if (res) {
		Joomla.submitform(pressbutton);
	} else {
		return false;
	}
}

function projectSelected() {
	var adminForm = window.top.document.forms.adminForm; 
	adminForm.elements.project_id.value 	= $('prjid').getSelected().get('value');
	adminForm.elements.project_name.value 	= $('prjid').getSelected().get('text');
	adminForm.elements.team_id.value 		= $('xtid').getSelected().get('value');
	adminForm.elements.team_name.value 		= $('xtid').getSelected().get('text');
	adminForm.elements.assignperson.value 	= '1';
	window.parent.SqueezeBox.close();
}
