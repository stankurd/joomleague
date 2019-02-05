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


use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access'); 

$cnt=-1;
$extensions=JoomleagueHelper::getExtensions(1);
foreach ($extensions as $e => $extension) {
	$JLGPATH_EXTENSION= JPATH_COMPONENT_SITE.'/extensions/'.$extension;
	$menufile = $JLGPATH_EXTENSION . '/admin/views/adminmenu/tmpl/default_'.$extension.'.php';
	if(File::exists($menufile )) {
		++$cnt;
	} else {
	}
	if($extension=='predictiongame') break;
}
$imagePath='administrator/components/com_joomleague/assets/images/';
// active pane selector
$active=count($this->tabs);

// Predictiongames Pane
$link4=array();
$label4=array();
$limage4=array();

$pane=new stdClass();
$pane->id = "pg";
$pane->title=Text::_('JL_T_MENU_PREDICTION');
$pane->name='PGMenu';
$pane->alert=false;
$tabs[]=$pane;

$link4[]=Route::_('index.php?option=com_joomleague&view=predictiongames&controller=predictiongame&active='.$active);
$label4[]=Text::_('JL_T_MENU_GAMES');
$limage4[]=HTMLHelper::_('image',$imagePath.'prediction.png',Text::_('JL_T_MENU_GAMES_INFO'));

$link4[]=Route::_('index.php?option=com_joomleague&view=predictionmembers&active='.$active);
$label4[]=Text::_('JL_T_MENU_MEMBERS');
$limage4[]=HTMLHelper::_('image',$imagePath.'user_edit.png',Text::_('JL_T_MENU_MEMBERS_INFO'));

$link4[]=Route::_('index.php?option=com_joomleague&view=predictiontemplates&active='.$active);
$label4[]=Text::_('JL_T_MENU_TEMPLATES');
$limage4[]=HTMLHelper::_('image',$imagePath.'icon-16-FrontendSettings.png',Text::_('JL_T_MENU_TEMPLATES_INFO'));

$link[]=$link4;
$label[]=$label4;
$limage[]=$limage4;

$n=0;
/**
$pane =& JPane::getInstance('sliders', 
							array('allowAllClose' => true, 
								'startOffset' => $active,  // it's no use here, as there is only one slider per page anyway...
								'startTransition' => true, 
								'alwaysHide'=>false, 
							true));
echo $pane->startPane("extpanepredictiongame");*/
foreach ($tabs as $tab)
{
	$title=Text::_($tab->title);
	echo $pane->startPanel($title, $tab->id);
	?>
	<div style="float: left;">
		<table><?php
			for ($i=0;$i < count($link[$n]); $i++)
			{
				?><tr><td><b><a href="<?php echo $link[$n][$i]; ?>" title="<?php echo Text::_($label[$n][$i]); ?>">
						<?php echo $limage[$n][$i].' '.Text::_($label[$n][$i]); ?>
				</a></b></td></tr><?php
			}
			?>
		</table>
	</div>
	<?php
	//echo $pane->endPanel();
	$n++;
}
//echo $pane->endPane();
?>