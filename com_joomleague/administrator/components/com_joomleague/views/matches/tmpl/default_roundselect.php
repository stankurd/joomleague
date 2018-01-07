<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;
?>

<!-- round selector START -->
		<form name="roundForm" id="roundForm" method="post">
			<input type="hidden" name="act" value="" id="short_act" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name='boxchecked' value="0" />
			<div style="float: right; vertical-align: middle; line-height: 27px;">
			<?php echo HTMLHelper::_('form.token'); ?>

				<?php
				$lv=""; $nv=""; $sv=false;

				foreach($this->ress as $v) { if ($v->id == $this->round->id) { break; } $lv=$v->id; }
				foreach($this->ress as $v) { $nv=$v->id; if ($sv) { break; } if ($v->id == $this->round->id) { $sv=true; } }
				echo '<div style="float: left; text-align: center;">';
				if ($lv != "")
				{
					$query="option=com_joomleague&view=matches&rid[]=".$lv;
					$link=Route::_('index.php?'.$query);
					$prevlink=HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PREV_ROUND'));
					echo $prevlink;
				}
				else
				{
					echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_PREV_ROUND');
				}
				echo '</div>';
				echo '<div style="float: left; text-align: center; margin-right: 10px; margin-left: 10px;">';
				echo $this->lists['project_rounds'];
				echo '</div>';
				echo '<div style="float: left; text-align: center;">';
				if (($nv != "") && ($nv != $this->round->id))
				{
					$query="option=com_joomleague&view=matches&rid[]=".$nv;
					$link=Route::_('index.php?'.$query);
					$nextlink=HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_NEXT_ROUND'));
					echo $nextlink;
				}
				else
				{
					echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_NEXT_ROUND');
				}
				echo '</div>';
				?>
				</div>
		</form>
		<!-- round selector END -->
