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

	jQuery('domready', function() {
	var moverightCount = jQuery("input[class*='move-right'").length;
	if (!moverightCount) {
		return;
	}
	// players - move - right
	jQuery("input[class*='pmove-right'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(10);
		var fbox = document.getElementById('roster');
		var tbox = document.getElementById('position' + posid);
		move (fbox,tbox);
	});
	
	// players - move - left
	jQuery("input[class*='pmove-left'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(9);
		var fbox = document.getElementById('position' + posid);
		var tbox = document.getElementById('roster');
		move (fbox,tbox);
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
		var fbox = document.getElementById('staff');
		var tbox = document.getElementById('staffposition' + posid);
		move (fbox,tbox);
	});
	
	//  staff - move - left
	jQuery("input[class*='smove-left'").click(function() {
		jQuery('#changes_check').val(1);
		var posid = this.id.substr(10);
		var fbox = document.getElementById('staffposition' + posid);
		var tbox = document.getElementById('staff');
		move (fbox,tbox);
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
		jQuery('#adminForm').submit (function(event) {
		 var choice = jQuery('*').val();
			if(!choice) {	
            jQuery('*').attr('selected','selected');			
			}	
			});
		};

		//---- DELETE ----//
		
		// button-delete: click function for comments
		jQuery("button[class*='button-delete'").click(deletesubst);

	// ajax save substitution
	jQuery('input.button-save').click (function() {
			// blank ajax status div
			jQuery('#ajaxresponse').text('');
				// define variables
				var rowid = this.id.substr(5);
				var playerin = jQuery('#in').val();
				var playerout = jQuery('#out').val();
				var position = jQuery('#project_position_id').val();
				var time = jQuery('#in_out_time').val();
				
				var querystring = 'in=' + playerin + '&out=' + playerout
						+ '&project_position_id=' + position + '&in_out_time='
						+ time + '&teamid=' + teamid + '&matchid=' + matchid
						+ '&rowid=' + rowid;
				var url = baseajaxurl + '&task=match.savesubst&';
				
				if (playerin != 0 || playerout != 0) {
					var jqXhr = jQuery.ajax({
						url : url + querystring,
						method : 'Post',
						success : substSaved,
						error : substFailed,
						rowid: rowid
					});
				}
			});
	// ajax remove substitution
	jQuery('input.button-delete').click (deletesubst);

});

function substRequest() {
	jQuery('#ajaxresponse').addClass('ajax-loading');
	jQuery('#ajaxresponse').innerHTML = '';
}

function deletesubst() {
	var substid = this.id.substr(7);
	var querystring = '&substid=' + substid;
	var url = baseajaxurl + '&task=match.removeSubst';
	if (substid) {
		var jqXhr = jQuery.ajax({
			url : url + querystring,
			method : 'Post',
			success : substRemoved,
			error : substFailed,
			substid: substid
		});
	}
}

function substSaved(data,textStatus,jqXHR) {
	jQuery('#ajaxresponse').removeClass('ajax-loading');
	var currentrow = document.getElementById('row-' + this.rowid);
	var si_out = document.getElementById('out').selectedIndex;
	var si_in  = document.getElementById('in').selectedIndex;
	var si_project_position_id = document.getElementById('project_position_id').selectedIndex;
	
	// modify response
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;
	if (status) {
		//------ create output ------//
	jQuery('#ajaxresponse').removeClass('ajax-loading');
	// first line contains the status, second line contains the new row.
	
		var newrow = jQuery("<tr>").attr({id: 'sub-' + obj.id});
		if(si_out > 0) {
			newrow.append(jQuery("<td>").text(jQuery('#out option:selected').text()));
			move(document.getElementById('out'), document.getElementById('in'));
			document.getElementById('out').selectedIndex = si_out;
			document.getElementById('in').selectedIndex = si_in;
		} else {
			newrow.appendTo(jQuery("<td>").text(''));
		}
		if(si_in > 0) {
		newrow.append(jQuery("<td>").text(jQuery('#in option:selected').text()));
			move(document.getElementById('in'), document.getElementById('out'));
			document.getElementById('out').selectedIndex = si_out;
			document.getElementById('in').selectedIndex = si_in;
		} else {
			newrow.appendTo(jQuery("<td>").text(''));
		}
		if(si_project_position_id > 0) {
		newrow.append(jQuery("<td>").text(jQuery('#project_position_id option:selected').text()));
		} else {
		newrow.appendTo(jQuery("<td>").text(''));
		}
		newrow.append(jQuery("<td>").text( jQuery('#in_out_time').val()));
		
		// create delete-td + delete button
		var deletebutton = jQuery("<button>").attr({id : 'delete-' + obj.id,type : 'button'}).addClass('inputbox button-delete-e btn btn-small').click(deletesubst);
		deletebutton.append(jQuery("<col-md->").addClass("icon-delete"));
		
		var deletetd = jQuery("<td>").addClass("center");
		deletetd.append(deletebutton);		
		
		newrow.append(deletetd);
		
		// add row after the new entry row
		jQuery('#row-new').before(newrow);
	// display response message
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-message'});
		$label.appendTo(jQuery('#ajaxresponse'));	
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponse'));	
	}
}

function substFailed(xhr, status, error) {
	jQuery('#ajaxresponse').removeClass('ajax-loading');
	var err = eval("(" + xhr.responseText + ")");
	var $label = jQuery("<label>").text(err.Message).attr({class: 'label label-warning'});
	$label.appendTo(jQuery('#ajaxresponse'));	
}

function substRemoved(data,textStatus,jqXHR) {
	// blank ajax response container
	jQuery('#ajaxresponse').removeClass('ajax-loading');
	
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;
	
	if (status) {
		// remove entry from view
		var currentrow = jQuery('#sub-' + this.substid);
		currentrow.remove();
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponse'));
	}		
}