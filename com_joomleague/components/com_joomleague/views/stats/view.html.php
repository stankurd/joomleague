<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Stats
 */
class JoomleagueViewStats extends JLGView
{
	public function display($tpl = null)
	{
		// Get a refrence of the page instance in joomla
		$document = Factory::getDocument();

		$model = $this->getModel();
		$config = $model->getTemplateConfig($this->getName());

		$tableconfig = $model->getTemplateConfig("ranking");
		$eventsconfig = $model->getTemplateConfig("eventsranking");
		$flashconfig = $model->getTemplateConfig("flash");


		$this->project=$model->getProject();
		if ( isset( $this->project ) )
		{
			$highest_home = $model->getHighestHome();
			$highest_away = $model->getHighestAway();
			
			$this->division=$model->getDivision();
			$this->overallconfig=$model->getOverallConfig();
			if (!isset($this->overallconfig['seperator']))
			{
				$this->overallconfig['seperator'] = ":";
			}
			$this->config=$config;

			$this->tableconfig=$tableconfig;
			$this->eventsconfig=$eventsconfig;
			$this->actualround=$model->getCurrentRoundNumber();

			$this->highest_home=$highest_home;
			$this->highest_away=$highest_away;
			$this->totals=$model->getSeasonTotals();
			$this->totalrounds=$model->getTotalRounds();
			$this->attendanceranking=$model->getAttendanceRanking();
			$this->bestavg=$model->getBestAvg();
			$this->bestavgteam=$model->getBestAvgTeam();
			$this->worstavg=$model->getWorstAvg();
			$this->worstavgteam=$model->getWorstAvgTeam();
				
			//hightest home
			$hhHomeTeaminfo = $model->getTeaminfo($highest_home->project_hometeam_id);
			$this->hhHomeTeaminfo=$hhHomeTeaminfo;
			$hhAwayTeaminfo = $model->getTeaminfo($highest_home->project_awayteam_id);
			$this->hhAwayTeaminfo=$hhAwayTeaminfo;
				
			//highest_away
			$haHomeTeaminfo = $model->getTeaminfo($highest_away->project_hometeam_id);
			$this->haHomeTeaminfo=$haHomeTeaminfo;
			$haAwayTeaminfo = $model->getTeaminfo($highest_away->project_awayteam_id);
			$this->haAwayTeaminfo=$haAwayTeaminfo;
				
			$limit = 3;

			$this->limit=$limit;
			$this->_setChartdata(array_merge($flashconfig, $config));
		}
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_STATS_PAGE_TITLE'));
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		if (!empty( $this->division ) && $this->division->id != 0)
		{
			$titleInfo->divisionName = $this->division->name;
		}
		$this->pagetitle=JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document->setTitle($this->pagetitle);
		
		parent::display($tpl);
	}

	function _setChartdata($config)
	{
		require_once JLG_PATH_SITE."/assets/classes/open-flash-chart/open-flash-chart.php";

		$data = $this->get('ChartData');
		// Calculate Values for Chart Object
		$forSum = array();
		$againstSum = array();
		$matchDayGoalsCount = array();
		$round_labels = array();

		foreach( $data as $rw )
		{
			if (!$rw->homegoalspd) $rw->homegoalspd = 0;
			if (!$rw->guestgoalspd) $rw->guestgoalspd = 0;
			$homeSum[] = (int)$rw->homegoalspd;
			$awaySum[] = (int)$rw->guestgoalspd;
			// check, if both results are missing and avoid drawing the flatline of "0" goals for not played games yet
			if ((!$rw->homegoalspd) && (!$rw->guestgoalspd))
			{
				$matchDayGoalsCount[] = null;
			}
			else
			{
				$matchDayGoalsCount[] = (int)$rw->homegoalspd + $rw->guestgoalspd;
			}
			$round_labels[] = $rw->roundcode;
		}

		$chart = new open_flash_chart();
		//$chart->set_title( $title );
		$chart->set_bg_colour($config['bg_colour']);

		if(!empty($homeSum)&&(!empty($awaySum)))
		{
			if ( $config['home_away_stats'] )
			{
				$bar1 = new $config['bartype_1']();
				$bar1->set_values( $homeSum );
				$bar1->set_tooltip( Text::_('COM_JOOMLEAGUE_STATS_HOME'). ": #val#" );
				$bar1->set_colour( $config['bar1'] );
				$bar1->set_on_show(new bar_on_show($config['animation_1'], $config['cascade_1'], $config['delay_1']));
				$bar1->set_key(Text::_('COM_JOOMLEAGUE_STATS_HOME'), 12);

				$bar2 = new $config['bartype_2']();
				$bar2->set_values( $awaySum );
				$bar2->set_tooltip(   Text::_('COM_JOOMLEAGUE_STATS_AWAY'). ": #val#" );
				$bar2->set_colour( $config['bar2'] );
				$bar2->set_on_show(new bar_on_show($config['animation_2'], $config['cascade_2'], $config['delay_2']));
				$bar2->set_key(Text::_('COM_JOOMLEAGUE_STATS_AWAY'), 12);

				$chart->add_element($bar1);
				$chart->add_element($bar2);
			}
		}
		// total
		$d = new $config['dotstyle_3']();
		$d->size((int)$config['line3_dot_strength']);
		$d->halo_size(1);
		$d->colour($config['line3']);
		$d->tooltip(Text::_('COM_JOOMLEAGUE_STATS_TOTAL2').' #val#');

		$line = new line();
		$line->set_default_dot_style($d);
		$line->set_values( $matchDayGoalsCount );
		$line->set_width( (int) $config['line3_strength'] );
		$line->set_key(Text::_('COM_JOOMLEAGUE_STATS_TOTAL'), 12);
		$line->set_colour( $config['line3'] );
		$line->on_show(new line_on_show($config['l_animation_3'], $config['l_cascade_3'], $config['l_delay_3']));
		$chart->add_element($line);


		$x = new x_axis();
		$x->set_colours($config['x_axis_colour'], $config['x_axis_colour_inner']);
		$x->set_labels_from_array($round_labels);
		$chart->set_x_axis( $x );
		$x_legend = new x_legend( Text::_('COM_JOOMLEAGUE_STATS_ROUNDS') );
		$x_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_x_legend( $x_legend );

		$y = new y_axis();
		$y->set_range( 0, @max($matchDayGoalsCount)+2, 1);
		$y->set_steps(round(@max($matchDayGoalsCount)/8));
		$y->set_colours($config['y_axis_colour'], $config['y_axis_colour_inner']);
		$chart->set_y_axis( $y );
		$y_legend = new y_legend( Text::_('COM_JOOMLEAGUE_STATS_GOALS') );
		$y_legend->set_style( '{font-size: 15px; color: #778877}' );
		$chart->set_y_legend( $y_legend );

		$this->chartdata=$chart;
	}
}
