<?php 
/*
 * @package             Joomleague
 * @subpackage          Module-Matches
 * @lastedit            06.09.2016
 * @testenvironment	Joomla 3.6 & PHP 5.6
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @link        http://www.joomleague.at 
 * @license     GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<div class="clr"></div>
<form method="post" name="matrixForm" id="matrixForm">

<fieldset class="adminform"><legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MATRIX_TITLE'); ?></legend>
<fieldset class="adminform">
	<?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_MATRIX_HINT'); ?>
</fieldset>
<br>
<style>
	table.adminlist
	{
  overflow: hidden;
	}
	.adminlist td, .adminlist th
	{
		border-right: 1px solid #ddd;
		border-bottom: 1px solid #ddd;
	}
	.adminlist th
	{
		font-weight: normal;
		vertical-align: bottom;
	}
	.adminlist tr:hover
	{
		background-color: #e8e8e8;
	}
	.adminlist td, .adminlist th
	{
  	position: relative;
	}
	.adminlist td:hover::after, .adminlist th:hover::after
	{
		content: "";
		position: absolute;
		background-color: #e8e8e8;
		left: 0;
		top: -5000px;
		height: 10000px;
		width: 100%;
		z-index: -1;
	}
	
input[type="radio"]
	{
		margin: auto 0 2px 0;
	}
</style>
<?php

$matrix ='';

if (isset($this->teams) && count($this->teams) > 1) {
	$teams = $this->teams;
	$matrix = "<table width=\"100%\" class=\"adminlist\">";

	$k = 0;
	for($rows = 0; $rows <= count($teams); $rows++){
		if($rows == 0) $trow = $teams[0];
		else $trow = $teams[$rows-1];
		$matrix .= "<tr class=\"row$k\">";
		for($cols = 0; $cols <= count($teams); $cols++){
			$text = '';
			$checked = '';
			$color = '';
			if( $cols == 0 ) $tcol = $teams[0];
			else $tcol = $teams[$cols-1];
			$match = $trow->value.'_'.$tcol->value;
			$onClick = sprintf("onclick=\"javascript:saveMatch('%s','%s');\"", $trow->value, $tcol->value);
			if($rows == 0 && $cols == 0) $text = "<th align=\"center\"></th>";
			else if($rows == 0) $text = sprintf("<th width=\"200\" align=\"center\" title=\"%s\">%s</th>",$tcol->text, isset($tcol->short_name) ? $tcol->short_name : $tcol->text ); //picture columns
			else if($cols == 0) $text = sprintf("<td align=\"left\" nowrap>%s</td>",$trow->text); // named rows
			else if($rows == $cols) $text = "<td align=\"center\"><input type=\"radio\" DISABLED></td>"; //impossible matches
			else{
				if(count($this->items) >0) {
					for ($i=0,$n=count($this->items); $i < $n; $i++)
					{
						$row = $this->items[$i];
						if($row->projectteam1_id == $trow->value 
							&& $row->projectteam2_id == $tcol->value
						){
							$checked = 'checked';
							$color = '#70a9c7';
							$onClick = '';
							break;
						} else {
							$checked = '';
							$color = '';
							$onClick = sprintf("onclick=\"javascript:saveMatch('%s','%s');\"", $trow->value, $tcol->value);
						}
					}
				}	
				$text = sprintf("<td align=\"center\" title=\"%s - %s\" style=\"background-color:%s\"><input type=\"radio\" name=\"match_%s\" %s %s></td>\n",$trow->text,$tcol->text,$color,$trow->value.$tcol->value, $onClick, $checked);
			}
			$matrix .= $text;
		}
		$k = 1 - $k;
	}
	$matrix .= "</table>";
}
//show the matrix
echo $matrix;
?></fieldset>
<?php 
$round_date_first = new Date($this->round->round_date_first);
$dValue = $round_date_first->format(Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT')).' '.$this->project->start_time; 
?>
<input type='hidden' name='match_date' value='<?php echo $dValue; ?>' />
<input type='hidden' name='projectteam1_id' value='' />
<input type='hidden' name='projectteam2_id' value='' />
<input type='hidden' name='published' value='1' />
<input type='hidden' name='task' value='match.addmatch' />

<?php echo HTMLHelper::_('form.token'); ?>
</form>
