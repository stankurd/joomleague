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

defined('_JEXEC') or die;
?>
<form action="<?php echo $this->request_url; ?>" method="post" id="adminForm" name="adminForm">
<?php
	$p=1;
	echo HTMLHelper::_('bootstrap.startTabSet', 'tabs', array('active' => 'panel1'));
	
	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_ABOUT_DETAILS', true));
	echo $this->loadTemplate('details');
	echo HTMLHelper::_('bootstrap.endTab');
	
	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_ABOUT_TEAM', true));
	echo $this->loadTemplate('team');
	echo HTMLHelper::_('bootstrap.endTab'); 

	echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'panel'.$p++,'<span class="icon-database"></span>'.Text::_('COM_JOOMLEAGUE_TABS_ABOUT_3RD', true));
	echo $this->loadTemplate('3rd');
	echo HTMLHelper::_('bootstrap.endTab');
	
	echo HTMLHelper::_('bootstrap.endTabSet');
?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
