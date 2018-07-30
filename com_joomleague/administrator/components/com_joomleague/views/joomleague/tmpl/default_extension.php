<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
?>
<?php
// this is an example menu for an extension
/*
 * $imagePath='administrator/components/com_joomleague/assets/images/';
 * // active pane selector
 * switch (Factory::getApplication()->input->get('view'))
 * {
 * case 'yourview': $active=count($this->tabs);
 * break;
 * default: $active=count($this->tabs);
 * }
 *
 * $pane=new stdClass();
 * $pane->id = 'Extension';
 * $pane->title=Text::_('COM_JOOMLEAGUE_T_MENU_Extension');
 * $pane->name='ExtMenuExtension';
 * $pane->alert=false;
 * $tabs[]=$pane;
 *
 * $link5=array();
 * $label5=array();
 * $limage5=array();
 * $link5[]=Route::_('index.php?option=com_joomleague&view=yourview&active='.$active);
 * $label5[]=Text::_('COM_JOOMLEAGUE_T_MENU_yourview');
 * $limage5[]=HtmlHelper::_('image',$imagePath.'icon-16-FrontendSettings.png',Text::_('COM_JOOMLEAGUE_T_MENU_yourview'));
 *
 * $link[]=$link5;
 * $label[]=$label5;
 * $limage[]=$limage5;
 *
 *
 * $n=0;
 *
 * echo HtmlHelper::_('sliders.start','sliders',array('allowAllClose' => true,
 * 'startOffset' => $this->active,
 * 'startTransition' => true, true));
 *
 * foreach ($tabs as $tab)
 * {
 * $title=Text::_($tab->title);
 * echo HtmlHelper::_('sliders.panel',$title, $tab->id);
 * ?>
 * <div style="float: left;">
 * <table><?php
 * for ($i=0;$i < count($link[$n]); $i++)
 * {
 * ?><tr><td><b><a href="<?php echo $link[$n][$i]; ?>" title="<?php echo
 * Text::_($label[$n][$i]); ?>">
 * <?php echo $limage[$n][$i].' '.Text::_($label[$n][$i]); ?>
 * </a></b></td></tr><?php
 * }
 * ?></table>
 * </div>
 * <?php
 * $n++;
 * }
 * echo HtmlHelper::_('sliders.end');
 */
?>
