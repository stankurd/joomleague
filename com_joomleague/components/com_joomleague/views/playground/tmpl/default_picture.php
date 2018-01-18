<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<?php if ( ( $this->playground->picture ) ): ?>

 <h2><?php echo Text::_('COM_JOOMLEAGUE_PLAYGROUND_CLUB_PICTURE'); ?></h2>  
<div class='venuecontent picture'>
    <?php
    if (($this->playground->picture))
    {
        echo HTMLHelper::image($this->playground->picture, $this->playground->name);
    }
    else
    {
        echo HTMLHelper::image(JoomleagueHelper::getDefaultPlaceholder('team'), $this->playground->name);
    }
    ?>
</div>
<?php endif; ?>
