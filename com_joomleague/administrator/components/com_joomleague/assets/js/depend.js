/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * @description	javascript for dependant element xml parameter
 */


// add update of field when fields it depends on change.
jQuery('domready', function() {
	console.log('ready');
	jQuery('.mdepend').click (function() {
		// rebuild hidden field list
		var sel = [];
		var i = 0;
		this.getElements('option').each(function(el) {
			if (el.attr('selected')) {
				sel[i++] = el.val();
				console.log(sel);
			}
		});
		this.getParent().getElement('input').value = sel.join("|");
	});

	jQuery(".depend").each(function() {
		// get value of attribute "depends", can be multiple
		// create array
		var dependsArray = jQuery(this).attr('depends').split(',');
		// gets the active element
		var myelement = this;

		// gets the prefix of the current element
		var prefix = getElementIdPrefix(this);

		// Attach update_depend to the change event of all elements it depends upon,
		// so that when (one of) the dependencies change, the element is refreshed.
		jQuery.each(dependsArray, function(){
			// incoming: string, without prefix so let's attach the prefix
			var combined = '#'+String(prefix)+String(this);
			var newid = jQuery(combined);

			jQuery(combined).change(function() {
				update_depend(myelement);
				console.log(combined,myelement);
			});
		});

		// Refresh the element also after the page is loaded (to fill the element)
		load_default(myelement);
		console.log(myelement);

	});
});


// load default values
function load_default(element) {

	// the element that will be changed upon change of depend
	var combo = element;
	// prefix
	var prefix = getElementIdPrefix(element);
	// do we have a required attributed?
	//var required = element.getProperty('required') || 'false';
	var required = jQuery(this).prop('required') || 'false';

	if (required == 'true') {
		var required = "&required=true";
	}
	if (required == 'false') {
		var required = "&required=false";
	}

	//var selectedItems = combo.getProperty('current').split('|');
	//var depends = combo.getProperty('depends').split(',');
	var selectedItems = jQuery(combo).attr('current').split('|');
	var depends = jQuery(combo).attr('depends').split(',');
	var dependquery = '';
	jQuery.each(depends,function(str) {
		dependquery += '&' + this + '=' + jQuery('#' + prefix + this).val();
		console.log(depends);
		console.log(selectedItems);
		console.log(dependquery);
	});
	var loaddefault = 1;
	var task = jQuery(combo).attr('task');
	var postStr = '';
	var url = 'index.php?option=com_joomleague&format=json&task=ajax.' + task + required;
		console.log(url,task,dependquery);
	var jqXhr = jQuery.ajax({
	    url : url+ dependquery,
	    type : 'Post',
	    dataType: 'json',
		postBody : postStr,
	    success:function (data,textStatus,jqXHR) {		
			// options is equal to the response
			var options = data;
			var headingLine = null;
					console.log(textStatus);
					console.log(data.error);
			// @todo: check!
			if (jQuery(combo).attr('isrequired') == 0) {
				// In case the element is not mandatory, then first option is 'select': keep it
				// Remark : the old solution options.unshift(combo.options[0]); does not work properly
				//          It seems to result in problems in the mootools library.
				//          Therefore a different approach is taken.
				headingLine = {value: jQuery(combo).options[0].val() , text: jQuery(combo).options[0].text};
				}
			jQuery(combo).empty();

			// adding first option
			if (headingLine != null) {
				jQuery(combo).append(jQuery('option: headingLine'));
				console.log(combo,headingLine);
			}

			jQuery.each(options,function(el) {				
				
				 if (typeof el == "undefined") return;
				 if (selectedItems != null && selectedItems.indexOf(el.value) != -1) {
				 el.selected = "selected";
				 }
			
				 
				var option = jQuery('<option>');
				jQuery(option).text(this.text),jQuery(option).val(this.value);
				jQuery(combo).append(option);
				console.log(el,option);
				});
			jQuery(combo).val(selectedItems);
			jQuery(combo).trigger("chosen:updated");
			jQuery(combo).trigger("liszt:updated");
		}
		
	});
	
	
	
}



// update dependant element function
function update_depend(element) {

	// the element that will be changed upon change of depend
	var combo = element;
	// prefix
	var prefix = getElementIdPrefix(element);
	// do we have a required attributed?
	//var required = element.getProperty('required') || 'false';
	var required = jQuery(this).prop('required') || 'false';
	if (required == 'true') {
		var required = "&required=true";
	}
	if (required == 'false') {
		var required = "&required=false";
	}

	//var selectedItems = combo.getProperty('current').split('|');
	//var depends = combo.getProperty('depends').split(',');
	var selectedItems = jQuery(combo).attr('current').split('|');
	var depends = jQuery(combo).attr('depends').split(',');
	var dependquery = '';
	jQuery(depends).each(function() {
		dependquery += '&' + this + '=' + jQuery('#' + prefix + this).val();
			console.log(depends, dependquery,this, prefix);
	});

	var task = jQuery(combo).attr('task');
	var postStr = '';
	var url = 'index.php?option=com_joomleague&format=json&task=ajax.' + task
		+ required ;
		console.log(url);
	var jqXhr = jQuery.ajax({
	    url : url + dependquery,
	    type : 'Post',
	    dataType: 'json',
		postBody : postStr,
	    success:  function(data,textStatus,jqXHR) {
		console.log(data,textStatus,jqXHR);
			// options is equal to the response
			var options = data;
			var headingLine = null;

			// @todo: check!
			if (jQuery(combo).attr('isrequired') == 0) {
				// In case the element is not mandatory, then first option is 'select': keep it
				// Remark : the old solution options.unshift(combo.options[0]); does not work properly
				//          It seems to result in problems in the mootools library.
				//          Therefore a different approach is taken.
				headingLine = {value: jQuery(combo).options[0].val(), text: jQuery(combo).options[0].text};
			}
			jQuery(combo).empty();

			// adding first option
			if (headingLine != null) {
				jQuery(combo).append(jQuery('option: headingLine'));
			}

			jQuery(options).each(function(el) {				
				
				 if (typeof el == "undefined") return;
				 if (selectedItems != null && selectedItems.indexOf(el.value) != -1) {
				 el.selected = "selected";
				 }
				
				 
				var option = jQuery('<option>');
				jQuery(option).text(this.text),jQuery(option).val(this.value);
				jQuery(combo).append(option);
				});
			jQuery(combo).trigger("chosen:updated");
			jQuery(combo).trigger("liszt:updated");
		}
	});
	console.log(jqXhr);
}

/** The element IDs can be either "jform_request_" (for menu items) or "jform_params_" (for modules)
 *  This function will check if we have to do with menu items or modules, and return the right
 *  prefix to be used for element-IDs */
function getElementIdPrefix(el) {
	//var id = el.getAttribute('id');
	var id = jQuery(el).attr('id');
	console.log(id);
	var infix = jQuery(el).attr('id').replace(/^jform_(\w+)_.*$/, "$1");
		console.log(infix.match);
	return infix.match("request") ? "jform_request_" : "jform_params_";
	
}