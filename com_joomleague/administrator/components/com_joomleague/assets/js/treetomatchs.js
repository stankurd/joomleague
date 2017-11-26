/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

Joomla.submitbutton = function(task) {
	var form = jQuery('adminForm');
	if(jQuery('node_matcheslist')) {
		var mylist = jQuery('node_matcheslist');
		for ( var i = 0; i < mylist.length; i++) {
			mylist[i].selected = true;
		}
	}
	Joomla.submitform(task);
}

function handleLeftToRight() {
	jQuery('matcheschanges_check').value = 1;
	move(jQuery('matcheslist'), jQuery('node_matcheslist'));
	selectAll(jQuery('node_matcheslist'));
}

function handleRightToLeft() {
	jQuery('matcheschanges_check').value = 1;
	move(jQuery('node_matcheslist'), jQuery('matcheslist'));
	selectAll(jQuery('node_matcheslist'));
}
