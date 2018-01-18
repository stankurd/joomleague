<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/models/results.php';

/**
 * View-Nextmatch
 */
class JoomleagueViewNextMatch extends JLGView
{
	public function display($tpl = null)
	{
	    $app = Factory::getApplication();
	    
		// Get a reference of the page instance in joomla
		$document= Factory::getDocument();

		$model = $this->getModel();
		$match = $model->getMatch();

		$config = $model->getTemplateConfig($this->getName());
		$tableconfig = $model->getTemplateConfig( "ranking" );

		$this->project=$model->getProject();
		$this->config=$config;
		$this->tableconfig=$tableconfig;
		$this->overallconfig=$model->getOverallConfig();
		if (!isset($this->overallconfig['seperator']))
		{
			$this->overallconfig['seperator'] = ":";
		}
		$this->match=$match;

		if ($match)
		{
			$newmatchtext = "";
			if($match->new_match_id > 0)
			{
				$ret = $model->getMatchText($match->new_match_id);
				$matchDate = JoomleagueHelper::getMatchDate($ret, Text::_('COM_JOOMLEAGUE_NEXTMATCH_NEXT_MATCHDATE'));
				$matchTime = JoomleagueHelperHtml::showMatchTime($ret, $this->config, $this->overallconfig, $this->project);
				$newmatchtext = $matchDate . " " . $matchTime . ", " . $ret->t1name . " - " . $ret->t2name;
			}
			$this->newmatchtext=$newmatchtext;
			$prevmatchtext = "";
			if($match->old_match_id > 0)
			{
				$ret = $model->getMatchText($match->old_match_id);
				$matchDate = JoomleagueHelper::getMatchDate($ret, Text::_('COM_JOOMLEAGUE_NEXTMATCH_PREVIOUS_MATCHDATE'));
				$matchTime = JoomleagueHelperHtml::showMatchTime($ret, $this->config, $this->overallconfig, $this->project);
				$prevmatchtext = $matchDate . " " . $matchTime . ", " . $ret->t1name . " - " . $ret->t2name;
			}
			$this->oldmatchtext=$prevmatchtext;

			$this->teams=$model->getMatchTeams();
			$this->referees=$model->getReferees();
			$this->playground=$model->getPlayground($this->match->playground_id);

			$this->homeranked=$model->getHomeRanked();		
			$this->awayranked=$model->getAwayRanked();
			$this->chances=$model->getChances();		

			$this->home_highest_home_win=$model->getHomeHighestHomeWin();
			$this->away_highest_home_win=$model->getAwayHighestHomeWin();
			$this->home_highest_home_def=$model->getHomeHighestHomeDef();
			$this->away_highest_home_def=$model->getAwayHighestHomeDef();
			$this->home_highest_away_win=$model->getHomeHighestAwayWin();
			$this->away_highest_away_win=$model->getAwayHighestAwayWin();
			$this->home_highest_away_def=$model->getHomeHighestAwayDef();
			$this->away_highest_away_def=$model->getAwayHighestAwayDef();

			$games = $model->getGames();
			$gamesteams = $model->getTeamsFromMatches( $games );
			$this->games=$games;
			$this->gamesteams=$gamesteams;
			
			
			$previousx = $this->get('previousx');
			$teams = $this->get('TeamsIndexedByPtid');
			
			$this->previousx=$previousx;
			$this->allteams=$teams;
		}
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_NEXTMATCH_PAGE_TITLE'));
		if (count($this->teams) == 2)
		{
			if (!empty($this->teams[0]))
			{
				$titleInfo->team1Name = $this->teams[0]->name;
			}
			if (!empty($this->teams[1]))
			{
				$titleInfo->team2Name = $this->teams[1]->name;
			}
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$division = $model->getDivision($app->input->getInt('division',0));
		if (!empty( $division ) && $division->id != 0)
		{
			$titleInfo->divisionName = $division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}
}
