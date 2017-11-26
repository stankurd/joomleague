/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

jQuery(document).ready(function(){
	document.formvalidator.setHandler('date',
		function(value) {
			if (value == "") {
				return true;
			} else {
				timer = new Date();
				time = timer.getTime();
				regexp = new Array();
				regexp[time] = new RegExp(
					'^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$', 'gi');
				return regexp[time].test(value);
			}
		});
	/*
	 document.formvalidator.setHandler('matchday', function(value) {
	 if (value == "") {
	 return false;
	 } else {
	 var regexp = new RegExp('^[0-9]+$', 'gi');
	 if (!regexp.test(value)) {
	 return false;
	 } else {
	 return (getInt(value) > 0);
	 }
	 }
	 });
	 */
	document.formvalidator.setHandler('select-required', function(value) {
		return value != 0;
	});

	document.formvalidator.setHandler('time',
		function (value) {
			regex=/^[0-9]{1,2}:[0-9]{1,2}$/;
			return regex.test(value);
		});
});

Joomla.submitbutton = function(task) {
	if (task == 'project.cancel') {
		Joomla.submitform(task);
		if(window.parent.SqueezeBox) {
			window.parent.SqueezeBox.close();
		}
		return;
	}
	var form = jQuery('adminForm');
	var validator = document.formvalidator;
	
	if (validator.isValid(form)) {
		Joomla.submitform(task);
		return true;   
    }
    else {
    	var msg = new Array();
		// do field validation
		if (validator.validate(form.name) === false) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_NAME'));
		}
		if (validator.validate(form['season_id']) === false
				&& form['season_id'].disabled != true
				|| (form.seasonNew && form.seasonNew.disabled == false && form.seasonNew.value == "")) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_LEAGUE_NAME'));
		}
		if ((validator.validate(form['league_id']) === false && form['league_id'].disabled != true)
				|| (form.leagueNew && form.leagueNew.disabled == false && form.leagueNew.value == "")) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_SEASON_NAME'));
		}
		if (validator.validate(form['sports_type_id']) === false
				&& form['sports_type_id'].disabled != true
				|| (form.seasonNew && form.seasonNew.disabled == false && form.seasonNew.value == "")) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_SPORT_TYPE'));
		}
		if (form['joomleague_admin'] && form['joomleague_admin'].value === 0) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_ADMIN'));
		}
		if (form['joomleague_editor'] && form['joomleague_editor'].value === 0) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_MATCHDAY'));
		}
		if (form['start_time'] && validator.validate(form['start_time']) === false) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_MATCHTIME'));
		}
		if (form['start_date'] && validator.validate(form['start_date']) === false) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_MATCHDATE'));
		}
		if (form['current_round'] && validator.validate(form['current_round']) === false) {
			msg.push(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_PROJECT_CSJS_ERROR_MATCHDAY'));
		}
        alert (msg.join('\n'));
    }
};

function RoundAutoSwitch() {
	var form = jQuery('adminForm');
	if (form['current_round_auto'].value == 0) {
		form['current_round'].readOnly = false;
		form['auto_time'].readOnly = true;
	} else {
		form['current_round'].readOnly = true;
		form['auto_time'].readOnly = false;
	}
};