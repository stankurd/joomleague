<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined ( '_JEXEC' ) or die ();
?>
<form id="adminForm" name="adminForm" method="post">
	<div class="clearfix"></div>
	<br>
	<div id="lineup">
		<?php
		// focus on players tab
		$selector = 'eventtype';
		echo JHtml::_('bootstrap.startTabSet', $selector, array('active' => 'substitutions'));
		
		echo JHtml::_('bootstrap.addTab',$selector,'substitutions',JText::_('COM_JOOMLEAGUE_TABS_SUBST'));
		echo $this->loadTemplate('substitutions');
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.addTab',$selector,'players',JText::_('COM_JOOMLEAGUE_TABS_PLAYERS'));
		echo $this->loadTemplate('players');
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.addTab',$selector,'stafftab',JText::_('COM_JOOMLEAGUE_TABS_STAFF'));
		echo $this->loadTemplate('staff');
		echo JHtml::_('bootstrap.endTab');
		
		echo JHtml::_('bootstrap.endTabSet');
		?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="cid[]" value="<?php echo $this->match->id; ?>" />
		<input type="hidden" name="changes_check" value="0" id="changes_check" />
		<input type="hidden" name="team_id" value="<?php echo $this->tid; ?>" id="team" />
		<input type="hidden" name="positionscount" value="<?php echo count($this->positions); ?>" id="positioncount" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>