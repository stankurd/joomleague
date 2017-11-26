<?php
/*
 * @package 			Joomleague
 * @subpackage		Module-Matches
 * @lastedit			26.08.2016
 * @testedversion	Joomla 3.6
 *
 * @copyright	Copyright (C) 2006-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at 
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT.'/components/com_joomleague/joomleague.core.php';
if (!defined('_JLMATCHLISTMODPATH')) { 
	define('_JLMATCHLISTMODPATH', dirname( __FILE__ ));
}
if (!defined('_JLMATCHLISTMODURL')) { 
	define('_JLMATCHLISTMODURL', JUri::base().'modules/mod_joomleague_matches/');
}
require_once (_JLMATCHLISTMODPATH.'/helper.php');
require_once (_JLMATCHLISTMODPATH.'/connectors/joomleague.php');


$jinput = JFactory::getApplication()->input;
$ajax	= $jinput->post->getInt('ajaxMListMod',0);
$match_id = $jinput->post->getInt('match_id',0);
$nr 	= $jinput->post->getInt('nr',-1);
$ajaxmod = $jinput->post->getInt('ajaxmodid',0);
$jltemplate = $params->get('template','default');

JHtml::_('behavior.framework');

$doc = JFactory::getDocument();
$doc->addScript(_JLMATCHLISTMODURL.'assets/js/mod_joomleague_matches.js');
$doc->addStyleSheet(_JLMATCHLISTMODURL.'tmpl/'.$jltemplate.'/mod_joomleague_matches.css');
$cssimgurl = ($params->get('use_icons') != '-1') ? _JLMATCHLISTMODURL.'assets/images/'.$params->get('use_icons').'/'
: _JLMATCHLISTMODURL.'assets/images/';
$doc->addStyleDeclaration('
div.tool-tip div.tool-title a.sticky_close{
	display:block;
	position:absolute;
	background:url('.$cssimgurl.'cancel.png) !important;
	width:16px;
	height:16px;
}
');
JHtml::_('behavior.tooltip');
$doc->addScriptDeclaration('
  window.addEvent(\'domready\', function() {
    if ($$(\'#modJLML'.$module->id.'holder .jlmlTeamname\')) addJLMLtips(\'#modJLML'.$module->id.'holder .jlmlTeamname\', \'over\');
  }
  );
  ');
$mod = new MatchesJoomleagueConnector($params, $module->id, $match_id);
$lastheading = '';
$oldprojectid = 0;
$oldround_id  = 0;
if($ajax == 0) { 
	echo '<div class="clearfix"><div id="modJLML'.$module->id.'holder" class="modJLMLholder">';
	echo '<div class="clearfix"></div>';

}
$matches = $mod->getMatches();

$cnt=($nr >= 0) ? $nr : 0;
if (count($matches) > 0){
	foreach ($matches AS $key => $match) {
		if(!isset($match['project_id'])) continue; 
		$styleclass=($cnt%2 == 1) ? $params->get('sectiontableentry1') : $params->get('sectiontableentry2');
		$show_pheading = false;
		$pheading = '';
		if (isset($match['type'])) {
			$heading = $params->get($match['type'].'_notice');
		}
		else { $heading = ''; }
		if ($match['project_id'] != $oldprojectid OR $match['round_id'] != $oldround_id) {
			if (!empty($match['heading'])) $show_pheading = true;
			$pheading .= $match['heading'];
		}
		include JModuleHelper::getLayoutPath('mod_joomleague_matches', $jltemplate.'/match');
		$lastheading = $heading;
		$oldprojectid = $match['project_id'];
		$oldround_id = $match['round_id'];
		$cnt++;
	}
}
elseif ($params->get('show_no_matches_notice') == 1) {
	echo '<br />'.$params->get('no_matches_notice').'<br />';
}
if($ajax == 0) {
	echo '</div></div>';
}