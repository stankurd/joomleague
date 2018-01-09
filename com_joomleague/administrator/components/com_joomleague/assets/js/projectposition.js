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
	var form = jQuery('adminForm');
	if (task == 'projectposition.cancel') {
		Joomla.submitform(task);
		return;
	}
	if(jQuery('project_positionslist')) {
		var mylist = jQuery('project_positionslist');
		for ( var i = 0; i < mylist.length; i++) {
			mylist[i].selected = true;
		}
	}
	Joomla.submitform(task);
}

function handleLeftToRight() {
	jQuery('positionschanges_check').value = 1;
	move(jQuery('positionslist'), jQuery('project_positionslist'));
	selectAll(jQuery('project_positionslist'));
}

function handleRightToLeft() {
	jQuery('positionschanges_check').value = 1;
	move(jQuery('project_positionslist'), jQuery('positionslist'));
	selectAll(jQuery('project_positionslist'));
}
})();