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
 * TeamStaff Table class
 */
class TableTeamStaff extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__joomleague_team_staff', 'id', $db);
	}

	
	public function canDelete($id)
	{
		// the staff cannot be deleted if assigned to games
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_staff '
		       . ' WHERE team_staff_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY team_staff_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Text::sprintf('STAFF ASSIGNED TO %d GAMES', $res));
			return false;
		}
		
		// the staff cannot be deleted if has stats
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_staff_statistic '
		       . ' WHERE team_staff_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY team_staff_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Text::sprintf('%d STATS ASSIGNED TO STAFF', $res));
			return false;
		}
		
		return true;
	}

	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.team_staff.'.(int) $this->$k;
	}
}
