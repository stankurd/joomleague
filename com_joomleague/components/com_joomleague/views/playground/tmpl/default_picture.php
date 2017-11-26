<?php defined('_JEXEC') or die; ?>

<?php if ( ( $this->playground->picture ) ): ?>

 <h2><?php echo JText::_('COM_JOOMLEAGUE_PLAYGROUND_CLUB_PICTURE'); ?></h2>  
<div class='venuecontent picture'>
    <?php
    if (($this->playground->picture))
    {
        echo JHtml::image($this->playground->picture, $this->playground->name);
    }
    else
    {
        echo JHtml::image(JoomleagueHelper::getDefaultPlaceholder('team'), $this->playground->name);
    }
    ?>
</div>
<?php endif; ?>
