<?php
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
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\String\StringHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . '/models/list.php' );
require_once(JLG_PATH_EXTENSION_PREDICTIONGAME.'/admin/models/predictionmember.php');


/**
 * Joomleague Component prediction members Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.02a
 */

class JoomleagueModelPredictionMembers extends JoomleagueModelList
{
	var $_identifier = "predmembers";
	
	function _buildQuery()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
        
        // Create a new query object.
        $query->select(array('tmb.*','u.name AS realname', 'u.username AS username', 'p.name AS predictionname' ))
        ->from('#__joomleague_prediction_member AS tmb')
        ->join('LEFT', '#__joomleague_prediction_game AS p ON p.id = tmb.prediction_id')
        ->join('LEFT', '#__users AS u ON u.id = tmb.user_id');

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
		$option			= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'tmb_filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmb_filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ( $filter_order == 'u.username' )
		{
			$orderby 	= 'u.username ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= '' . $filter_order . ' ' . $filter_order_Dir . ' , u.username ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$app		= Factory::getApplication();
		$option			= 'com_joomleague';

		$filter_state		= $app->getUserStateFromRequest( $option . 'tmb_filter_state',		'filter_state',		'',				'word' );
		$filter_order		= $app->getUserStateFromRequest( $option . 'tmb_filter_order',		'filter_order',		'u.username',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmb_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $app->getUserStateFromRequest( $option . 'tmb_search',				'search',			'',				'string' );
		$search_mode		= $app->getUserStateFromRequest( $option . 'tmb_search_mode',			'search_mode',		'',				'string' );
		$search				= StringHelper::strtolower( $search );

		$where = array();
		$prediction_id = (int) $app->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'tmb.prediction_id = ' . $prediction_id;
		}

		if ( $search )
		{
			$where[] = "LOWER(u.username) LIKE " . $db->Quote( $search . '%' );
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'tmb.approved = 1';
			}
			elseif ($filter_state == 'U' )
				{
					$where[] = 'tmb.approved = 0';
				}
		}

		$where 	= ( count( $where ) ? ''. implode( ' AND ', $where ) : '' );

		return $where;
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
	               $db->setQuery( $query );
	               $result = $db->loadObjectList();
	           }
	           catch (RunTimeException $e)
    	           {
    	               $app->enqueueMessage(Text::_($e->getMessage()), 'error');
    	               return false;
    	           }
    	           return $result;
	}
	
	function getPredictionProjectName($predictionID)
	{
	//$db = $this->getDBO();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	$app			= Factory::getApplication();
	$option			= 'com_joomleague';
	$query
	       ->select('ppj.name AS pjName')
	       ->from('#__joomleague_prediction_game AS ppj')
	       ->where('ppj.id = ' . $predictionID);
	       try{
        		$db->setQuery($query);
        		$result = $db->loadResult();
	           }
	       catch (RunTimeException $e)
    	       {
    	           $app->enqueueMessage(Text::_($e->getMessage()), 'error');
    	           return false;
    	       }
		return $result;
	}
	
	function getPredictionMembers($prediction_id)
	{
	$app = Factory::getApplication();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	$query
	       ->select('pm.user_id AS value')
	       ->select('u.name AS text')
	       ->from('#__joomleague_prediction_member AS pm')
	       ->leftJoin('#__users AS u ON	u.id = pm.user_id')
	       ->where('prediction_id = ' . (int) $prediction_id);
	       try{
                $db->setQuery($query);
            	$results = $db->loadObjectList();
	       }
	       catch (RunTimeException $e)
	       {
	           $app->enqueueMessage(Text::_($e->getMessage()), 'error');
	           return false;
	       }	       
    return $results;				
	}
	
	function getJLUsers($prediction_id)
	{
	$app = Factory::getApplication();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	//$not_in = array();
	$query
	       ->select('pm.user_id AS value')
	       ->from('#__joomleague_prediction_member AS pm')
	       ->leftJoin('#__users AS u ON	u.id = pm.user_id')
	       ->where('prediction_id = ' . (int) $prediction_id);
	       $db->setQuery($query);
	       $records = $db->loadColumn ();
	       if ( $predresult = $records ){
	           $query = $db->getQuery(true);
	           $query
	                   ->select('id AS value')
	                   ->select('name AS text')
	                   ->from('#__users')
	                   ->where('id not in (' . implode(",", $records ) .')')
	                   ->order('name');	                       
	       }
	       else
	       {
	           $query = $db->getQuery(true);
	           $query
	                ->select('id AS value')
	                ->select('name AS text')
	                ->from('#__users')
	                ->order('name');
	       }
	       try{
	         
        		$db->setQuery( $query );
        		$result = $db->loadObjectList();
	       }
	       catch (RunTimeException $e)
	       {
	           $app->enqueueMessage(Text::_($e->getMessage()), 'error');
	           return false;
	       }
	       return $result;
	}

	
	function save_memberlist()
	{
	$app = Factory::getApplication();
	$date = Factory::getDate();
	$user = Factory::getUser();
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	$option	= 'com_joomleague';
	$post = $app->input->post->getArray();
	$cid	= $app->input->post->getVar('cid', array(0), 'array');
	$prediction_id = (int) $cid[0];
  //echo '<br />save_memberlist post<pre>~' . print_r($post,true) . '~</pre><br />';
  
  //$app->enqueueMessage(JText::_('<br />save_memberlist post<pre>~' . print_r($post,true) . '~</pre><br />'),'Notice');
  //$app->enqueueMessage(JText::_('<br />prediction id<pre>~' . print_r($prediction_id,true) . '~</pre><br />'),'Notice');
  
  
  foreach ( $post['prediction_members'] as $key => $value )
  {
  $query->clear();
  $query->select('pm.id'); 
  $query->from('#__joomleague_prediction_member AS pm '); 
  $query->where('pm.prediction_id = ' . $prediction_id); 
  $query->where('pm.user_id = ' . $value); 
  $db->setQuery($query); 
  $result = $db->loadResult(); 

if ( !$result )
  $app->enqueueMessage(JText::_('<br />memberlist id<pre>~' . print_r($value,true) . '~</pre><br />'),'Notice');
  //$table = 'predictionmember';
  $table = 'predictionentry';
  $rowproject = Table::getInstance( $table, 'Table' );
  //$rowproject->load( $value );
  $rowproject->prediction_id = $prediction_id;
  $rowproject->user_id = $value;
  $rowproject->registerDate = HTMLHelper::date(time(),'Y-m-d H:i:s');
  $rowproject->approved = 1;
  $rowproject->modified = $date->toSql();
  $rowproject->modified_by = $user->get('id');
  if ( !$rowproject->store() )
  {
  echo 'project -> '.$value. ' nicht gesichert <br>';
  }
  else
  {
  echo 'project -> '.$value. ' gesichert <br>';
  }
        
  }
  
  }
	

}
?>