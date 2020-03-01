<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 *
 */
abstract class PredictionHelperRoute extends JoomleagueHelperRoute 
{
    public function __construct($app = null, $menu = null)
    {
        
    }
    
    /**
     * Generic method to preprocess a URL
     *
     * @param   array  $query  An associative array of URL arguments
     *
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     *
     * @since   3.3
     */
   
	
	public static function getPredictionMemberRoute($predictionID,$userID=null,$task=null,$projectID=null)
	{
		$params = array('option' => 'com_joomleague', 
						'view' => 'predictionusers', 
						'prediction_id' => $predictionID);

		if (!is_null($userID)){$params['uid']=$userID;}
		if (!is_null($projectID)){$params['pj']=$projectID;}
		if (!is_null($task)){$params['layout']=$task;}

		$query = PredictionHelperRoute::buildQuery($params);
		$link = Route::_('index.php?' . $query, false);

		return $link;
	}

	public static function getPredictionRulesRoute($predictionID)
	{
		$params = array('option' => 'com_joomleague', 
						'view' => 'predictionrules', 
						'prediction_id' => $predictionID);

		$query = PredictionHelperRoute::buildQuery($params);
		$link = Route::_('index.php?' . $query, false);

		return $link;
	}

	public static function getPredictionResultsRoute($predictionID,$roundID=null,$projectID=null,$anchor='')
	{
		$params = array('option' => 'com_joomleague', 
						'view' => 'predictionresults', 
						'prediction_id' => $predictionID);

		if (!is_null($projectID)){$params['pj']=$projectID;}
		if (!is_null($roundID)){$params['r']=$roundID;}
		$query = PredictionHelperRoute::buildQuery($params);
		//echo $query; die();
		$link = Route::_('index.php?' . $query . $anchor, false);

		return $link;
	}

	public static function getPredictionRankingRoute($predictionID,$projectID=null,$roundID=null,$anchor='')
	{
		$params = array('option' => 'com_joomleague', 
						'view' => 'predictionranking', 
						'prediction_id' => $predictionID);

		if (!is_null($projectID)){$params['pj']=$projectID;}
		if (!is_null($roundID)){$params['r']=$roundID;}

		$query = PredictionHelperRoute::buildQuery($params);
		$link = Route::_('index.php?' . $query, false);

		return $link;
	}

	public static function getPredictionTippEntryRoute($predictionID,$userID=null,$roundID=null,$projectID=null,$anchor='')
	{
		$params = array('option' => 'com_joomleague', 
						'view' => 'predictionentry', 
						'prediction_id' => $predictionID);

		//if (!is_null($projectID)){$params['pj']=$projectID;}
		if (!is_null($projectID)){$params['p']=$projectID;}
		if (!is_null($roundID)){$params['r']=$roundID;}
		if (!is_null($userID)){$params['uid']=$userID;}
		$query = PredictionHelperRoute::buildQuery($params);
		$link = Route::_('index.php?' . $query, false);
		return $link;
	}

	public static function buildQuery($parts)
	{
		if($item = PredictionHelperRoute::_findItem($parts))
		{
			$parts['Itemid'] = $item->id;
		};

		return Uri::buildQuery( $parts );
	}

	/**
	 * Determines the Itemid
	 *
	 * searches if a menuitem for this item exists
	 * if not the first match will be returned
	 *
	 * @param array The id and view
	 * @since 0.9
	 *
	 * @return int Itemid
	 */
	public static function _findItem($query)
	{
		$component = ComponentHelper::getComponent('com_joomleague');
		$site = new JSite();
		$menus	= $site->getMenu();
		$items	= $menus->getItems('component', $component->id);
		//$menus	=  SiteMenu::getMenu();
		//$items	= $menus->getItems('componentid', $component->id);
		$user 	=  Factory::getUser();
		$access = (int)$user->get('aid');

		if ($items) {
			foreach($items as $item)
			{
				if ((@$item->query['view'] == $query['view']) && ($item->published == 1) && ($item->access <= $access)) {

					switch ($query['view'])
					{
						case 'predictionentry':
						case 'predictionusers':
						case 'predictionresults':
						case 'predictionranking':
						case 'predictionrules':
							if ((int)@$item->query['prediction_id'] == (int) $query['prediction_id']) {
								return $item;
							}
							break;
					}
				}
			}

			//no menuitem exists -> return first possible match within project ?
			if (isset($query['prediction_id']) && $query['prediction_id'])
			{
				foreach($items as $item)
				{
					if (((int)@$item->query['prediction_id'] == (int) $query['prediction_id']) && $item->published == 1 && $item->access <= $access) {
						return $item;
					}
				}
			}

			//still no menuitem exists -> return first possible match
			foreach($items as $item)
			{
				if (isset($item->published) && $item->published == 1 && $item->access <= $access) {
					return $item;
				}
			}
		}

		return false;
	}	
	
}
?>