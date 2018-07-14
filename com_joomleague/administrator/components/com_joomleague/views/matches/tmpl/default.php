<?php 
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

HTMLHelper::_('bootstrap.tooltip');
//HTMLHelper::_('behavior.modal');


$app 		= Factory::getApplication();
$input		= $app->input;
$massadd	= $input->getInt('massadd',0);
?>
<div id="j-main-container" class="col-md-12">
<div id="alt_decision_enter" style="display:<?php echo ($massadd == 0) ? 'none' : 'block'; ?>">
<?php echo $this->loadTemplate('massadd'); ?>
</div>
<?php 
echo $this->loadTemplate('matches');
if(count($this->teams) > 1) {
	echo $this->loadTemplate('matrix');
} 
?>
</div>