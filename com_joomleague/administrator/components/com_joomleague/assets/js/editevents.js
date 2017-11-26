/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @note
 * JSON.stringify(response) can be used to convert object to string
 */

window.addEvent('domready', function() {
	updatePlayerSelect();
	if(jQuery('#team_id')) {
		jQuery('#team_id').change(updatePlayerSelect);

		//---- DELETE ----//
		
		// button-delete: click function for comments
		jQuery("button[class*='button-delete-c'").click(deletecomment);

		// button-delete: click function for events
		jQuery("button[class*='button-delete-e'").click(deleteevent);
		
		//---- ADD ----//

		// button-newcomment: click function
		jQuery('#save-new-comment').click(function() {
			
			// blank ajax status div
			jQuery('#ajaxresponsecomment').text('');
			
			// define variables
			var url = baseajaxurl + '&task=match.savecomment';
			var player = 0;
			var event = 0; 
			var team = 0;
			var ctype = jQuery('#ctype').val();
			var comnt = encodeURIComponent(jQuery('#comment_note').val())
			var time = jQuery('#comment_event_time').val();
			var querystring = '&teamplayer_id=' + player
				+ '&projectteam_id=' + team + '&event_type_id='
				+ event + '&event_time=' + time + '&match_id='
				+ matchid + '&ctype='
				+ ctype + '&notes='
				+ comnt;

			if (comnt == '') {
				var $label = jQuery("<label>").text('note: fill in commentary').attr({class: 'label label-warning'});
				$label.appendTo(jQuery('#ajaxresponsecomment'));
			}
			
			var validTime = 'undefined';
			
			if (time != '') {
				var checkTime2 = checkTime(time);
				if (checkTime2 != true) {
					validTime = false;
					var $label = jQuery("<label>").text('note: fill in a valid time').attr({class: 'label label-warning'});
					$label.appendTo(jQuery('#ajaxresponsecomment'));
				} else {
					validTime = true;
				}
			}
			
			if (ctype != 0 && comnt != '' && validTime != false) {
				
				jQuery('#ajaxresponsecomment').addClass('ajax-loading');
				jQuery('#ajaxresponsecomment').text('');
				
				var jqXhr = jQuery.ajax({
				    url : url + querystring,
				    type : 'POST',
				    success: commentaddsuccess,
				    error: commentadderror
				});
			}
			
		});	
			
	
		// button-newevent: click function
		jQuery('#save-new-event').click(function() {
			
			// blank ajax status div
			jQuery('#ajaxresponseevent').text('');
			
			// define variables
			var url = baseajaxurl + '&task=match.saveevent&';
			var player = jQuery('#teamplayer_id').val();
			var event = jQuery('#event_type_id').val();
			var team = jQuery('#team_id').val();
			var time = jQuery('#event_time').val();
			var notice = encodeURIComponent(jQuery('#event_notice').val());
			var querystring = 'teamplayer_id=' + player +
					'&projectteam_id=' + team +
					'&event_type_id=' + event +
					'&event_time=' + time +
					'&match_id=' + matchid +
					'&event_sum=' + $('event_sum').value +
					'&notice=' + notice
				;
			
			var validTime = 'undefined';
			
			if (time != '') {
				var checkTime2 = checkTime(time);
				if (checkTime2 != true) {
					validTime = false;
					var $label = jQuery("<label>").text('note: fill in a valid time').attr({class: 'label label-warning'});
					$label.appendTo(jQuery('#ajaxresponseevent'));
				} else {
					validTime = true;
				}
			}
			
			if (team != 0 && event != 0 && validTime != false) {
				
				jQuery('#ajaxresponseevent').addClass('ajax-loading');
				jQuery('#ajaxresponseevent').text('');
				
				var jqXhr = jQuery.ajax({
					url: url + querystring,
					type : 'POST',
					success: eventaddsuccess,
					error: eventadderror
				});
			}
		});
	}
});

									
//-------- EVENT--------//
									
/**
 * delete event
 */
function deleteevent() {
	var eventid = this.id.substr(7);
	var url = baseajaxurl + '&task=match.removeEvent';
	var querystring = '&event_id=' + eventid;
	if (eventid) {
		jQuery('#ajaxresponseevent').addClass('ajax-loading');
		jQuery('#ajaxresponseevent').text('');
		
		var jqXhr = jQuery.ajax({
			url: url + querystring,
			type : 'POST',
			success : eventdeletesuccess,
			error: eventdeleteerror,
		});
	}
}

/**
 * event-delete-success
 */
function eventdeletesuccess(data,textStatus,jqXHR) {
	
	// blank ajax response container
	jQuery('#ajaxresponseevent').removeClass('ajax-loading');
	
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;
	
	if (status) {
		// remove entry from view
		var currentrow = jQuery('#rowe-' + obj.id);
		currentrow.remove(); 
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponseevent'));	
	}
}

/**
 * event-delete-error
 */
function eventdeleteerror(xhr, status, error) {
	
	jQuery('#ajaxresponseevent').removeClass('ajax-loading');
	
	var err = eval("(" + xhr.responseText + ")");
	var $label = jQuery("<label>").text(err.Message).attr({class: 'label label-warning'});
	$label.appendTo(jQuery('#ajaxresponseevent'));	
}

/**
 * event-add-success
 */
function eventaddsuccess(data,textStatus,jqXHR) {
	
	// blank ajax response div
	jQuery('#ajaxresponseevent').removeClass('ajax-loading');
	
	// modify response
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;
	
	if (status) {
		//------ create output ------//
	
		// create new row in events table
		/* var newrow = new Element('tr', {id : 'rowe-' + obj.id}); */
		var newrow = jQuery("<tr>").attr({id: 'rowe-'+obj.id});
		
		// add td's
		newrow.append(jQuery("<td>").text(obj.id));
		newrow.append(jQuery("<td>").text(jQuery('#team_id option:selected').text()));
		newrow.append(jQuery("<td>").text(jQuery('#teamplayer_id option:selected').text()));
		newrow.append(jQuery("<td>").text(jQuery('#event_type_id option:selected').text()));
		newrow.append(jQuery("<td>").text(jQuery('#event_sum').val()).attr({class:"center"}));
		newrow.append(jQuery("<td>").text(jQuery('#event_time').val()));
		newrow.append(jQuery("<td>").text(trimstr(jQuery('#event_notice').val(), 20)).attr({title: jQuery('#event_notice').val(),class:"hasTooltip"}));
		
		// create delete-td + delete button
		var deletebutton = jQuery("<button>").attr({id : 'delete-' + obj.id,type : 'button'}).addClass('inputbox button-delete-e btn btn-small').click(deleteevent);
		deletebutton.append(jQuery("<span>").addClass("icon-delete"));
		
		var deletetd = jQuery("<td>").addClass("center");
		deletetd.append(deletebutton);		
		
		newrow.append(deletetd);
		
		// add row after the new entry row
		jQuery('#row-new-event').after(newrow);
	
		// display response message
		/* new Element('span').addClass('label').addClass('label-message').set('text','note:event created').inject($('ajaxresponseevent'),'inside');*/
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponseevent'));	
	}
}

/**
 * event-add-error
 */
function eventadderror(xhr, status, error) {
	
	jQuery('#ajaxresponseevent').removeClass('ajax-loading');
	
	var err = eval("(" + xhr.responseText + ")");
	var $label = jQuery("<label>").text(err.Message).attr({class: 'label label-warning'});
	$label.appendTo(jQuery('#ajaxresponseevent'));	
}



//---------- COMMENT --------//


/**
 * delete-comment
 */
function deletecomment() {
	var commentid = this.id.substr(7);
	var url = baseajaxurl + '&task=match.removeComment';
	var querystring = '&comment_id=' + commentid;
	if (commentid) {
		jQuery('#ajaxresponsecomment').addClass('ajax-loading');
		jQuery('#ajaxresponsecomment').text('');
		
		var jqXhr = jQuery.ajax({
			url: url + querystring,
			type : 'POST',
			success : commentdeletesuccess,
			error: commentdeleteerror,
		});
	}
}



/**
 * comment-delete-success 
 */
function commentdeletesuccess(data,textStatus,jqXHR) {
	
	// blank ajax response container
	jQuery('#ajaxresponsecomment').removeClass('ajax-loading');
	
	// modify response
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;
	
	if (status) {
		// remove entry from view
		var currentrow	= jQuery('#rowc-' + obj.id);
		currentrow.remove();
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponsecomment'));	
	}
}


/**
 * comment-delete-error
 */
function commentdeleteerror(xhr, status, error) {
	
	jQuery('#ajaxresponsecomment').removeClass('ajax-loading');
	
	var err = eval("(" + xhr.responseText + ")");
	var $label = jQuery("<label>").text(err.Message).attr({class: 'label label-warning'});
	$label.appendTo(jQuery('#ajaxresponsecomment'));	
}


/**
 * comment-add-success
 */
function commentaddsuccess(data,textStatus,jqXHR) {

	// blank ajax response container
	jQuery('#ajaxresponsecomment').removeClass('ajax-loading');

	// modify response
	var obj 	= jQuery.parseJSON(data);
	var status	= obj.success;

	if (status) {
		
		//------ create output ------//
	
		// create new row in comments table
		var newrow = jQuery("<tr>").attr({id: 'rowc-'+obj.id});

		// add td's
		newrow.append(jQuery("<td>").text(obj.id));
		newrow.append(jQuery("<td>").text(jQuery('#ctype option:selected').text()));
		newrow.append(jQuery("<td>").text(jQuery('#comment_event_time').val()));
		newrow.append(jQuery("<td>").text(jQuery('#comment_note').val()).attr({title:jQuery('#comment_note').val(),class:"hasTooltip"}));
		
		// create delete-td + delete button
		var deletebutton = jQuery("<button>").attr({id : 'delete-' + obj.id,type : 'button'}).addClass('inputbox button-delete-c btn btn-small').click(deletecomment);
		deletebutton.append(jQuery("<span>").addClass("icon-delete"));
		
		var deletetd = jQuery("<td>").addClass("center");
		deletetd.append(deletebutton);		
		
		newrow.append(deletetd);
		
		// add row before the new entry row
		jQuery('#row-new-comment').after(newrow);
	
		// display response message
		/* new Element('span').addClass('label').addClass('label-message').set('text','note:comment created').inject($('ajaxresponsecomment'),'inside');*/
	} else {
		var $label = jQuery("<label>").text(obj.message).attr({class: 'label label-warning'});
		$label.appendTo(jQuery('#ajaxresponsecomment'));	
	}
}

/**
 * comment-add-error
 */
function commentadderror(xhr, status, error) {
	jQuery('#ajaxresponsecomment').removeClass('ajax-loading');

	var err = eval("(" + xhr.responseText + ")");
	var $label = jQuery("<label>").text(err.Message).attr({class: 'label label-warning'});
	$label.appendTo(jQuery('#ajaxresponsecomment'));	
}


/**
 * updatePlayerSelect
 */
function updatePlayerSelect() {
	if(jQuery('#cell-player'))
		jQuery('#cell-player').empty().append(
			getPlayerSelect(jQuery('#team_id')[0].selectedIndex));
}


/**
 * return players select for specified team
 *
 * @param 0 for home, 1 for away
 * @return dom element
 */
function getPlayerSelect(index) {
	
	// homeroster and awayroster must be defined globally (in the view calling the script)
	var roster = rosters[index];

	// create select
	var select = jQuery("<select>").attr({id: 'teamplayer_id',class:'span12'});
	// add options
	for (var i = 0, n = roster.length; i < n; i++) {
		select.append(jQuery("<option>").attr({value : roster[i].value}).text(roster[i].text));
	}

	return select;
}


/**
 * trimstr
 */
function trimstr(str, mylength) {
	return (str.length > mylength) ? str.substr(0, mylength - 3) + '...' : str;
}

/**
 * Original JavaScript code by Chirp Internet: www.chirp.com.au
 * Please acknowledge use of this code by including this header
 * http://www.the-art-of-web.com/javascript/validate-date/
 */
function checkTime(field)
{
  var errorMsg = "";

  // regular expression to match required time format
  var re = /^(\d{1,2}):(\d{2})(:00)?([ap]m)?$/;
 
  if(field != '') {  
    if(regs = field.match(re)) {
      if(regs[4]) {
        // 12-hour time format with am/pm
        if(regs[1] < 1 || regs[1] > 12) {
          errorMsg = "Invalid value for hours: " + regs[1];
        }
      } else {
        // 24-hour time format
        if(regs[1] > 23) {
          errorMsg = "Invalid value for hours: " + regs[1];
        }
      }
      if(!errorMsg && regs[2] > 59) {
        errorMsg = "Invalid value for minutes: " + regs[2];
      }
    } else {
    	// not a valid time format
    	var onlyInt = field.match(/^\d+$/);
    	if (onlyInt) {
    		// pass
    	} else {
    		errorMsg = "Invalid time format: " + field;
    	}
    }
  }

  if(errorMsg != "") {
    return errorMsg;
  }

  return true;
}

