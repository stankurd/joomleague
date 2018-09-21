
/**
 * js script for joomleague navigation module
 */
(function($){
	var form;
	var updateProjects = function(response)
	{
		var select = form.find('#p');
		var first = select.find('option').first().clone();
		select.empty();
		select.append(first);

		var count = response.length;

		var include_season =form.find('[name="include_season"]').val();

		for (var i = 0; i < count; i++)
		{
			if (include_season == 2) {
				var txt = response[i].text + " - " + response[i].season_name;
			}
			else if (include_season == 1) {
				var txt = response[i].season_name + " - " + response[i].text;
			}
			else {
				var txt = response[i].text;
			}

			select.append('<option value="' + response[i].value + '">' + txt + '</option>');
		}
	};

	var customsubmit = function()
	{
		var query = '';
		query += 'view=' + form.find('[name="view"]').val();
		query += '&p=' + form.find('[name="p"]').val();

		if (form.find('[name="d"]').length) {
			query += '&division=' + form.find('[name="d"]').val();
		}

		if (form.find('[name="tid"]').length) {
			query += '&tid=' + form.find('[name="tid"]').val();
		}

		$.ajax({
				url: 'index.php?option=com_joomleague&task=ajax.getroute&format=json&tmpl=component',
				data: query,
				dataType: 'json',
				type : 'POST'
		})
		.done(credirect);
	};

	var credirect = function(response)
	{
		window.location = response;
	};

jQuery(document).ready(function($) {	
		form = $('div#jl-nav-module form');

		$('div#jl-nav-module .jlnav-select').change(function() {
			$('.nav-item').hide();
			$('.team-select').hide();
			$('.division-select').hide();

			$.ajax({
				url: 'index.php?option=com_joomleague&task=ajax.getprojectsoptions&format=json&tmpl=component',
				data: form.serialize(),
				dataType: 'json',
				type : 'POST'
			})
			.done(updateProjects);
		});

		$('div#jl-nav-module .jlnav-project').change(function(){
			if ($(this).val() > 0) {
				customsubmit();
			}
		});

		$('div#jl-nav-module .jlnav-division').change(function(){
			if ($(this).val() > 0) {
				customsubmit();
			}
		});

		$('div#jl-nav-module .jlnav-team').change(function(){
			form.find('[name=view]').val(form.find('[name="teamview"]').val());
			customsubmit();
		});
	});
})(jQuery);
