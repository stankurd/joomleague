<?php 
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license	GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

if (ComponentHelper::getParams('com_joomleague')->get('show_footer',1))
{
?>
	<br />
		<div class="copyright">
			<?php
			echo ' :: Powered by ';
			echo HTMLHelper::link('http://www.joomleague.at','JoomLeague',array('target' => '_blank'));
			echo ' - ';
			echo HTMLHelper::link('index.php?option=com_joomleague&amp;view=about',sprintf('Version %1$s',JoomleagueHelper::getVersion()));
			echo ' :: ';
			?>
		</div>
<?php
}
?>