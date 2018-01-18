<?php 
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

defined('_JEXEC') or die; ?>
<!-- Team Player Description START -->
<?php
	$description = "";
	if ( isset($this->teamPlayer) && !empty($this->teamPlayer->notes) )
	{
		$description = $this->teamPlayer->notes;
	}
	else
	{
		if ( !empty($this->person->notes) )
		{
			$description = $this->person->notes;
		}
	}

	if ( !empty($description) )
	{
		?>
		<h2><?php echo Text::_( 'COM_JOOMLEAGUE_PERSON_INFO' );	?></h2>
		<div class="personinfo">
			<?php	
			$description = HTMLHelper::_('content.prepare', $description);
			echo stripslashes( $description ); 
			?>
		</div>
		<?php
	}
	?>
<!-- Team Player Description END -->