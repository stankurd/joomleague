<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * Joomleague
 * @subpackage	Module-Logo
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;

// check if any results returned
$items = count($list['teams']);
if (!$items) {
	echo '<p class="modjlglogo">' . Text::_('MOD_JOOMLEAGUE_LOGO_NO_ITEMS') . '</p>';
	return;
}
$nametype = $params->get('nametype');
$typel = $params->get('show_logo', 0);
?>

<!--[if IE 7]>
<style type="text/css">
#modjlglogo li.logo0, li.logo1, li.logo2 {
    display: inline;
}
</style>
<![endif]-->

<script type="text/javascript">
	(function(){
	var Tips1 = new Tips(jQuery('.logo'));
}); 
</script>



<div class="modjlglogo"><?php if ($params->get('show_project_name', 0)):?>
<p class="projectname"><?php echo $list['project']->name; ?></p>
<?php endif; ?>

<ul id="modjlglogo">
<?php foreach (array_slice($list['teams'], 0, $params->get('limit', 12)) as $item) :  ?>
	<li class="logo<?php echo $typel; ?>">
	<?php $link = HTMLHelper::link(modJLGLogoHelper::getTeamLink($item, 
															$params, 
															$list['project']), 
															isset($item->team->$nametype));
	$link1 = explode(">", $link);
	echo $link1[0].'>';
	echo modJLGLogoHelper::getLogo($item, $typel)."</a></li>"; ?>
	<?php endforeach; ?>
</ul>
</div>
