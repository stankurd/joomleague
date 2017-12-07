<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

jimport('joomla.html.pane');
jimport('joomla.functions');
HTMLHelper::_('bootstrap.tooltip');

/**
 * View-Ical
 */
class JoomleagueViewIcal extends JLGView
{
	public function display($tpl = null)
	{
		// Get a reference of the page instance in joomla
		$document	= Factory::getDocument();
		$model	 	= $this->getModel();

		$project	= $model->getProject();
		//$config		= $model->getTemplateConfig($this->getName());

		if (isset($project))
		{
			$this->project=$project;
		}
		$this->overallconfig=$model->getOverallConfig();
		$this->config=$this->overallconfig;
		$this->matches=$model->getMatches();
		$this->teams=$model->getTeamsFromMatches($this->matches);

		// load a class that handles ical formats.
		require_once JLG_PATH_SITE.'/helpers/iCalcreator.class.php';
		// create a new calendar instance
		$v = new vcalendar();

		foreach($this->matches as $match)
		{
			$hometeam = $this->teams[$match->team1];
			$home = sprintf('%s', $hometeam->name);
			$guestteam = $this->teams[$match->team2];
			$guest = sprintf('%s', $guestteam->name);
			$summary =  $match->project_name.': '.$home.' - '.$guest;

			//  check if match gots a date, if not it will not be included in ical
			if ($match->match_date)
			{
				$totalMatchTime = isset( $project ) ? ($project->halftime * ($project->game_parts - 1)) + $project->game_regular_time : 90;
				$start = JoomleagueHelper::getMatchStartTimestamp($match, 'Y-m-d H:i:s');
				$end = JoomleagueHelper::getMatchEndTimestamp($match, $totalMatchTime, 'Y-m-d H:i:s');

				// check if exist a playground in match or team or club
				$stringlocation	= '';
				$stringname	= '';
				if (!empty($match->playground_id))
				{
					$stringlocation= $match->playground_address.", ".$match->playground_zipcode." ".$match->playground_city;
					$stringname= $match->playground_name;
				}
				elseif (!empty($match->team_playground_id))
				{
					$stringlocation= $match->team_playground_address.", ".$match->team_playground_zipcode." ".$match->team_playground_city;
					$stringname= $match->team_playground_name;
				}
				elseif (!empty($match->club_playground_id))
				{
					$stringlocation= $match->club_playground_address.", ".$match->club_playground_zipcode." ".$match->club_playground_city;
					$stringname= $match->club_playground_name;
				}

				$location=$stringlocation;

				//if someone want to insert more in description here is the place
				$description=$stringname;

				// create an event and insert it in calendar
				$vevent = new vevent();

				$timezone = JoomleagueHelper::getMatchTimezone($match);
				$vevent->setProperty( "dtstart", $start, array( "TZID" => $timezone ));
				$vevent->setProperty( "dtend", $end, array( "TZID" => $timezone ));

				$vevent->setProperty( 'LOCATION', $location );
				$vevent->setProperty( 'summary', $summary );
				$vevent->setProperty( 'description', $description );

				$v->setComponent ( $vevent );
			}
		}

		$v->setProperty( "X-WR-TIMEZONE", $timezone );
		$xprops = array( "X-LIC-LOCATION" => $timezone );
		iCalUtilityFunctions::createTimezone( $v, $timezone, $xprops );

		$v->returnCalendar();
	}
}
