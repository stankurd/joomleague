<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;

defined ( '_JEXEC' ) or die ();

JHtml::_ ( 'behavior.tooltip' );
JHtml::_ ( 'behavior.modal' );
?>
<div id="gamesevents">
	<form method="post" id="adminForm">
		<div class="col50" id="eventtype">
	<?php
	$app = Factory::getApplication ();
	$option = $app->input->get ( 'option' );
	$params = ComponentHelper::getParams ( $option );
	$model = $this->getModel ();
	if (! empty ( $this->teams )) {
		$selector = 'editeventsbb';
		echo JHtml::_ ( 'bootstrap.startTabSet', $selector, array (
				'active' => 'panel1',
				'onclick' => 'alert(1)' 
		) );
		
		$teamname = $this->teams->team1;
		echo JHtml::_ ( 'bootstrap.addTab', $selector, 'panel1', $teamname );
		$this->_handlePreFillRoster ( $this->teams, $model, $params, $this->teams->projectteam1_id, $teamname );
		echo $this->loadTemplate ( 'home' );
		echo JHtml::_ ( 'bootstrap.endTab' );
		
		$teamname = $this->teams->team2;
		echo JHtml::_ ( 'bootstrap.addTab', $selector, 'panel2', $teamname );
		$this->_handlePreFillRoster ( $this->teams, $model, $params, $this->teams->projectteam2_id, $teamname );
		echo $this->loadTemplate ( 'away' );
		echo JHtml::_ ( 'bootstrap.endTab' );
		
		echo JHtml::_ ( 'bootstrap.endTabSet' );
	}
	?>
		</div>
		<input type="hidden" name="task" value="match.saveeventbb" /> <input
			type="hidden" name="view" value="match" /> <input type="hidden"
			name="option" value="com_joomleague" id="option" /> <input
			type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_( 'form.token' ); ?>
	</form>
</div>