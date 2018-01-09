/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

jQuery(function() {
	jQuery('#stid').change(function(){
		var form = jQuery('#adminForm1');
		form.submit();
	});
	if (jQuery('#seasonnav')) {
		jQuery('#seasonnav').change(function(){
			var form = jQuery('#adminForm1');
			if (this.value != 0) {
				jQuery('jl_short_act').val('seasons');
			}
			form.submit();
		});
	}

	if(jQuery('#pid')!=null){
		jQuery('#pid').change(function(){
			var form = jQuery('#adminForm1');
			if (this.value != 0) {
				jQuery('#jl_short_act').val('projects');
			}
			form.submit();
		});
	}

	if(jQuery('#tid')!=null){
		jQuery('#tid').change(function(){
			var form = jQuery('#adminForm1');
			if (this.value != 0) {
				jQuery('#jl_short_act').val('teams');
			}
		form.submit();
	});}
	
	if(jQuery('#rid')!=null){
		jQuery('#rid').change(function(){
		var form = jQuery('#adminForm1');
		if (this.value != 0) {
			jQuery('#jl_short_act').val('rounds');
		}
		form.submit();
	});}
});