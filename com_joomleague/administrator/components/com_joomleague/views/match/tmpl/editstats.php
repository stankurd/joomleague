<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined ( '_JEXEC' ) or die ();
HTMLHelper::_ ( 'behavior.tooltip' );

?>
<form method="post" name="adminForm" id="adminForm">
	<div id="jlstatsform">
		<div class="clear"></div>
		<?php
		$selector = 'editstats';
		echo HTMLHelper::_ ( 'bootstrap.startTabSet', $selector, array (
				'active' => 'home' 
		) );
		
		echo HTMLHelper::_ ( 'bootstrap.addTab', $selector, 'home', Text::_ ( $this->teams->team1 ) );
		echo $this->loadTemplate ( 'home' );
		echo HTMLHelper::_ ( 'bootstrap.endTab' );
		
		echo HTMLHelper::_ ( 'bootstrap.addTab', $selector, 'away', Text::_ ( $this->teams->team2 ) );
		echo $this->loadTemplate ( 'away' );
		echo HTMLHelper::_ ( 'bootstrap.endTab' );
		
		echo HTMLHelper::_ ( 'bootstrap.endTabSet' );
		?>
		<input type="hidden" name="task" id="" value="" /> <input
			type="hidden" name="project_id"
			value="<?php echo $this->match->project_id; ?>" /> <input
			type="hidden" name="match_id" value="<?php echo $this->match->id; ?>" />
		<input type="hidden" name="boxchecked" value="0" />
		
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
<div style="clear: both"></div>
