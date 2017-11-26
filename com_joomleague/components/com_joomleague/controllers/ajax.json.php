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

defined('_JEXEC') or die;


class JoomleagueControllerAjax extends JoomleagueController
{
	public function __construct($config = array())
	{
		// Get the document object.
		$document = Factory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		parent::__construct($config);
	}
	
	public function getprojectsoptions()
	{
		$app = Factory::getApplication();
		
		$season = $app->input->getInt('s');
		$league = $app->input->getInt('l');
		$ordering = $app->input->getInt('o');
		
		$model = $this->getModel('ajax');
		
		$res = $model->getProjectsOptions($season, $league, $ordering);
		
		echo json_encode($res);
		
		$app->close();
	}
	
	public function getroute()
	{
		$app = Factory::getApplication();
		$view = $app->input->get('view');
	
		switch ($view)
		{
			case "matrix":
				$link = JoomleagueHelperRoute::getMatrixRoute( $app->input->getVar('p'), $app->input->getVar('division'), $app->input->getVar('r') );
				break;
				
			case "teaminfo":
				$link = JoomleagueHelperRoute::getTeamInfoRoute( $app->input->getVar('p'), $app->input->getVar('tid') );
				break;
				
			case "referees":
				$link = JoomleagueHelperRoute::getRefereesRoute( $app->input->getVar('p') );
				break;
				
			case "results":
				$link = JoomleagueHelperRoute::getResultsRoute( $app->input->getVar('p'), $app->input->getVar('r'), $app->input->getVar('division') );
				break;
				
			case "resultsranking":
				$link = JoomleagueHelperRoute::getResultsRankingRoute( $app->input->getVar('p') );
				break;
				
			case "rankingmatrix":
				$link = JoomleagueHelperRoute::getRankingMatrixRoute( $app->input->getVar('p'), $app->input->getVar('r'), $app->input->getVar('division') );
				break;
				
			case "resultsrankingmatrix":
				$link = JoomleagueHelperRoute::getResultsRankingMatrixRoute( $app->input->getVar('p'), $app->input->getVar('r'), $app->input->getVar('division') );
				break;
				
			case "teamplan":
				$link = JoomleagueHelperRoute::getTeamPlanRoute( $app->input->getVar('p'), $app->input->getVar('tid'), $app->input->getVar('division') );
				break;
				
			case "roster":
				$link = JoomleagueHelperRoute::getPlayersRoute( $app->input->getVar('p'), $app->input->getVar('tid'), null, $app->input->getVar('division') );
				break;
				
			case "eventsranking":				
				$link = JoomleagueHelperRoute::getEventsRankingRoute( $app->input->getVar('p'), $app->input->getVar('division'),$app->input->getVar('tid') );
				break;
				
			case "curve":
				$link = JoomleagueHelperRoute::getCurveRoute( $app->input->getVar('p'),$app->input->getVar('tid'),0, $app->input->getVar('division') );
				break;
				
			case "statsranking":
				$link = JoomleagueHelperRoute::getStatsRankingRoute( $app->input->getVar('p'), $app->input->getVar('division') );
				break;
								
			default:
			case "ranking":
				$link = JoomleagueHelperRoute::getRankingRoute( $app->input->getVar('p'),$app->input->getVar('r'),null,null,0,$app->input->getVar('division') );
		}
		
		echo json_encode($link);
	}
}