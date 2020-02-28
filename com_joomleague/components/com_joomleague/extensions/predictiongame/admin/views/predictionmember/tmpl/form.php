<?php 
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;

defined( '_JEXEC' ) or die( 'Restricted access' );

HTMLHelper::_( 'behavior.tooltip' );

$params = $this->form->getFieldsets('params');
// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();

echo 'predictionuser<pre>',print_r($this->predictionuser, true),'</pre>';

// Set toolbar items for the page
$edit = Factory::getApplication()->input->getVar( 'edit', true );
$text = !$edit ? Text::_( 'COM_JOOMLEAGUE_GLOBAL_NEW' ) : Text::_( 'COM_JOOMLEAGUE_GLOBAL_EDIT' );
JLToolBarHelper::title( Text::_( 'COM_JOOMLEAGUE_ADMIN_PMEMBER_PGAME' ) . ': <small><small>[ ' . $text . ' ]</small></small>' );
JLToolBarHelper::save('predictionmember.save');

if ( !$edit )
{
	JLToolBarHelper::divider();
	JLToolBarHelper::cancel('predictionmember.cancel');
}
else
{
	// for existing items the button is renamed `close` and the apply button is showed
	JLToolBarHelper::apply('predictionmember.apply');
	JToolBarHelper::divider();
	JLToolBarHelper::cancel( 'predictionmember.cancel');
}
//JLToolBarHelper::onlinehelp();

$uri	= Uri::getInstance();


?>


<style type="text/css">
	table.paramlist td.paramlist_key {
		width: 92px;
		text-align: left;
		height: 30px;
	}
</style>
<form action="index.php" method="post"  id="adminForm">
	<?php
	$p = 1;
	echo HTMLHelper::_('bootstrap.startTabSet','tabs',array('active' => 'panel1'));
	echo HTMLHelper::_('bootstrap.addTab','tabs','panel'.$p++,Text::_('COM_JOOMLEAGUE_TABS_DETAILS',true));
	echo $this->loadTemplate('details');
	echo HTMLHelper::_('bootstrap.endTab');
	echo HTMLHelper::_('bootstrap.endTabSet');
	
	?>
	<div class="clearfix"></div>
	

	
				
				
				
			</table>
		</fieldset>

		

		<div class="clr"></div>
		
		<input type="hidden" name="option"											value="com_joomleague" />
		
		<input type="hidden" name="cid[]"											value="<?php echo $this->predictionuser->id; ?>" />
		<input type="hidden" name="task"											value="" />
	</div>
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>