/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

$(document).ready(function() {
	// check row box when a value is updated
	$('tr.statrow').each(function(row){
		row.getElements('.stat').each(function(stat){
			stat.addEvent('change', function(){
				row.getElement('.statcheck').setProperty('checked', 'true');
			});
		});
	});

	// check row box when a value is updated
	$('tr.staffstatrow').each(function(row){
		row.getElements('.staffstat').each(function(stat){
			stat.addEvent('change', function(){
				row.getElement('.staffstatcheck').setProperty('checked', 'true');
			});
		});
	});
});