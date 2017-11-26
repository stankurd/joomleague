/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @note
 * functions move, moveoptionup, moveoptiondown are defined in joomleague.js
 */

window.addEvent('domready', function() {

	var moverightCount = jQuery("input[class*='move-right'").length;

	if (!moverightCount) {
		return;
	}
		
	// players - move - right
	jQuery("input[class*='pmove-right'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(10);
		var fbox = $('roster');
		var tbox = $('position' + posid);
		move(fbox,tbox);
	});
	
	// players - move - left
	jQuery("input[class*='pmove-left'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(9);
		var fbox = $('position' + posid);
		var tbox = $('roster');
		move(fbox,tbox);
	});
	
	// player - move - up
	jQuery("input[class*='pmove-up'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(7);
		moveOptionUp('position' + posid);
	});
	
	// player - move - down
	jQuery("input[class*='pmove-down'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(9);
		moveOptionDown('position' + posid);
	});


	// staff - move - right
	jQuery("input[class*='smove-right'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(11);
		var fbox = $('staff');
		var tbox = $('staffposition' + posid);
		move(fbox,tbox);
	});
	
	//  staff - move - left
	jQuery("input[class*='smove-left'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(10);
		var fbox = $('staffposition' + posid);
		var tbox = $('staff');
		move(fbox,tbox);
	});
	
	// staff - move - up
	jQuery("input[class*='smove-up'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(8);
		moveOptionUp('staffposition' + posid);
	});

	//  staff - move - down
	jQuery("input[class*='smove-down'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(10);
		moveOptionDown('staffposition' + posid);
	});
	
	// upon submit
	if (document.adminForm) {
		// on submit select all elements of select lists
		$('adminForm').addEvent('submit', function(event) {
			$$('select.position-starters').each(function(element) {
				selectAll(element);
			});

			$$('select.position-staff').each(function(element) {
				selectAll(element);
			});
		});
	}
	
	// ajax save substitution
	$$('input.button-save').addEvent('click',function() {
				
				var rowid = this.id.substr(5);
				var playerin = jQuery('#in').val();
				var playerout = jQuery('#out').val();
				var position = jQuery('#project_position_id').val();
				var time = jQuery('#in_out_time').val();
				
				var querystring = 'in=' + playerin + '&out=' + playerout
						+ '&project_position_id=' + position + '&in_out_time='
						+ time + '&teamid=' + teamid + '&matchid=' + matchid
						+ '&rowid=' + rowid;
				
				var url = baseajaxurl + '&task=match.savesubst&' + querystring;
				
				if (playerin != 0 || playerout != 0) {
					var myXhr = new Request.JSON({
						url : url,
						postBody : querystring,
						method : 'post',
						onRequest : substRequest,
						onSuccess : substSaved,
						onFailure : substFailed,
						rowid: rowid
					});
					myXhr.post();
				}
			});
	// ajax remove substitution
	$$('input.button-delete').addEvent('click', deletesubst);

});

function substRequest() {
	$('ajaxresponse').addClass('ajax-loading');
	$('ajaxresponse').innerHTML = '';
}

function deletesubst() {
	var substid = this.id.substr(7);
	var querystring = '&substid=' + substid;
	var url = baseajaxurl + '&task=match.removeSubst';
	if (substid) {
		var myXhr = new Request.JSON({
			url : url + querystring,
			method : 'post',
			onRequest : substRequest,
			onSuccess : substRemoved,
			onFailure : substFailed,
			substid: substid
		});
		myXhr.post();
	}
}

function substSaved(response) {
	$('ajaxresponse').removeClass('ajax-loading');
	var currentrow = $('row-' + this.options.rowid);
	var si_out = $('out').selectedIndex;
	var si_in  = $('in').selectedIndex;
	var si_project_position_id = $('project_position_id').selectedIndex;
	// first line contains the status, second line contains the new row.
	if (response.success) {
		// create new row in substitutions table
		var newrow = new Element('tr', {
			id : 'sub-' + response.id
		});
		if(si_out > 0) {
			new Element('td').set('text', $('out').options[si_out].text).inject(newrow);
			move($('out'), $('in'));
			$('out').selectedIndex = si_out;
			$('in').selectedIndex = si_in;
		} else {
			new Element('td').set('text', '').inject(newrow);
		}
		if(si_in > 0) {
			new Element('td').set('text', $('in').options[si_in].text).inject(newrow);
			move($('in'), $('out'));
			$('out').selectedIndex = si_out;
			$('in').selectedIndex = si_in;
		} else {
			new Element('td').set('text', '').inject(newrow);
		}
		if(si_project_position_id > 0) {
			new Element('td').set('text', $('project_position_id').options[si_project_position_id].text).inject(newrow);
		} else {
			new Element('td').set('text', '').inject(newrow);
		}
		new Element('td').set('text', $('in_out_time').value).inject(newrow);
		var deletebutton = new Element('input', {
			id : 'delete-' + response.id,
			type : 'button',
			value : str_delete
		}).addClass('inputbox button-delete').addEvent('click', deletesubst);
		var td = new Element('td').inject(newrow).appendChild(deletebutton);
		newrow.inject(currentrow, 'before');
		$('ajaxresponse').set('text', response.message);
	} else {
		$('ajaxresponse').set('text', response.message);
	}
}

function substFailed(response) {
	$('ajaxresponse').removeClass('ajax-loading');
	document.html.innerHTML = response.message || "";
}

function substRemoved(response) {
	if (response.success) {
		var currentrow = $('sub-' + this.options.substid);
		currentrow.dispose();
	}

	$('ajaxresponse').removeClass('ajax-loading');
	$('ajaxresponse').innerHTML = response.message;
}