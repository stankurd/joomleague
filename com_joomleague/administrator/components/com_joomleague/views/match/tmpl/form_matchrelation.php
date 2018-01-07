<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ();
?>
<fieldset class="adminform">
	<legend><?php echo Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_DETAILS');?></legend>
	<br />



	<table class='admintable table'>
		<tr>
			<td align="right" class="key"><label>
							<?php
							echo Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_OLD_ID' );
							?>
						</label></td>
			<td align="left">
						<?php echo $this->lists['old_match']; ?>  
						<?php if($this->item->old_match_id >0) : ?>
						  <a
				href="index.php?option=com_joomleague&task=match.edit&id=<?php echo $this->item->old_match_id?>">Match
					Link</a>
						<?php endif ?>
					</td>
		</tr>
		<tr>
			<td align="right" class="key"><label>
							<?php
							echo Text::_ ( 'COM_JOOMLEAGUE_ADMIN_MATCH_F_MREL_NEW_ID' );
							?>
						</label></td>
			<td align="left">
						<?php echo $this->lists['new_match']; ?> 
						<?php if($this->item->new_match_id >0) : ?>
						  <a
				href="index.php?option=com_joomleague&task=match.edit&id=<?php echo $this->item->new_match_id?>">Match
					Link</a>
						<?php endif ?>
					</td>
		</tr>

	</table>
</fieldset>