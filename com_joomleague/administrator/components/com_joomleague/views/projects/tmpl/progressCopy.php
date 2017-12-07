<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;
?>
<form action="<?php echo Route::_('index.php?option=com_joomleague&view=projects&layout=progressCopy'); ?>" method="post" id="adminForm" name="adminForm">
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
