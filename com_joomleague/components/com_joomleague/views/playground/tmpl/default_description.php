<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<?php if ( $this->playground->notes ): ?>
<h2><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_NOTES'); ?></h2>
<div class='venuecontent'>
<?php
    $description = $this->playground->notes;
    $description = HTMLHelper::_('content.prepare', $description);
    echo $description; 
?>
</div>
<?php endif; ?>