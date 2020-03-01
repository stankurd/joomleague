<?php use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die; ?>

<!-- Flash Statistik Start -->
<script type="text/javascript">
	function get_teamstats_chart() {
		var data_teamstats_chart = <?php echo $this->chartdata->toString(); ?>;
		console.log(data_teamstats_chart);
		return JSON.stringify(data_teamstats_chart);
		//console.dir(data_teamstats_chart);
	}
	swfobject.embedSWF("<?php echo Uri::base(true).'/components/com_joomleague/assets/classes/open-flash-chart/open-flash-chart.swf'; ?>", 
			"teamstats_chart", "100%", "400", "9.0.0", false, {"get-data": "get_teamstats_chart"} );
</script>


<table width='100%' height='400px' cellspacing='0' border='0'>
	<tbody>
	<tr class='sectiontableheader'>
		<th><?php echo Text::_('COM_JOOMLEAGUE_TEAMSTATS_GOALS_STATISTIC'); ?></th>
	</tr>
	<tr>
		<td>
			<div id='teamstats_chart'></div>
		</td>
	</tr>
	</tbody>
</table>
<!-- Flash Statistik END -->