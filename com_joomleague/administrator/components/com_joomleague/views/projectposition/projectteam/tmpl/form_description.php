<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>
<fieldset class="form-vertical">
	<legend>
		<?php echo Text::sprintf('COM_JOOMLEAGUE_ADMIN_P_TEAM_TITLE_DESCR','<i>'.$this->item->name.'</i>','<i>'.$this->project->name.'</i>');?>
	</legend>
	<?php
	echo $this->form->renderField('info');
	echo $this->form->renderField('notes');
	?>
</fieldset>