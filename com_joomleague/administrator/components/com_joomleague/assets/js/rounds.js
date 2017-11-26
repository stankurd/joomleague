/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

window.addEvent('domready',function(){
	if($('populate_enter_division')) {
		$('populate_enter_division').hide();
		$$('table.adminlist tr').each(function(el){
			var cb;
			if (cb=el.getElement("input[name^=cid]")) {
				el.getElement("input[name^=roundcode]").addEvent('change',function(){
					if (isNaN(this.value)) {
						alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_ROUNDS_CSJS_MSG_NOTANUMBER'));
						return false;
					}
				});
			}
		});
	}
	if($('buttonup')) {
		$('buttonup').addEvent('click', function(){
			moveOptionUp('teamsorder');
		});
		$('buttondown').addEvent('click', function(){
			moveOptionDown('teamsorder');
		});
	}
});

Joomla.submitbutton = function(pressbutton) {
	var ret = true;
	var validator = document.formvalidator;
	var form = $('adminForm');

	if (pressbutton == 'rounds.populate') {
		if($('populate_enter_division')) {
			$('populate_enter_division').show();
			ret = false;
		}
	}

	if(pressbutton == 'rounds.startpopulate') {
		$('teamsorder').getElements('option').each(function(el) {
			el.setProperty('selected', 'selected');
		});
		Joomla.submitform(pressbutton);
		return;
	}

	if (ret) {
		Joomla.submitform(pressbutton);
	} else {
		return false;
	}
}


function updateAddRoundMethod(element) {
	$('interval_method').style.display = (element.value == 0) ? 'block' : 'none';
}


/**
 * show/hide the teams sort list
 * 
 * @param element
 * @returns {Boolean}
 */
//function handleOnChange_scheduling(element) {
//	var show = false;
//	$(element).getElements('option').each(function(el) {
//		if(el.selected && (el.value == "0"  || el.value == "1")) {
//			show = true;
//		}
//	});
//	if(show) {
//		$("sortable_teams").show();
//	} else {
//		$("sortable_teams").hide();	
//	}
//	return true;
//}
//);