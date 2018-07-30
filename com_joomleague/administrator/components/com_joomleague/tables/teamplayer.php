<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

// Include library dependencies
jimport( 'joomla.filter.input');

/**
 * TeamPlayer Table class
 */
class TableTeamPlayer extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_team_player', 'id', $db);
	}

	
	/**
	 * Default delete method
	 **
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	public function delete( $oid=null )
	{
		//TODO: check that there are no events and and matches associated to this player

		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		$query = 'DELETE FROM '.$this->getDbo()->quoteName( $this->_tbl ).
				' WHERE '.$this->_tbl_key.' = '. $this->getDbo()->Quote($this->$k);
		$this->getDbo()->setQuery( $query );

		if ($this->getDbo()->execute())
		{
			return true;
		}
		else
		{
			$this->setError($this->getDbo()->getErrorMsg());
			return false;
		}
	}

	
	public function canDelete($id)
	{
		// cannot be deleted if assigned to games
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_player '
		       . ' WHERE teamplayer_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY teamplayer_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Text::sprintf('PLAYER ASSIGNED TO %d GAMES', $res));
			return false;
		}
		
		// cannot be deleted if has events
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_event '
		       . ' WHERE teamplayer_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY teamplayer_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Text::sprintf('%d EVENTS ASSIGNED TO PLAYER', $res));
			return false;
		}
		
		// cannot be deleted if has stats
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_statistic '
		       . ' WHERE teamplayer_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY teamplayer_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Text::sprintf('%d STATS ASSIGNED TO PLAYER', $res));
			return false;
		}
		
		return true;
	}
	
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.team_player.'.(int) $this->$k;
	}
}
