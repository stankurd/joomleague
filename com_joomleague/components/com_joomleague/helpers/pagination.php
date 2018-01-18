<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JLG_PATH_ADMIN.'/models/jlgitem.php';
require_once JLG_PATH_ADMIN.'/models/jlglist.php';

require_once JLG_PATH_ADMIN.'/models/round.php';
require_once JLG_PATH_ADMIN.'/models/rounds.php';

class JoomleaguePagination
{

	/**
	 * create and return the round page navigation
	 *
	 * @param object $project
	 * @return string
	 */
	static function pagenav($project)
	{
	    $app = Factory::getApplication();
		$pageNav = '';
		$spacer2 = '&nbsp;&nbsp;';
		$spacer4 = '&nbsp;&nbsp;&nbsp;&nbsp;';
		$roundid = $app->input->getInt( "r", $project->current_round);
		$mytask = $app->input->getVar('task','','request','word');
		$view = $app->input->getVar('view','','request','word');
		$layout = $app->input->getVar('layout','','request','word');
		$controller = $app->input->getVar('controller');
		$divLevel = $app->input->getInt('divLevel',0);
		$division = $app->input->getInt('division',0);
		$firstlink = '';
		$lastlink = '';
		$mdlRound = BaseDatabaseModel::getInstance("Round", "JoomleagueModel");
		$mdlRounds = BaseDatabaseModel::getInstance("Rounds", "JoomleagueModel");
		//$mdlRounds->setProjectId($project->id);

		$firstRound			= $mdlRounds->getFirstRound($project->id);
		$lastRound			= $mdlRounds->getLastRound($project->id);
		$previousRound 		= $mdlRounds->getPreviousRound($roundid, $project->id);
		$nextRound			= $mdlRounds->getNextRound($roundid, $project->id);
		$currentRoundcode 	= $mdlRound->getRoundcode($roundid);
		$arrRounds 			= $mdlRounds->getRoundsOptions($project->id);
		$rlimit 			= count($arrRounds);

		$params = array();
		$params['option'] = 'com_joomleague';
		if ($view){$params['view'] = $view;}
		$params['p'] = $project->id;
		if ($controller){$params['controller'] = $controller;}
		if ($layout){$params['layout'] = $layout;}
		if ($mytask){$params['task'] = $mytask;}
		if ($division > 0){$params['division'] = $division;}
		if ($divLevel > 0){$params['divLevel'] = $divLevel;}
		$prediction_id = $app->input->getInt("prediction_id",0);
		if($prediction_id >0) {
			$params['prediction_id']= $prediction_id;
		}
		
		$query = Uri::buildQuery($params);
		$link = Route::_('index.php?' . $query);
		$backward = $mdlRound->getRoundId($currentRoundcode-1, $project->id);
		$forward = $mdlRound->getRoundId($currentRoundcode+1, $project->id);

		if ($firstRound['id'] != $roundid)
		{
			$params['r'] = $backward;
			$query = Uri::buildQuery($params);
			$link = Route::_('index.php?' . $query . '#com_joomleague_top');
			$prevlink = HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_GLOBAL_PREV'));

			$params['r'] = $firstRound['id'];
			$query = Uri::buildQuery($params);
			$link = Route::_('index.php?' . $query . '#com_joomleague_top');
			$firstlink = HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_GLOBAL_PAGINATION_START')) . $spacer4;
		}
		else
		{
			$prevlink = Text::_('COM_JOOMLEAGUE_GLOBAL_PREV');
			$firstlink = Text::_('COM_JOOMLEAGUE_GLOBAL_PAGINATION_START') . $spacer4;
		}
		if ($lastRound['id'] != $roundid)
		{
			$params['r'] = $forward;
			$query = Uri::buildQuery($params);
			$link = Route::_('index.php?'.$query.'#com_joomleague_top');
			$nextlink = $spacer4;
			$nextlink .= HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_GLOBAL_NEXT'));

			$params['r'] = $lastRound['id'];
			$query = Uri::buildQuery($params);
			$link = Route::_('index.php?' . $query . '#com_joomleague_top');
			$lastlink = $spacer4 . HTMLHelper::link($link,Text::_('COM_JOOMLEAGUE_GLOBAL_PAGINATION_END'));
		}
		else
		{
			$nextlink = $spacer4 . Text::_('COM_JOOMLEAGUE_GLOBAL_NEXT');
			$lastlink = $spacer4 . Text::_('COM_JOOMLEAGUE_GLOBAL_PAGINATION_END');
		}
		$limit = count($arrRounds);
		$low = $currentRoundcode - 3;
		$high = $currentRoundcode + 3;
		for ($counter=1; $counter <= $limit; $counter++)
		{
				$round = $arrRounds[$counter-1];
				$roundcode = (int) $round->roundcode;
				if($roundcode < $low || $roundcode > $high) continue;
				if ( $roundcode < 10 )
				{
					$pagenumber = '0' . $roundcode;
				}
				else
				{
					$pagenumber = $roundcode;
				}
				if ($round->id != $roundid)
				{
					$params['r']= $round->id;
					$query		= Uri::buildQuery($params);
					$link		= Route::_('index.php?' . $query . '#com_joomleague_top');
					$pageNav   .= $spacer4 . HTMLHelper::link($link,$pagenumber);
				}
				else
				{
					$pageNav .= $spacer4 . $pagenumber;
				}
		}
		return '<span class="pageNav">&laquo;' . $spacer2 . $firstlink . $prevlink . $pageNav . $nextlink .  $lastlink . $spacer2 . '&raquo;</span>';
	}

	function pagenav2($jl_task,$rlimit,$currentRoundcode=0,$user='',$mode='')
	{
	    $app = Factory::getApplication();
		$mytask = $app->input->getVar('task',false);
		$divLevel = $app->input->getInt('divLevel',0);
		$division = $app->input->getInt('division',0);

		$pageNav2 = '<form action="" method="get" style="display:inline;">';
		$pageNav2 .= '<select class="inputbox" onchange="joomleague_changedoc(this)">';

		$params = array();
		$params['option'] = 'com_joomleague';
		$params['controller'] = $jl_task;
		$params['p'] = $this->projectid;
		if ($user){$params['uid'] = $user;}
		if ($mode){$params['mode'] = $mode;}
		if ($mytask){$params['task'] = $mytask;}
		if ($division > 0){$params['division'] = $division;}
		if ($divLevel > 0){$params['divLevel'] = $divLevel;}

		for ($counter=1; $counter <= $rlimit; $counter++)
		{
			if ($counter< 10){$pagenumber="0" . $counter;}else{$pagenumber = $counter;}
			if ($counter <= $rlimit)
			{
				$params['r'] = $counter;
				$query = Uri::buildQuery($params);
				$link  = Route::_('index.php?' . $query);

				$pageNav2 .= "<option value='".$link."'";
				if ($counter==$currentRoundcode)
				{
					$pageNav2 .= " selected='selected'";
				}
				$pageNav2 .= '>';
			}
			$pageNav2 .= $pagenumber . '</option>';
		}
		$pageNav2 .= '</select></form>';
		return $pageNav2;
	}

}
?>