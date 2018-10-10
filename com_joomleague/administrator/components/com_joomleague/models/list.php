<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;

defined('_JEXEC') or die;

/**
 * List Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 */
class JoomleagueModelList extends JLGModel
{
	 /**
	 * list data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/* current project id */
	var $_project_id = 0;
	var $_identifier = "";

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest(	'global.list.limit', 
														'limit', 
														$app->getCfg('list_limit'), 
														'int' );
		$this->setState('limit', $limit );
		$limitstart	= $app->getUserStateFromRequest(	$option.'.'.$this->_identifier.'.limitstart',
															'limitstart', 
															0, 'int' );
		$this->setState('limitstart', $limitstart);
		$this->_project_id = $app->getUserState( $option . 'project', 0 );
	}

	/**
	 * Method to get List data
	 *
	 * @access public
	 * @return array
	 */
	function getData()
	{
		$app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Lets load the content if it doesn't already exist
		if ( empty($this->_data) )
		{
			$query = $this->_buildQuery();
			if ( !$this->_data = $this->_getList( $query, $this->getState('limitstart'), 
													$this->getState('limit') ) )
			try
			{
				//$db->execute();
			}
			
			catch (RuntimeException $e)
			{
				$this->setError($e->getMessage());
			}
			
		}
		return $this->_data;
	}

	/**
	 * Method to get the total number of items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_total ) )
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount( $query );
		}
		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the list
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport( 'joomla.html.pagination' );
			$this->_pagination = new Pagination(	$this->getTotal(), 
													$this->getState( 'limitstart' ), 
													$this->getState( 'limit' ) );
		}
		return $this->_pagination;
	}
	
	function getIdentifier() {
		return $this->_identifier;
	}
	
	function publish($pks=array(),$value=1)
	{
		$user 		= Factory::getUser();
		$table		= $this->getTable();
		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id'))) {
			$this->setError($table->getError());
			return false;
		}
		
		return true;
	}
	
	/**
	 * get details of current project
	 *
	 * @return object
	 */
	public function getProject()
	{
		$app	= Factory::getApplication();
		$option = $app->input->get('option');
		$project_id = $app->getUserState($option . 'project');

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('p.*');
		$query->from('#__joomleague_project AS p');
		$query->where('p.id = '.$project_id);
		$db->setQuery($query);
		$res = $db->loadObject();
		return $res;
	}
}
