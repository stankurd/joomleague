/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

function handleMoveLeftToRight() {
	$('teamschanges_check').value = 1;
	move($('teamslist'), $('project_teamslist'));
	selectAll($('project_teamslist'));
}

function handleMoveRightToLeft() {
	$('teamschanges_check').value = 1;
	move($('project_teamslist'), $('teamslist'));
	selectAll($('project_teamslist'));
}