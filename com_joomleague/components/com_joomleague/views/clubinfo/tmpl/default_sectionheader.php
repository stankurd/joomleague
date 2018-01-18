<?php
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers');
$canEdit = $this->showediticon;
?>
<div class='contentpaneopen'>
	<div class='contentheading'>
		<?php
			echo $this->pagetitle.' ';
			if ($canEdit) {
				echo HTMLHelper::_('icon.edit',$this->project->id,$this->club,$this->club->id,'clubform.edit','clubinfo');
			}
		?>
	</div>
</div>