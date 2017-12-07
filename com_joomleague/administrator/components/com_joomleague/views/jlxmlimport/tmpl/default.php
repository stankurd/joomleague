<?php 
use Joomla\CMS\HTML\HTMLHelper;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die;

HTMLHelper::_('behavior.tooltip');

$model = $this->getModel('jlxmlimport');
echo $model->getXml;
?>