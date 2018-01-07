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

HTMLHelper::_('behavior.tooltip');
jimport('joomla.html.pane');

JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_TREETONODE_TITLE'));

JLToolBarHelper::apply('treetonode.apply');
JLToolBarHelper::save('treetonode.save');
JLToolBarHelper::back('Back','index.php?option=com_joomleague&view=treetonodes');
JLToolBarHelper::custom('treetonode.unpublishnode','delete.png','delete_f2.png',Text::_('COM_JOOMLEAGUE_ADMIN_TREETONODES_UNPUBLISH'),false);

JLToolBarHelper::help('screen.joomleague',true);
?>

<script>
		function submitbutton(pressbutton) {
			var form = $('adminForm');
			if (pressbutton == 'cancel') {
				submitform(pressbutton);
				return;
			}
			submitform(pressbutton);
			return;
		}

</script>
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<div class="col50">

<?php
$p = 1;
echo HTMLHelper::_('bootstrap.startTabSet','tabs',array(
		'active' => 'panel1'
));
echo HTMLHelper::_('bootstrap.addTab','tabs','panel' . $p ++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
echo $this->loadTemplate('description');
echo HTMLHelper::_('bootstrap.endTab');
echo HTMLHelper::_('bootstrap.endTabSet');
?>

		<div class="clr"></div>
		<input type="hidden" name="option" value="com_joomleague" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="project_id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="task" value="" />
	</div>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>