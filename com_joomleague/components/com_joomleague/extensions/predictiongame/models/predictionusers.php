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
use Joomla\CMS\Access\Access;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

/*
// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');
*/

//require_once(JPATH_COMPONENT.DS.'models'.DS.'item.php');
require_once('prediction.php');

/**
 * Joomleague Component prediction Members Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */
class JoomleagueModelPredictionUsers extends JoomleagueModelPrediction
{
    var $config = null;

	function __construct()
	{
		parent::__construct();
	}
/*
	function savememberdata()
	{
		$result	= true;
		$app = Factory::getApplication();
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$post = $app->input->post->getArray();
		//echo '<br /><pre>~'.print_r($post,true).'~</pre><br />';

		$predictionGameID	= $app->input->post->get('prediction_id','','int');
		$joomlaUserID		= $app->input->post->get('user_id',		'','int');
		$predictionMemberID	= $app->input->post->get('member_id',	'','int');
		$show_profile		= $app->input->post->get('show_profile', '','int');
		$fav_teams		    = $app->input->post->get('fav_team',		'','array');
		$champ_teams		= $app->input->post->get('champ_tipp',	'','array');
		$slogan			    = $app->input->post->get('slogan',		'','string',JREQUEST_ALLOWRAW);
		$reminder		    = $app->input->post->get('reminder',		'','int');
		$receipt		    = $app->input->post->get('receipt',		'','int');
		$admintipp		    = $app->input->post->get('admintipp',	'','int');
		$group_id		    = $app->input->post->get('group_id',		'','int');
		$picture		    = $app->input->post->get('picture',		'','string',JREQUEST_ALLOWRAW);
		$aliasName		    = $app->input->post->get('aliasName',	'','string',JREQUEST_ALLOWRAW);

		$pRegisterDate		= $app->input->post->get('registerDate',	'',	'date',JREQUEST_ALLOWRAW);
		$pRegisterTime		= $app->input->post->get('registerTime',	'',	'time',JREQUEST_ALLOWRAW);
		//echo '<br /><pre>~'.print_r($pRegisterDate,true).'~</pre><br />';
		//echo '<br /><pre>~'.print_r($pRegisterTime,true).'~</pre><br />';

		$dFavTeams='';foreach($fav_teams AS $key => $value){$dFavTeams.=$key.','.$value.';';}$dFavTeams=trim($dFavTeams,';');
		$dChampTeams='';foreach($champ_teams AS $key => $value){$dChampTeams.=$key.','.$value.';';}$dChampTeams=trim($dChampTeams,';');

		$registerDate = JoomleagueHelper::convertDate($pRegisterDate,0) . ' ' . $pRegisterTime . ':00';
		//echo '<br /><pre>~'.print_r($registerDate,true).'~</pre><br />';
		$query = $db->getQuery(true);
		$query =	"	UPDATE	#__joomleague_prediction_member
							SET	registerDate='$registerDate',
								show_profile=$show_profile,
                                group_id=$group_id,
								fav_team='$dFavTeams',
								champ_tipp='$dChampTeams',
								slogan='$slogan',
								aliasName='$aliasName',
								reminder=$reminder,
								receipt=$receipt,
								admintipp=$admintipp,
								picture='$picture'
						WHERE	id=$predictionMemberID";
		//echo $query . '<br />';
		try{
		    $db->setQuery($query);
		    $db->execute();
		}
		catch (\RuntimeException $e)
		{
		    $this->setError($e->getMessage());
		    
		    return false;
		
			echo '<br />ERROR~' . $query . '~<br />';
		}

		return $result;
	}
*/
	function savememberdata()
	{
	    $document	= Factory::getDocument();
	    $option = Factory::getApplication()->input->getCmd('option');
	    $app = Factory::getApplication();
	    $jinput = $app->input;
	    // Create a new query object.
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    
	    $result	= true;
	    $post = $jinput->post->getArray();
	    
	    $predictionGameID = $post['prediction_id'];
	    $joomlaUserID = $post['user_id'];
	    $predictionMemberID	= $post['member_id'];
	    $show_profile = $post['show_profile'];
	    $fav_teams = $post['fav_team'];
	    if ( isset($post['champ_tipp']) )
	    {
	        $champ_teams = $post['champ_tipp'];
	    }
	    $slogan	= $post['slogan'];
	    $reminder = $post['reminder'];
	    $receipt = $post['receipt'];
	    $admintipp = $post['admintipp'];
	    $group_id = $post['group_id'];
	    $picture = $post['picture'];
	    $aliasName = $post['aliasName'];
	    $pRegisterDate = $post['registerDate'];
	    $pRegisterTime = $post['registerTime'];
	    
	    $dFavTeams = '';
	    foreach( $fav_teams AS $key => $value)
	    {
	        $dFavTeams .= $key.','.$value.';';
	    }
	    $dFavTeams = trim($dFavTeams,';');
	    $dChampTeams = '';
	    foreach( $champ_teams AS $key => $value)
	    {
	        $dChampTeams .= $key.','.$value.';';
	    }
	    $dChampTeams = trim($dChampTeams,';');
	    
	    $registerDate = JoomleagueHelper::convertDate($pRegisterDate,0) . ' ' . $pRegisterTime . ':00';
	    
	    // Must be a valid primary key value.
	    $object = new stdClass();
	    $object->id = $predictionMemberID;
	    $object->registerDate = $registerDate;
	    $object->show_profile = $show_profile;
	    $object->group_id = $group_id;
	    $object->fav_team = $dFavTeams;
	    if( $dChampTeams )
	    {
	        $object->champ_tipp = $dChampTeams;
	    }
	    $object->slogan = $slogan;
	    $object->aliasName = $aliasName;
	    $object->reminder = $reminder;
	    $object->receipt = $receipt;
	    $object->admintipp = $admintipp;
	    $object->picture = $picture;
	    
	    // Update their details in the table using id as the primary key.
	    $resultquery = $db->updateObject('#__joomleague_prediction_member', $object, 'id');
	    
	    if (!$resultquery)
	    {
	        $app->enqueueMessage(Text::_(__METHOD__.'<br><pre>'.print_r($db->getErrorMsg(),true).'</pre>'),'Error');
	        $result = false;
	    }
	    
	    return $result;
	}
	function showMemberPicture($outputUserName, $user_id = 0)
	{
	//global $app, $option;
	$app	= Factory::getApplication();
	$db = Factory::getDBO();
	$playerName = $outputUserName;
	$picture = '';
	$config = '';
	//$app->enqueueMessage(JText::_('username ->'.$outputUserName),'Notice');
	//$app->enqueueMessage(JText::_('user_id ->'.$user_id),'Notice');

	
	if ($config['show_photo'])
	{
	// von welcher komponente soll das bild kommen
	// und ist die komponente installiert
	$query = $db->getQuery(true);
	$query = "SELECT element
				FROM #__extensions
				WHERE element LIKE '" . $this->config['show_image_from'] . "'" ;
	$db->setQuery($query);
	$results = $db->loadResult();
	if ( !$results )
	{
    $app->enqueueMessage(Text::_('die komponente '.$this->config['show_image_from'].' ist f&uuml;r das profilbild nicht installiert'),'Error');
    }
	$app->enqueueMessage(Text::_('komponente ->'.$this->config['show_image_from']),'Notice');


	switch ( $this->config['show_image_from'] )
	{
    case 'com_joomleague':
    case 'prediction':
    $picture = $this->predictionMember->picture;
		
    break;
    
    case 'com_cbe':
    $picture = 'components/com_cbe/assets/user.png';
    $query = $db->getQuery(true);
    $query = 'SELECT avatar
			FROM #__cbe_users
			WHERE userid = ' . (int)$user_id ;
	$db->setQuery($query);
	$results = $db->loadResult();
	if ( $results )
    {
    $picture = $results;
    }
    break;
    
    case 'com_comprofiler':
    $query = $db->getQuery(true);
    $query = 'SELECT avatar
			FROM #__comprofiler
			WHERE user_id = ' . (int)$user_id ;
	$db->setQuery($query);
	$results = $db->loadResult();
    //$app->enqueueMessage(JText::_('avatar com_comprofiler ->'.$results),'Notice');
    if ( $results )
    {
    $picture = 'images/comprofiler/'.$results;
    }
    break;
    
    case 'com_kunena':
    $picture = 'media/kunena/avatars/resized/size200/nophoto.jpg';
    $query = $db->getQuery(true);
    $query = 'SELECT avatar
			FROM #__kunena_users
			WHERE userid = ' . (int)$user_id ;
	$db->setQuery($query);
	$results = $db->loadResult();
    //$app->enqueueMessage(JText::_('avatar com_kunena ->'.$results),'Notice');
    if ( $results )
    {
    $picture = 'media/kunena/avatars/'.$results;
    }
    
    
    break;
    
    case 'com_community':
        $query = $db->getQuery(true);
        $query = 'SELECT avatar
			FROM #__community_users
			WHERE userid = ' . (int)$user_id ;
	$db->setQuery($query);
	$results = $db->loadResult();
    //$app->enqueueMessage(JText::_('avatar com_community ->'.$results),'Notice');
    if ( $results )
    {
    $picture = $results;
    }
    
    break;
    
    }
			//$imgTitle = JText::sprintf('JL_PRED_USERS_AVATAR_OF', $outputUserName, '');
			
			
	if ( !file_exists($picture) )
	{
		//$app->enqueueMessage(JText::_('user bild ->'.$picture.' ist nicht vorhanden'),'Error');
        $picture = JoomleagueHelper::getDefaultPlaceholder("player");
        //$app->enqueueMessage(JText::_('nehme standard ->'.$picture),'Notice');
	}
	//echo JHTML::image($picture, $imgTitle, array(' title' => $imgTitle));
	echo JoomleagueHelper::getPictureThumb($picture, $playerName,0,0);
	}
	else
	{
		echo '&nbsp;';
	}
    
	}
  
  /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = Factory::getApplication('site');
    // Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
	
	function memberPredictionData()
	{
		$dataObject = new stdClass();
		$dataObject->rankingAll		= 'X';
		$dataObject->lastTipp		= '';

		return $dataObject;
	}

	function getChampTippAllowed()
	{
		$allowed = false;
		$user =  Factory::getUser();

		if ($user->id > 0)
		{
		    $auth =  Access::getAuthorisedViewLevels($user->get('id'));
		    $aro_group = $user->getAuthorisedGroups();
		    

			if ((strtolower($aro_group->name) == 'Super Users') || (strtolower($aro_group->name) == 'administrator'))
			{
				$allowed = true;
			}
		}
		return $allowed;
	}

	function getPredictionProjectTeams($project_id)
	{
		$query = '	SELECT	pt.id AS value,
							t.name AS text

					FROM #__joomleague_project_team AS pt
						LEFT JOIN #__joomleague_team AS t ON t.id=pt.team_id

					WHERE pt.project_id=' . (int)$project_id . '
					ORDER by text';

		//echo "<br />$query</br />";
		$this->_db->setQuery( $query );
		$results = $this->_db->loadObjectList();
		//echo '<br /><pre>~' . print_r($results,true) . '~</pre><br />';
		return $results;
	}
	
    /**
     * get data for pointschart
     * @return  
     */
		function getPointsChartData( )
		{
			$pgid	= $this->_db->Quote($this->predictionGameID);
			$uid	= $this->_db->Quote($this->predictionMemberID);

/*

SELECT rounds.id, 
rounds.id AS roundcode, 
rounds.name, 
SUM(pr.points) AS points 
FROM jos_joomleague_round AS rounds 
INNER JOIN jos_joomleague_match AS matches 
ON rounds.id = matches.round_id 
LEFT JOIN jos_joomleague_prediction_result AS pr 
ON pr.match_id = matches.id 				   
WHERE rounds.project_id = 1
AND (matches.cancel IS NULL OR matches.cancel = 0)
GROUP BY rounds.roundcode

*/

			$query = ' SELECT rounds.id, '
			     . ' rounds.roundcode AS roundcode, '
				   . ' rounds.name, '
				   . ' SUM(pr.points) AS points '
			     . ' FROM #__joomleague_round AS rounds '
			     . ' INNER JOIN #__joomleague_match AS matches ON rounds.id = matches.round_id '
			     . ' LEFT JOIN #__joomleague_prediction_result AS pr ON pr.match_id = matches.id '
           . ' LEFT JOIN #__joomleague_prediction_member AS prmem ON prmem.user_id = pr.user_id '
			     . ' WHERE pr.prediction_id = '.$pgid
				   . '  AND (matches.cancel IS NULL OR matches.cancel = 0)'
           . '  AND prmem.id = '.$uid			   
			     . ' GROUP BY rounds.roundcode'
			       ;
    		$this->_db->setQuery( $query );
    		$this->result = $this->_db->loadObjectList();
    		return $this->result;
		}	

    /**
     * get data for rankschart
     * @return  
     */
		function getRanksChartData( )
		{

		}		
}
?>