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
<div>
	<fieldset class="form-vertical">
		<legend><?php echo Text::_('JCONFIG_PERMISSIONS_LABEL'); ?></legend>
		<?php echo $this->form->getInput('rules'); ?>
	</fieldset>
</div>
