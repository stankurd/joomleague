<?php
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Language\Text;

/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/


// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . '/models/list.php' );

/**
 * Joomleague Component PredictionGames Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */
class JoomleagueModelPredictionGames extends JoomleagueModelList
{
	var $_identifier = "predgames";
	
	function _buildQuery()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();        
        // Create a new query object.
        $query = $db->getQuery(true);
        $query->select(array('pre.*', 'u.name AS editor'))
        ->from('#__joomleague_prediction_game AS pre')
        ->join('LEFT', '#__users AS u ON u.id = pre.checked_out');
        if ($where)
        {
            $query->where($where);
        }
        if ($orderby)
        {
            $query->order($orderby);
        }		
		return $query;
	}

	function _buildContentOrderBy()
	{
		$app		= Factory::getApplication();
		$option		= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ( $filter_order == 'pre.name' )
		{
			$orderby 	= 'pre.name ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= '' . $filter_order . ' ' . $filter_order_Dir . ' , pre.name ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$app			= Factory::getApplication();
		$option			= 'com_joomleague';

		$filter_state		= $app->getUserStateFromRequest( $option . 'pre_filter_state',		'filter_state',		'',			'word' );
		$filter_order		= $app->getUserStateFromRequest( $option . 'pre_filter_order',		'filter_order',		'pre.name',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'pre_filter_order_Dir',	'filter_order_Dir',	'',			'word' );
		$search				= $app->getUserStateFromRequest( $option . 'pre_search',				'search',			'',			'string' );
		$search_mode		= $app->getUserStateFromRequest( $option . 'pre_search_mode',			'search_mode',		'',			'string' );
		$search				= StringHelper::strtolower( $search );

		$where = array();
		$prediction_id = (int) $app->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'pre.id = ' . $prediction_id;
		}

		if ( $search )
		{
			$where[] = "LOWER(pre.name) LIKE " . $db->Quote( $search . '%' );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'pre.published = 1';
			}
			elseif ($filter_state == 'U' )
				{
					$where[] = 'pre.published = 0';
				}
		}

		$where 	= ( count( $where ) ? ''. implode( ' AND ', $where ) : '' );

		return $where;
	}

	function getChilds( $pred_id, $all = false )
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$what = 'pro.*';
		if ( $all )
		{
			$what = 'pro.project_id';
		}
		$query
		->select($what)
		->select('joo.name AS project_name')
		->from('#__joomleague_prediction_project AS pro')
		->leftJoin('#__joomleague_project AS joo ON joo.id=pro.project_id')
		->where('pro.prediction_id = ' . $pred_id);
		$db->setQuery( $query );
		if ( $all )
		{
			return $db->loadColumn();
		}
		return $db->loadAssocList( 'id' );
	}

	function getAdmins( $pred_id, $list = false )
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$as_what = '';
		if ( $list )
		{
			$as_what = ' AS value';
		}
		$query
		      ->select('user_id' . $as_what)
		      ->from('#__joomleague_prediction_admin')
		      ->where('prediction_id =' .$pred_id);
		$db->setQuery( $query );
		if ( $list )
		{
			return $db->loadObjectList();
		}
		else
		{
			return $db->loadColumn();
		}
	}

	/**
	* Method to return a prediction games array
	*
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getPredictionGames()
	{
	$app = Factory::getApplication();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	$query
	       ->select('id AS value')
	       ->select('name AS text')
	       ->from('#__joomleague_prediction_game')
	       ->order('name');
		try{
		    $db->setQuery($query);
		    $result = $db->loadObjectList();
		}
		catch (RunTimeException $e)
		{
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		    return false;
		}
			return $result;
		}
}
?>