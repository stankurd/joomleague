<?php 
use Joomla\CMS\Factory;
use Joomla\CMS\Feed\Feed;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Document\Feed\FeedItem;

defined('_JEXEC') or die;

/**
 * View-Results (feed)
 */
class JoomleagueViewResults extends JLGView
{

	public function display($tpl = null)
	{
		$document	= Factory::getDocument();
		$document->link = Route::_('index.php?option=com_joomleague');
		$model = $this->getModel();
		$this->config=$model->getTemplateConfig('results');
		$this->overallconfig=$model->getOverallConfig();
		$this->matches=$model->getMatches();
		$this->project=$model->getProject();
		$this->teams=$model->getTeamsIndexedByPtid();
		$dates = $this->sortByDate();
		foreach( $dates as $date => $games )
		{
			foreach($games as $game)
			{
				$item = new FeedItem();
				$team1 = $this->teams[$game->projectteam1_id];
				$team2 = $this->teams[$game->projectteam2_id];
				$result = $game->cancel > 0 ? $game->cancel_reason : $game->team1_result . "-" . $game->team2_result;
				$item->title 		= $team1->name. " - ".$team2->name." : ".$result;
				$item->link 		= Route::_( 'index.php?option=com_joomleague&view=matchreport&p=' .
				$game->project_id . '&mid=' . $game->id);
				$item->description 	= $game->summary;
				$item->date			= JoomleagueHelper::getMatchDate($game);
				$item->category   	= "results";
				// loads item info into rss array
				$document->addItem( $item );
			}
		}
	}

	/**
	 * return an array of matches indexed by date
	 *
	 * @return array
	 */
	public function sortByDate()
	{
		$dates=array();
		foreach ((array) $this->matches as $m)
		{
			$matchDate = JoomleagueHelper::getMatchDate($m);
			$dates[$matchDate][]=$m;
		}
		return $dates;
	}
}
