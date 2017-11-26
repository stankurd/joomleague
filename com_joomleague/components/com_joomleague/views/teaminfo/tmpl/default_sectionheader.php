<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die; 

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

$canEdit = $this->showediticon;
?>
<!-- START: Contentheading -->
<div class='contentpaneopen'>
	<div class='contentheading'>
		<?php 
		echo $this->pagetitle;
		if ($canEdit) {
			echo JHtml::_('icon.edit',$this->project->id,$this->team,$this->team->project_team_id,'projectteamform.edit','teaminfo');
		} 		
		?>
	</div>
</div>
<!-- END: Contentheading -->
