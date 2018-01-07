<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>
<fieldset class="adminform">
	<legend>
			<?php
			echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_TREETONODE_TITLE_DESCRIPTION','<i>' . $this->item->node . '</i>',
					'<i>' . $this->project->name . '</i>');
			?>
		</legend>
	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETONODE_TITLE_NODE'); ?></div>
		<div class="controls">
			<input class="text_area" type="text" name="title" id="title"
				size="60" maxlength="250" value="<?php echo $this->item->title; ?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETONODE_CONTENT_NODE'); ?></div>
		<div class="controls">
			<input class="text_area" type="text" name="content" id="content"
				size="60" maxlength="250"
				value="<?php echo $this->item->content; ?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_TREETONODE_TEAM'); ?></div>
		<div class="controls">
				<?php
				$append = '';
				if($this->item->team_id == 0)
				{
					$append = ' style="background-color:#bbffff"';
				}
				echo HTMLHelper::_('select.genericlist',$this->lists['team'],'team_id' . $this->item->id,
						'class="inputbox select-hometeam" size="1"' . $append,'value','text',$this->item->team_id);
				?>
			</div>
	</div>
</fieldset>