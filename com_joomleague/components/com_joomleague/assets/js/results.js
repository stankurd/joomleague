/**
* @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
* @license	GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

jQuery('domready', function() {
	jQuery('.eventstoggle').click (function(){
		var id = this.getProperty('id').substr(7);
		if (document.getElementById('info'+id).getStyle('display') == 'block') {
			document.getElementById('info'+id).setStyle('display', 'none');
		}
		else {
			document.getElementById('info'+id).setStyle('display', 'block');
		}
	});
});