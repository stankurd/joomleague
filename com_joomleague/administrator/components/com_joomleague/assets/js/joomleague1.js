/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

function joomleague_changedoc(docid){
  if (docid != "" && docid.options[docid.options.selectedIndex].value!="") {
    window.location.href = docid.options[docid.options.selectedIndex].value;
  }
}

/**
 * toggle object visibility
 * @param obj the object to show/hide
 */       
function visibleMenu(obj) {
	var joomleague_el = document.getElementById(obj);
	if ( joomleague_el.style.visibility != "hidden" ) {
		joomleague_el.style.visibility = 'hidden';
	}
	else {
		joomleague_el.style.visibility = 'visible';
	}
}

function switchMenu(obj) {
	var joomleague_el = document.getElementById(obj);
	if ( joomleague_el.style.display != "none" ) {
		joomleague_el.style.display = 'none';
	}
	else {
		joomleague_el.style.display = 'block';
	}
}

/**
 * hide objects
 * @param array objs the objects to hide
 */
function collapseAll(objs) {
  var i;
  for (i=0;i<objs.length;i++ ) {
    objs[i].style.display = 'none';
  }
}

//-------------------------------------------------------------------
//hasOptions(obj)
//Utility function to determine if a select box has an options array
//-------------------------------------------------------------------
function hasOptions(box) {
	if (box!=null && box.options!=null) { return true; }
	return false;
	}

//aktualnie używa mootools
function move(fbox, tbox) {
	var arrFbox = [];//tablica lista zawodników minus zawodnik aktualnie przesunięty do składu wyjściowego
	var arrTbox = [];// tablica lista zawodników przypisanych do składu na poszczegolnych pozycjach
	var arrLookup = [];//tablica lista zawodników plus value personid
	//pętla podlicza aktualna listę zawodnikoów przypisanych do poszczególnych pozycji arrTbox
	//arrLookup zlicza aktualna ilość dostępnych zwodnikow
	var i;//zmienna przyjmuje aktualna wartość wg dostepnych pozycji wyboru np zawodnikow
	for (i = 0; i < tbox.length; i++) {
		arrLookup[tbox[i].text] = tbox[i].value;
		arrTbox[i] = tbox[i].text;
		console.dir(arrLookup);
		console.dir(arrTbox);
		//console.log(i);
	}
	var fLength = 0;//zmienna przechowuje liczbę dostepnych elementów podaje tylko aktualną liczbę dostepnych elementów aktualizowana po każdym wykonaniu petli
	var tLength = arrTbox.length // zmienna zlicza ilość elementów po przesunieciu na poszczególne pozycje po wykonaniu petli
	for (i = 0; i < fbox.length; i++) {
		//petla wykonuje podliczenia dla zmiennej 
		arrLookup[fbox[i].text] = fbox[i].value;//tablica podlicza aktualnie dostępną liste zawodnikow i dodaje personid po wykonaniu pętli
		if (fbox[i].selected && fbox[i].value != "") {
			arrTbox[tLength] = fbox[i].text;
			tLength++;
			//petla podlicza wartość dla zmiennej 
		} else {
			arrFbox[fLength] = fbox[i].text;
			fLength++;
		}
		//console.log(tLength);
		//console.log(fLength);
		//console.dir(arrLookup);
	}
	fbox.length = 0;//Lista wyboru select#roster.inputbox tworzy kolekcje fbox zmiennej c
	tbox.length = 0;//lista wybranych elementów po przesunięciu do tbox dla poszczegolnych pozycji
	var c; //zmienna c przechowuje liczbę dostępnych elementów
	for (c = 0; c < arrFbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrFbox[c]];
		no.text = arrFbox[c];
		fbox[c] = no;
		//console.log(fbox);
		//console.log(c);

	}
	//petla for tworzy kolekcje HTML option dla klasy "inputbox position-starters" dla poszczególnych id np id: "position3"
	
	for (c = 0; c < arrTbox.length; c++) {
		var no = new Option();
		no.value = arrLookup[arrTbox[c]];
		no.text = arrTbox[c];
		tbox[c] = no;
		//console.dir(tbox);

	}
	//console.dir(move);
};

function selectAll(box) {
	if (!hasOptions(box)) { return; }
	for (var i=0; i<box.options.length; i++) {
		box.options[i].selected = true;
		}
	}
/*
function selectAll(box) {
	//var box = [];
	for ( var i = 0; i < box.length; i++) {
		box[i].selected = true;
		console.log(box);
	}
};
*/

// ===================================================================
// Author: Matt Kruse <matt@mattkruse.com>
// WWW: http://www.mattkruse.com/
//
// NOTICE: You may use this code for any purpose, commercial or
// private, without any further permission from the author. You may
// remove this notice from your final code if you wish, however it is
// appreciated by the author if at least my web site address is kept.
//
// You may *NOT* re-distribute this code in any way except through its
// use. That means, you can include it in your product, or your web
// site, or any other form where the code is actually being used. You
// may not put the plain javascript up on your site for download or
// include it in your javascript libraries for download. 
// If you wish to share this code with others, please just point them
// to the URL instead.
// Please DO NOT link directly to my .js files from your site. Copy
// the files to your server and use them there. Thank you.
// ===================================================================

// HISTORY
// ------------------------------------------------------------------
// April 20, 2005: Fixed the removeSelectedOptions() function to 
//                 correctly handle single selects
// June 12, 2003: Modified up and down functions to support more than
//                one selected option
/*
DESCRIPTION: These are general functions to deal with and manipulate
select boxes. Also see the OptionTransfer library to more easily 
handle transferring options between two lists

COMPATABILITY: These are fairly basic functions - they should work on
all browsers that support Javascript.
*/

/*
// -------------------------------------------------------------------
// hasOptions(obj)
//  Utility function to determine if a select object has an options array
// -------------------------------------------------------------------
function hasOptions(obj) {
	if (obj!=null && obj.options!=null) { return true; }
	return false;
	}

// -------------------------------------------------------------------
// selectUnselectMatchingOptions(select_object,regex,select/unselect,true/false)
//  This is a general function used by the select functions below, to
//  avoid code duplication
// -------------------------------------------------------------------
function selectUnselectMatchingOptions(obj,regex,which,only) {
	if (window.RegExp) {
		if (which == "select") {
			var selected1=true;
			var selected2=false;
			}
		else if (which == "unselect") {
			var selected1=false;
			var selected2=true;
			}
		else {
			return;
			}
		var re = new RegExp(regex);
		if (!hasOptions(obj)) { return; }
		for (var i=0; i<obj.options.length; i++) {
			if (re.test(obj.options[i].text)) {
				obj.options[i].selected = selected1;
				}
			else {
				if (only == true) {
					obj.options[i].selected = selected2;
					}
				}
			}
		}
	}
		
// -------------------------------------------------------------------
// selectMatchingOptions(select_object,regex)
//  This function selects all options that match the regular expression
//  passed in. Currently-selected options will not be changed.
// -------------------------------------------------------------------
function selectMatchingOptions(obj,regex) {
	selectUnselectMatchingOptions(obj,regex,"select",false);
	}
// -------------------------------------------------------------------
// selectOnlyMatchingOptions(select_object,regex)
//  This function selects all options that match the regular expression
//  passed in. Selected options that don't match will be un-selected.
// -------------------------------------------------------------------
function selectOnlyMatchingOptions(obj,regex) {
	selectUnselectMatchingOptions(obj,regex,"select",true);
	}
// -------------------------------------------------------------------
// unSelectMatchingOptions(select_object,regex)
//  This function Unselects all options that match the regular expression
//  passed in. 
// -------------------------------------------------------------------
function unSelectMatchingOptions(obj,regex) {
	selectUnselectMatchingOptions(obj,regex,"unselect",false);
	}
	
// -------------------------------------------------------------------
// sortSelect(select_object)
//   Pass this function a SELECT object and the options will be sorted
//   by their text (display) values
// -------------------------------------------------------------------
function sortSelect(obj) {
	var o = new Array();
	if (!hasOptions(obj)) { return; }
	for (var i=0; i<obj.options.length; i++) {
		o[o.length] = new Option( obj.options[i].text, obj.options[i].value, obj.options[i].defaultSelected, obj.options[i].selected) ;
		}
	if (o.length==0) { return; }
	o = o.sort( 
		function(a,b) { 
			if ((a.text+"") < (b.text+"")) { return -1; }
			if ((a.text+"") > (b.text+"")) { return 1; }
			return 0;
			} 
		);

	for (var i=0; i<o.length; i++) {
		obj.options[i] = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
		}
	}

// -------------------------------------------------------------------
// selectAllOptions(select_object)
//  This function takes a select box and selects all options (in a 
//  multiple select object). This is used when passing values between
//  two select boxes. Select all options in the right box before 
//  submitting the form so the values will be sent to the server.
// -------------------------------------------------------------------
function selectAllOptions(obj) {
	if (!hasOptions(obj)) { return; }
	for (var i=0; i<obj.options.length; i++) {
		obj.options[i].selected = true;
		}
	}
	
// -------------------------------------------------------------------
// moveSelectedOptions(select_object,select_object[,autosort(true/false)[,regex]])
//  This function moves options between select boxes. Works best with
//  multi-select boxes to create the common Windows control effect.
//  Passes all selected values from the first object to the second
//  object and re-sorts each box.
//  If a third argument of 'false' is passed, then the lists are not
//  sorted after the move.
//  If a fourth string argument is passed, this will function as a
//  Regular Expression to match against the TEXT or the options. If 
//  the text of an option matches the pattern, it will NOT be moved.
//  It will be treated as an unmoveable option.
//  You can also put this into the <SELECT> object as follows:
//    onDblClick="moveSelectedOptions(this,this.form.target)
//  This way, when the user double-clicks on a value in one box, it
//  will be transferred to the other (in browsers that support the 
//  onDblClick() event handler).
// -------------------------------------------------------------------
function moveSelectedOptions(fbox,tbox) {
	// Unselect matching options, if required
	if (arguments.length>3) {
		var regex = arguments[3];
		if (regex != "") {
			unSelectMatchingOptions(fbox,regex);
			}
		}
	// Move them over
	if (!hasOptions(fbox)) { return; }
	for (var i=0; i<fbox.options.length; i++) {
		var o = fbox.options[i];
		if (o.selected) {
			if (!hasOptions(tbox)) { var index = 0; } else { var index=tbox.options.length; }
			tbox.options[index] = new Option( o.text, o.value, false, false);
			}
		}
	// Delete them from original
	for (var i=(fbox.options.length-1); i>=0; i--) {
		var o = fbox.options[i];
		if (o.selected) {
			fbox.options[i] = null;
			}
		}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(fbox);
		sortSelect(tbox);
		}
	fbox.selectedIndex = -1;
	tbox.selectedIndex = -1;
	}

// -------------------------------------------------------------------
// copySelectedOptions(select_object,select_object[,autosort(true/false)])
//  This function copies options between select boxes instead of 
//  moving items. Duplicates in the target list are not allowed.
// -------------------------------------------------------------------
function copySelectedOptions(fbox,tbox) {
	var options = new Object();
	if (hasOptions(tbox)) {
		for (var i=0; i<tbox.options.length; i++) {
			options[tbox.options[i].value] = tbox.options[i].text;
			}
		}
	if (!hasOptions(fbox)) { return; }
	for (var i=0; i<fbox.options.length; i++) {
		var o = fbox.options[i];
		if (o.selected) {
			if (options[o.value] == null || options[o.value] == "undefined" || options[o.value]!=o.text) {
				if (!hasOptions(tbox)) { var index = 0; } else { var index=tbox.options.length; }
				tbox.options[index] = new Option( o.text, o.value, false, false);
				}
			}
		}
	if ((arguments.length<3) || (arguments[2]==true)) {
		sortSelect(tbox);
		}
	from.selectedIndex = -1;
	tbox.selectedIndex = -1;
	}

// -------------------------------------------------------------------
// moveAllOptions(select_object,select_object[,autosort(true/false)[,regex]])
//  Move all options from one select box to another.
// -------------------------------------------------------------------
function moveAllOptions(fbox,tbox) {
	selectAllOptions(fbox);
	if (arguments.length==2) {
		moveSelectedOptions(fbox,tbox);
		}
	else if (arguments.length==3) {
		moveSelectedOptions(fbox,tbox,arguments[2]);
		}
	else if (arguments.length==4) {
		moveSelectedOptions(fbox,tbox,arguments[2],arguments[3]);
		}
	}

// -------------------------------------------------------------------
// copyAllOptions(select_object,select_object[,autosort(true/false)])
//  Copy all options from one select box to another, instead of
//  removing items. Duplicates in the target list are not allowed.
// -------------------------------------------------------------------
function copyAllOptions(fbox,tbox) {
	selectAllOptions(fbox);
	if (arguments.length==2) {
		copySelectedOptions(fbox,tbox);
		}
	else if (arguments.length==3) {
		copySelectedOptions(fbox,tbox,arguments[2]);
		}
	}
/*
// -------------------------------------------------------------------
// swapOptions(select_object,option1,option2)
//  Swap positions of two options in a select list
// -------------------------------------------------------------------
function swapOptions(obj,i,j) {
	var o = obj.options;
	var i_selected = o[i].selected;
	var j_selected = o[j].selected;
	var temp = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	var temp2= new Option(o[j].text, o[j].value, o[j].defaultSelected, o[j].selected);
	o[i] = temp2;
	o[j] = temp;
	o[i].selected = j_selected;
	o[j].selected = i_selected;
	}

*/
	
// -------------------------------------------------------------------
// moveOptionUp(selectId)
//  Move selected option in a select list up one
// -------------------------------------------------------------------
function moveOptionUp(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for ( var i = 1; i < selectOptions.length; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			selectList.removeChild(opt);
			selectList.insertBefore(opt, selectOptions[i - 1]);
			return true;
		}
	}
};

// -------------------------------------------------------------------
// moveOptionDown(selectId)
//  Move selected option in a select list down one
// -------------------------------------------------------------------
function moveOptionDown(selectId) {
	var selectList = document.getElementById(selectId);
	var selectOptions = selectList.getElementsByTagName('option');
	for ( var i = 0; i < selectOptions.length - 1; i++) {
		var opt = selectOptions[i];
		if (opt.selected) {
			var next = selectOptions[i + 1];
			selectList.removeChild(next);
			selectList.insertBefore(next, selectOptions[i]);
			return true;
		}
	}
};
// -------------------------------------------------------------------
// removeSelectedOptions(select_object)
//  Remove all selected options from a list
//  (Thanks to Gene Ninestein)
// -------------------------------------------------------------------
function removeSelectedOptions(fbox) { 
	if (!hasOptions(fbox)) { return; }
	if (fbox.type=="select-one") {
		fbox.options[fbox.selectedIndex] = null;
		}
	else {
		for (var i=(fbox.options.length-1); i>=0; i--) { 
			var o=fbox.options[i]; 
			if (o.selected) { 
				fbox.options[i] = null; 
				} 
			}
		}
	fbox.selectedIndex = -1; 
	} 

// -------------------------------------------------------------------
// removeAllOptions(select_object)
//  Remove all options from a list
// -------------------------------------------------------------------
function removeAllOptions(fbox) { 
	if (!hasOptions(fbox)) { return; }
	for (var i=(fbox.options.length-1); i>=0; i--) { 
		fbox.options[i] = null; 
		} 
	fbox.selectedIndex = -1; 
	} 

// -------------------------------------------------------------------
// addOption(select_object,display_text,value,selected)
//  Add an option to a list
// -------------------------------------------------------------------
function addOption(obj,text,value,selected) {
	if (obj!=null && obj.options!=null) {
		obj.options[obj.options.length] = new Option(text, value, false, selected);
		}
	}

