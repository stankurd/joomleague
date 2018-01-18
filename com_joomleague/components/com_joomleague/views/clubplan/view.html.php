<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * View-Clubplan
 */
class JoomleagueViewClubPlan extends JLGView
{
	public function display($tpl=null)
	{
		// Get a reference of the page instance in joomla

		$uri = Uri::getInstance();
		$this->model = $this->getModel();
		$this->project = $this->model->getProject();
		$this->overallconfig = $this->model->getOverallConfig();
		$this->config = $this->model->getTemplateConfig($this->getName());
		$this->favteams = $this->model->getFavTeams();
		$this->club = $this->model->getClub();
		switch ($this->config['type_matches']) {
			case 0 : case 4 : // all matches
				$this->allmatches = $this->model->getAllMatches($this->config['MatchesOrderBy']);
				break;
			case 1 : // home matches
				$this->homematches = $this->model->getHomeMatches($this->config['MatchesOrderBy']);
				break;
			case 2 : // away matches
				$this->awaymatches = $this->model->getAwayMatches($this->config['MatchesOrderBy']);
				break;
			default: // home+away matches
				$this->homematches = $this->model->getHomeMatches($this->config['MatchesOrderBy']);
				$this->awaymatches = $this->model->getAwayMatches($this->config['MatchesOrderBy']);
				break;
		}
		$this->startdate = $this->model->getStartDate();
		$this->enddate = $this->model->getEndDate();
		$this->teams = $this->model->getTeams();
		$this->action = $uri->toString();

		$this->setPageTitle();
		$this->addFeedLink();

		parent::display($tpl);
	}

	private function setPageTitle()
	{
		$titleInfo = JoomleagueHelper::createTitleInfo(Text::_('COM_JOOMLEAGUE_CLUBPLAN_PAGE_TITLE'));
		if (!empty( $this->club ) )
		{
			$titleInfo->clubName = $this->club->name;
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$this->pagetitle = JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]);
		$document = Factory::getDocument();
		$document->setTitle($this->pagetitle);
	}

	private function addFeedLink()
	{
		if (!empty($this->club->id))
		{
			$rssVar = '&cid='.$this->club->id;
		}
		elseif (!empty($this->project->id))
		{
			$rssVar = '&p='.$this->project->id;
		}
		else
		{
			$rssVar = '';
		}

		$feed = 'index.php?option=com_joomleague&view=clubplan'.$rssVar.'&format=feed';
		$rss = array('type' => 'application/rss+xml','title' => Text::_('COM_JOOMLEAGUE_CLUBPLAN_RSSFEED'));

		// add the links
		$document = Factory::getDocument();
		$document->addHeadLink(Route::_($feed.'&type=rss'),'alternate','rel',$rss);
	}

	function formatMatches($matches, $template, $noMatchesString)
	{
		if (!empty($matches))
		{
			$tm = count($matches);
			?>
			<h3><?php echo $tm . ' ' . Text::_('COM_JOOMLEAGUE_CLUBPLAN_MATCHES'); ?></h3>
			<?php
			$this->matches = $matches;
			echo $this->loadTemplate($template); //or use matches_sbd (sort by date)
		}
		else
		{
			?>
			<h3><?php echo Text::_($noMatchesString); ?></h3><br/>
			<?php
		}
	}
}
