<?php
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

 echo HTMLHelper::_('select.genericlist', $this->matchdaysoptions, 'select-round', 'onchange="joomleague_changedoc(this);" style="float:right;"', 'value', 'text', $this->currenturl);
?>