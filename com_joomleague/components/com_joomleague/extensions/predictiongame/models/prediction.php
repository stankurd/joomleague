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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');
jimport('joomla.utilities.array');
jimport('joomla.utilities.arrayhelper') ;
jimport('joomla.utilities.utility' );
jimport('joomla.user.authorization' );
jimport('joomla.access.access' );


require_once(JLG_PATH_ADMIN.'/models/item.php');
require_once(JLG_PATH_ADMIN.'/models/rounds.php');

class JoomleagueModelPrediction extends JoomleagueModelItem
{
    
	var $_predictionGame		= null;
	var $predictionGameID		= 0;

	var $_predictionMember		= null;
	var $predictionMemberID		= 0;

	var $_predictionProjectS	= null;
	var $predictionProjectSIDs	= null;

	var $_predictionProject		= null;
	var $predictionProjectID	= null;
	
   
    
    var $joomlaUserID		= 0;
    var $roundID		= 0;
    var $pggroup		= 0;
    var $pggrouprank		= 0;
    var $pjID		= 0;
    var $isNewMember		= 0;
    
    var $tippEntryDone		= 0;
    var $from		= 0;
    var $to		= 0;
    var $type		= 0;
    var $page		= 0;
    
    var $table_config = '';

	function __construct()
	{
	    $app = Factory::getApplication();
	    $post = $app->input->post->getArray();
	    
		$this->predictionGameID		= $app->input->getInt('prediction_id',0);
		$this->predictionMemberID	= $app->input->getInt('uid',	0);
		$this->joomlaUserID			= $app->input->getInt('juid',	0);
		$this->roundID				= $app->input->getInt('r',		0);
		$this->pjID					= $app->input->getInt('p',		0);
		$this->isNewMember			= $app->input->getInt('s',		0);
		$this->tippEntryDone		= $app->input->getInt('eok',	0);
		$this->from  				= $app->input->getInt('from',	$this->roundID);
		$this->to	 				= $app->input->getInt('to',	$this->roundID);
		$this->type  				= $app->input->getInt('type',	0);
		$this->page  				= $app->input->getInt('page',	1);

		parent::__construct();
	}

	function getPredictionGame()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
	if (!$this->_predictionGame)
	{
	if ($this->predictionGameID > 0)
	{
	// Select some fields
        $query->select('*');
        $query->select("CONCAT_WS(':',id,alias) AS slug");
        $query->from('#__joomleague_prediction_game');
        $query->where('id='.$db->Quote($this->predictionGameID).' AND published=1');
	$db->setQuery($query,0,1);
	$this->_predictionGame=$db->loadObject();
			}
		}
		return $this->_predictionGame;
	}
 function getPredictionMemberAvatar($members, $configavatar)
  {
  
  // Reference global application object
        $app = Factory::getApplication();
        // JInput object
        $input = $app->input;
        $option = $input->getCmd('option');
    // Create a new query object.		
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
        
  $picture = '';
  $query->select('avatar');
  $query->where('userid = ' . (int)$members); 
  
  switch ( $configavatar )
		{
    
    case 'prediction':
    $picture = 'images/com_joomleague/database/placeholders/placeholder_150_2.png';
    // Select some fields
    $query->clear('select');
    $query->clear('where');
    $query->select('picture');
    $query->from('#__joomleague_prediction_member');  
    $query->where('user_id = ' . (int)$members);
    $query->where('prediction_id = '.self::$predictionGameID);
    break;
    
    case 'com_joomleague':
	  // alles ok
    break;
    
    case 'com_cbe15':
    $picture = 'images/cbe/'.$members.'.png';
    break;
    
    case 'com_cbe25':
    $picture = 'components/com_cbe/assets/user.png';
    $query->from('#__cbe_users');  
    break;
    
    case 'com_cbe':
    $picture = 'components/com_cbe/assets/user.png';
    $query->from('#__cbe_users'); 
    break;
    
    case 'com_kunena':
    $picture = 'media/kunena/avatars/resized/size200/nophoto.jpg';
    $query->from('#__kunena_users'); 
    break;
    
    case 'com_community':
    $query->from('#__community_users'); 
    break;
    
    case 'com_comprofiler':
    $query->clear('where');
    $query->from('#__comprofiler'); 
    $query->where('user_id = ' . (int)$members);
    break;
    
    }
    
    switch ( $configavatar )
		{
	case 'prediction':
        case 'com_comprofiler':
        case 'com_community':
        case 'com_cbe':
        case 'com_cbe25':
        case 'prediction':
        $db->setQuery($query);
		$results = $db->loadResult();
        if ( $results )
        {
        $picture = $results;
        }
        break;  
        case 'com_kunena':
        $db->setQuery($query);
		$results = $db->loadResult();
        if ( $results )
        {
        $picture = 'media/kunena/avatars/'.$results;
        }
        break;
        }  
 
 
  return $picture;
  
  }
  
  	function getPredictionMember()
	{
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
		if (!$this->_predictionMember)
		{
			if ($this->predictionMemberID > 0)
			{
				$query=" SELECT	pm.id AS pmID,
									pm.registerDate AS pmRegisterDate,
									pm.*, u.name, u.username
							FROM #__joomleague_prediction_member AS pm
								LEFT JOIN #__users AS u ON u.id=pm.user_id
							WHERE	pm.prediction_id=".$db->Quote($this->predictionGameID)." AND
									pm.id=".$db->Quote($this->predictionMemberID);
				$db->setQuery($query,0,1);
				$this->_predictionMember=$db->loadObject();
				if (isset($this->_predictionMember->pmID)){
					$this->predictionMemberID=$this->_predictionMember->pmID;
				}
			}
			else
			{
				$user= Factory::getUser();
				if ($user->id > 0)
				{
				    $query = $db->getQuery(true);
					$query=" SELECT	pm.id AS pmID,
										pm.registerDate AS pmRegisterDate,
										pm.*,
										u.*
								FROM #__joomleague_prediction_member AS pm
									LEFT JOIN #__users AS u ON u.id=pm.user_id
								WHERE	pm.prediction_id=".$db->Quote($this->predictionGameID)." AND
										pm.user_id=".$db->Quote($user->id);
					$db->setQuery($query,0,1);
					$this->_predictionMember=$db->loadObject();
					if (isset($this->_predictionMember->pmID))
					{
						$this->predictionMemberID=$this->_predictionMember->pmID;
					}
					else
					{
						$this->_predictionMember->id=0;
						$this->_predictionMember->pmID=0;
						$this->predictionMemberID=0;
					}
				}
				else
				{
					$this->_predictionMember->id=0;
					$this->_predictionMember->pmID=0;
					$this->predictionMemberID=0;
				}
			}
		}
		return $this->_predictionMember;
	}

	function getPredictionProjectS()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if (!$this->_predictionProjectS)
		{
			if ($this->predictionGameID > 0)
			{
				$query =	'	SELECT	pp.*,
										p.name AS projectName
								FROM #__joomleague_prediction_project AS pp
								LEFT JOIN #__joomleague_project AS p ON p.id=pp.project_id
								WHERE	pp.prediction_id='.$db->Quote($this->predictionGameID).' AND
										pp.published=1';
				$db->setQuery($query);
				$this->_predictionProjectS=$db->loadObjectList();
			}
		}
		return $this->_predictionProjectS;
	}

	function getPredictionOverallConfig()
	{
		return $this->getPredictionTemplateConfig('predictionoverall');
	}

	function getPredictionTemplateConfig($template)
	{
	    $app = Factory::getApplication();
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		$query =	"	SELECT t.params
						FROM #__joomleague_prediction_template AS t
						INNER JOIN #__joomleague_prediction_game AS p ON p.id=t.prediction_id
						WHERE	t.template=".$db->Quote($template)." AND
								p.id =".$db->Quote($this->predictionGameID);

		$db->setQuery($query);
		if (!$result=$db->loadResult())
		{
			if (isset($this->predictionGame) && ($this->predictionGame->master_template))
			{
				$query="	SELECT t.params
							FROM #__joomleague_Prediction_template AS t
							INNER JOIN #__joomleague_prediction_game AS p ON p.id=t.prediction_id
							WHERE	t.template=".$db->Quote($template)." AND
									p.id=".$db->Quote($this->predictionGame->master_template);

				$db->setQuery($query);
				if (!$result=$db->loadResult())
				{
					$app->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_PRED_MISSING_MASTER_TEMPLATE',$template,$predictionGame->master_template),'notice');
					//$app->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_PRED_MISSING_MASTER_TEMPLATE_HINT'),'notice');
					echo '<br /><br />';
					return false;
				}
			}
			else
			{
			    $app->enqueueMessage(500,Text::sprintf('COM_JOOMLEAGUE_PRED_MISSING_TEMPLATE',$template,$this->predictionGameID),'notice');
			    //$app->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_PRED_MISSING_MASTER_TEMPLATE_HINT'),'notice');
				echo '<br /><br />';
				return false;
			}
		}
		
		//$params=explode("\n",trim($result));
		$jRegistry = new Registry;
		$jRegistry->loadString($result);
		$configvalues = $jRegistry->toArray();
		
		
/*
		foreach($params AS $param)
		{
			list($name,$value)=explode('=',$param);
			$configvalues[$name]=$value;
		}
*/
		// check some defaults and init data for quicker access
		switch ($template)
		{
			case	'predictionoverall':	{
												if (!array_key_exists('sort_order_1',$configvalues))
												//for people updating,the ranking order won't be set until they edit
												//predictionoverall.xml. In that case,use a default sorting
												{
													$configvalues['sort_order_1']='points';
													$configvalues['sort_order_2']='correct_tipps';
													$configvalues['sort_order_3']='correct_diffs';
													$configvalues['sort_order_4']='correct_tend';
													$configvalues['sort_order_5']='count_tipps_p';
												}
												break;
											}

			default:	{
							break;
						}
		}
		return $configvalues;
	}

	function getTimestamp($date,$offset=0)
	{
		if ($date <> '')
		{
			$datum=split("-| |:",$date);
		}
		else
		{
			$datum=preg_split("/-| |:/",HTMLHelper::_('date',date('Y-m-d H:i:s',time()),"Y-m-d H:i:s"));
		}
		if ($offset)
		{
			$serveroffset=explode(':',$offset);
			$timestampoffset=($serveroffset[0] * 3600) + ($serveroffset[1] * 60);
		}
		else
		{
			$timestampoffset=0;
		}
		$result=mktime($datum[3],$datum[4],$datum[5],$datum[1],$datum[2],$datum[0]) + $timestampoffset;
		return $result;
	}

	function getPredictionProject($project_id=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($project_id > 0)
		{
			$query='SELECT * FROM #__joomleague_project WHERE id='.$project_id;
			$db->setQuery($query);
			if (!$result=$db->loadObject()){return false;}
			return $result;
		}
		return false;
	}

	function getMatchTeam($teamID=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($teamID==0){return '#Error1 in _getTeamName#';}

		$query =	"
					SELECT t.name
					FROM #__joomleague_team AS t
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadObject())
		{
			return $object->name;
		}
		return '#Error2 in _getTeamName#';

	}

	function getMatchTeamClubLogo($teamID=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($teamID == 0) { return '#Error1 in _getTeamNameClubLogo#'; }

		$query =	"
					SELECT c.logo_small
					FROM #__joomleague_club AS c
					INNER JOIN #__joomleague_team AS t on t.club_id=c.id
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadObject())
		{
			return $object->logo_small;
		}
		return '#Error2 in _getTeamNameClubLogo#';

	}
	
	function getMatchTeamClubFlag($teamID=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($teamID == 0) { return '#Error1 in _getTeamNameClubFlag#'; }

		$query =	"
					SELECT c.country
					FROM #__joomleague_club AS c
					INNER JOIN #__joomleague_team AS t on t.club_id=c.id
					INNER JOIN #__joomleague_project_team AS pt on pt.id='$teamID'
					WHERE t.id=pt.team_id";
		$db->setQuery($query);
		$db->execute();
		if ($object=$db->loadObject())
		{
			return $object->country;
		}
		return '#Error2 in _getTeamNameClubFlag#';

	}
	
  

	function getProjectSettings($pid=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($pid > 0)
		{
			$query='	SELECT current_round,
							CASE WHEN CHAR_LENGTH(alias) THEN CONCAT_WS(\':\',id,alias) ELSE id END AS slug
						FROM #__joomleague_project
						WHERE id='.$db->Quote($pid);
			$db->setQuery($query,0,1);
			//return $this->_project=$db->loadResult();
			return $db->loadResult();
		}
		return false;
	}

	function getProjectRounds($pid=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($pid > 0)
		{
			$query='SELECT max(id) FROM #__joomleague_round 
					WHERE project_id='.$db->Quote($pid);
			$db->setQuery($query);
			$this->_projectRoundsCount=$db->loadResult();
			return $this->_projectRoundsCount;
		}
		return false;
	}

	function checkPredictionMembership()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query='	SELECT id
					FROM #__joomleague_prediction_member
					WHERE	prediction_id='.$db->Quote($this->predictionGameID).' AND
							user_id='.$db->Quote(Factory::getUser()->id).' AND
							approved=1';
		$db->setQuery($query,0,1);
		if (!$db->loadResult()){return false;}
		return true;
	}

	function checkIsNotApprovedPredictionMember()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query='	SELECT user_id,approved
					FROM #__joomleague_prediction_member
					WHERE	prediction_id='.$db->Quote($this->predictionGameID).' AND
							user_id='.$db->Quote(Factory::getUser()->id);
		$db->setQuery($query,0,1);
		if (!$result=$db->loadObject()){return 2;}
		if ($result->approved){return 0;}
		return 1;
	}
	function getAllowed($pmUID=0)
	{
	    $app = Factory::getApplication();
	    $option = $app->input->getCmd('option');
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		$allowed=false;
		$groupNames = '';
		$user = Factory::getUser();
	
		$authorised = Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
		//echo 'authorised<br /><pre>~' . print_r($authorised,true) . '~</pre><br />';
		$authorisedgroups = $user->getAuthorisedGroups();
		//echo 'authorised groups<br /><pre>~' . print_r($authorisedgroups,true) . '~</pre><br />';
	
		foreach ($user->groups as $groupId => $value)
		{
	/*
			$db->setQuery(
					'SELECT `title`' .
					' FROM `#__usergroups`' .
					' WHERE `id` = '. (int) $groupId
			);*/
		    $query->clear();
		    $query->select('title');
		    $query->from('#__usergroups');
		    $query->where('id = '.(int) $groupId);
		    $db->setQuery($query);
			$groupNames .= $db->loadResult();
			$groupNames .= '<br/>';
		}
		//print $groupNames.'<br>';
	
		$groups = Access::getGroupsByUser($user->id, false);
		//echo 'user groups<br /><pre>~' . print_r($groups,true) . '~</pre><br />';
	
		if ($user->id > 0)
		{
		   // $auth= Access::getAuthorisedViewLevels(Factory::getUser()->get('id'));
			//$aro_group = $acl->getAroGroup($user->id);
	
			if (($groups[0] == 7) || ($groups[0] == 8))
			{
				$allowed=true;
			}
			else
			{
				if (($pmUID > 0) && ($pmUID==$user->id))
				{
					$allowed=true;
				}
				else
				{
					$predictionGame=$this->getPredictionGame();
					$adminAllowed=$predictionGame->admin_tipp;
					if ($adminAllowed)
					{
						$predictionGameAdmins=$this->getPredictionGameAdmins($predictionGame->id);
						foreach($predictionGameAdmins AS $adminUserID)
						{
							if ($adminUserID==$user->id)
							{
								$allowed=true;
								break;
							}
						}
					}
				}
			}
		}
		return $allowed;
	}

	function getSystemAdminsEMailAdresses()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	u.sendEmail=1 AND
								u.block=0"
						ORDER BY u.email';
		$db->setQuery($query);
		return $db->loadcolumn();
	}

	function getPredictionGameAdminsEMailAdresses()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query =	'	SELECT u.email
						FROM #__users AS u
						INNER JOIN #__joomleague_prediction_admin AS pa ON	pa.prediction_id='.(int) $this->predictionGameID.' AND
																			pa.user_id=u.id
						WHERE	u.sendEmail=1 AND
								u.block=0
						ORDER BY u.email';
		$db->setQuery($query);
		return $db->loadcolumn();
	}

	function getPredictionGameAdmins($predictionID)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query='SELECT user_id FROM #__joomleague_prediction_admin WHERE prediction_id='.$predictionID;
		$db->setQuery($query);
		return $db->loadcolumn();
	}

	function getPredictionMemberEMailAdress($predictionMemberID)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query =	'	SELECT user_id
						FROM #__joomleague_prediction_member
						WHERE	id='.$predictionMemberID;
		$db->setQuery($query);
		if (!$user_id=$db->loadResult()){return false;}

		$query =	'	SELECT u.email
						FROM #__users AS u
						WHERE	u.sendEmail=1 AND
								u.block=0 AND
								u.id='.$user_id.'
						ORDER BY u.email';
		$db->setQuery($query);
		return $db->loadcolumn();
	}

	function sendMembershipConfirmation($cid=array())
	{
		if (count($cid))
		{
			$cids=implode(',',$cid);
			// create and send mail about registration in Prediction game
			$systemAdminsMails=$this->getSystemAdminsEMailAdresses();
			$predictionGameAdminsMails=$this->getPredictionGameAdminsEMailAdresses();

			foreach ($cid as $predictionMemberID)
			{
				$predictionGameMemberMail=$this->getPredictionMemberEMailAdress($predictionMemberID);
				if (count($predictionGameMemberMail) > 0)
				{
					//Fetch the mail object
					$mailer = Factory::getMailer();

					//Set a sender
					$config = Factory::getConfig();
					$sender=array($config->getValue('config.mailfrom'),$config->getValue('config.fromname'));
					$mailer->setSender($sender);

					//set Member as recipient
					$lastMailAdress='';
					$recipient=array();
					foreach ($predictionGameMemberMail AS $predictionGameMember_EMail)
					{
						if ($lastMailAdress != $predictionGameMember_EMail)
						{
							$recipient[]=$predictionGameMember_EMail;
							$lastMailAdress=$predictionGameMember_EMail;
						}
					}
					$mailer->addRecipient($recipient);

					//set system admins as BCC recipients
					$lastMailAdress='';
					$recipientAdmins=array();
					foreach ($systemAdminsMails AS $systemAdminMail)
					{
						if ($lastMailAdress != $systemAdminMail)
						{
							$recipientAdmins[]=$systemAdminMail;
							$lastMailAdress=$systemAdminMail;
						}
					}
					$lastMailAdress='';

					//set predictiongame admins as BCC recipients
					foreach ($predictionGameAdminsMails AS $predictionGameAdminMail)
					{
						if ($lastMailAdress != $predictionGameAdminMail)
						{
							$recipientAdmins[]=$predictionGameAdminMail;
							$lastMailAdress=$predictionGameAdminMail;
						}
					}
					$mailer->addBCC($recipientAdmins);
					unset($recipientAdmins);

					//Create the mail
					$mailer->setSubject('Approved Prediction Game Membership');
					$body="Your request for membership on a prediction game on this website was approved by an admin.\nnBe welcome!";

					$mailer->setBody($body);
					echo '<br /><pre>~'.print_r($mailer,true).'~</pre><br />';

					// Optional file attached
					//$mailer->addAttachment(PATH_COMPONENT.DS.'assets'.DS.'document.pdf');

					//Sending the mail
					$send = $mailer->Send();
					if ($send !== true)
					{
						echo 'Error sending email to:<br />'.print_r($recipient,true).'<br />';
						echo 'Error message: '.$send->message;
					}
					else
					{
						echo 'Mail sent';
					}
					echo '<br /><br />';
				}
				else
				{
					// joomla_user is blocked or has set sendEmail to off
					// can't send email
					return false;
				}
			}
		}

		return true;
	}

	function echoLabelTD($labelText,$labelTextHelp,$rowspan=0)
	{
		?><td class='labelEdit'<?php echo ($rowspan > 1 ? ' rowspan="'.$rowspan.'"' : '')?> ><span class='hasTip' title="<?php echo Text::_($labelTextHelp); ?>"><?php echo Text::_($labelText); ?></span></td><?php
	}

	function getPredictionMemberList(&$config,$actUserId=null)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($config['show_full_name']==0){$nameType='username';}else{$nameType='name';}
		$query="	SELECT	pm.id AS value,
							u.".$nameType." AS text

					FROM #__joomleague_prediction_member AS pm
						LEFT JOIN #__users AS u ON	u.id=pm.user_id
					WHERE	prediction_id=".$db->Quote((int)$this->predictionGameID);
		if(isset($actUserId))
		{
			$query .= " AND pm.approved=1 AND
							(pm.show_profile=1 OR pm.user_id=$actUserId)";
		}
		$db->setQuery($query);
		$results=$db->loadObjectList();
		return $results;
	}

	function getMemberPredictionTotalCount($user_id)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query=	"	SELECT	count(*)
						FROM #__joomleague_prediction_result AS pr
						WHERE prediction_id=$this->predictionGameID AND user_id=$user_id";

		$db->setQuery($query);
		$results=$db->loadResult();
		return $results;
	}

	function getMemberPredictionJokerCount($user_id,$project_id=0)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query=	"	SELECT	count(id)
						FROM #__joomleague_prediction_result
						WHERE	prediction_id=$this->predictionGameID AND
								user_id=$user_id AND
								joker=1";
		if ($project_id>0)
		{
			$query .= 	" AND project_id=$project_id";
		}

		$db->setQuery($query);
		$results=$db->loadResult();
		return $results;
	}

	function createResultsObject($home,$away,$tipp,$tippHome,$tippAway,$joker,$homeDecision=0,$awayDecision=0)
	{
		$result=new stdClass();
		$result->team1_result		= $home;
		$result->team2_result		= $away;
		$result->team1_result_decision	= $homeDecision;
		$result->team2_result_decision	= $awayDecision;
		$result->tipp			= $tipp;
		$result->tipp_home		= $tippHome;
		$result->tipp_away		= $tippAway;
		$result->joker			= $joker;

		return $result;
	}

	function getMemberPredictionPointsForSelectedMatch(&$predictionProject,&$result)
	{

		//echo '<br /><pre>~'.print_r($predictionProject,true).'~</pre><br />';

/*
ok[points_correct_result] => 7
ok[points_correct_result_joker] => 6
ok[points_correct_diff] => 5
ok[points_correct_diff_joker] => 4
ok[points_correct_draw] => 4
ok[points_correct_draw_joker] => 3
ok[points_correct_tendence] => 3
ok[points_correct_tendence_joker] => 2
ok[points_tipp] => 1						Points for wrong prediction
ok[points_tipp_joker] => 0					Points for wrong prediction with Joker
 */


		//echo '<br /><pre>~'.print_r($result,true).'~</pre><br />';

/*
[team1_result] => 1					Standard result of the match for hometeam
[team2_result] => 1					Standard result of the match for awayteam
[team1_result_decision] => 			There is NO standard result of the match for hometeam but A DECISION
[team2_result_decision] => 			There is NO standard result of the match for awayteam but A DECISION
[tipp] => 0							Only interesting for toto
[tipp_home] => 1					Only interesting for standard mode
[tipp_away] => 1					Only interesting for standard mode
[joker] => 1
*/



		if ($predictionProject->mode==0)	// Standard prediction Mode
		{
		
			if ((!isset($result->team1_result)) || (!isset($result->team2_result)) || (!isset($result->tipp_home)) || (!isset($result->tipp_away)))
			{
				return 0;
			}
		
			if (!$result->joker)	// No Joker was used for this prediction
			{
				//Prediction Result is the same as the match result / Top Tipp
				if (($result->team1_result==$result->tipp_home)&&($result->team2_result==$result->tipp_away))
				{
					return $predictionProject->points_correct_result;
				}

				//Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped and the matchresult is draw
				/*
				if ($result->team1_result==$result->team2_result)
				{
					if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
					{
						return $predictionProject->points_correct_draw;
					}
				}
				*/
				if (($result->team1_result==$result->team2_result) &&
					($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_draw;
				}

				//Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped
				if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_diff;
				}

				//Prediction Result is not the same as the match result but the tendence of the result is correct
				if	(((($result->team1_result - $result->team2_result)>0)&&(($result->tipp_home - $result->tipp_away)>0)) ||
					 ((($result->team1_result - $result->team2_result)<0)&&(($result->tipp_home - $result->tipp_away)<0)))
				{
					return $predictionProject->points_correct_tendence;
				}

				//Prediction Result is totally wrong but we check if there is at least one point to give ;-)
				return $predictionProject->points_tipp;
			}
			else	// Member took a Joker for this prediction
			{
				//With Joker - Prediction Result is the same as the match result / Top Tipp
				if (($result->team1_result==$result->tipp_home)&&($result->team2_result==$result->tipp_away))
				{
					return $predictionProject->points_correct_result_joker;
				}

				//With Joker - Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped and the matchresult is draw
				if (($result->team1_result==$result->team2_result) &&
					($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_draw_joker;
				}

				//With Joker - Prediction Result is not the same as the match result but the correct difference between home and
				//away result was tipped
				if (($result->team1_result - $result->team2_result)==($result->tipp_home - $result->tipp_away))
				{
					return $predictionProject->points_correct_diff_joker;
				}

				//Prediction Result is not the same as the match result but the tendence of the result is correct
				if	(((($result->team1_result - $result->team2_result)>0)&&(($result->tipp_home - $result->tipp_away)>0)) ||
					 ((($result->team1_result - $result->team2_result)<0)&&(($result->tipp_home - $result->tipp_away)<0)))
				{
					return $predictionProject->points_correct_tendence_joker;
				}

				//With Joker - Prediction Result is totally wrong but we check if there is a point to give
				return $predictionProject->points_tipp_joker;
			}
		}
		else	// Toto Mode - No Joker is used here
		{
			if ((!isset($result->team1_result)) || (!isset($result->team2_result)))
			{
				return 0;
			}		
		
			if (($result->team1_result > $result->team2_result) && ($result->tipp=="1")){return $predictionProject->points_tipp;}
			if (($result->team1_result < $result->team2_result) && ($result->tipp=="2")){return $predictionProject->points_tipp;}
			if (($result->team1_result== $result->team2_result) && ($result->tipp=="0")){return $predictionProject->points_tipp;}
			return 0;
		}

		return 'ERROR';
	}
/*
	function getPredictionMembersResultsList($project_id,$round1ID,$round2ID=0,$user_id=0,$type=0)
	{
	    $app = Factory::getApplication();
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		  if ($round1ID==0){$round1ID=1;}
		      $query
                    ->select('m.id AS matchID')
                    ->select('m.match_date')
                    ->select('m.team1_result AS homeResult')
                    ->select('m.team2_result AS awayResult')
                    ->select('m.team1_result_decision AS homeDecision')
                    ->select('m.team2_result_decision AS awayDecision')
                    ->select('pr.id AS prID')
                    ->select('pr.user_id AS prUserID')
                    ->select('pr.tipp AS prTipp')
                    ->select('pr.tipp_home AS prHomeTipp')
                    ->select('pr.tipp_away AS prAwayTipp')
                    ->select('pr.joker AS prJoker')
                    ->select('pr.points AS prPoints')
                    ->select('pr.top AS prTop')
                    ->select('pr.diff AS prDiff')
                    ->select('pr.tend AS prTend')
                    ->select('pm.id AS pmID')
                    ->from('#__joomleague_match AS m')
                    ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id');
		if ((isset($project_id)) && ($project_id > 0))
		{
			$query->where('r.project_id = '.(int)$project_id);
		}

		$query->where('r.id >= '.(int)$round1ID);

		if ((isset($round2ID)) && ($round2ID > 0))
		{
		    $query->where('r.id <= '.(int)$round2ID);
		}
		$query = $db->getQuery(true);
        $query->leftJoin('#__joomleague_prediction_result AS pr ON pr.match_id = m.id');
		if ((isset($user_id)) && ($user_id > 0))
		{
			$query->where('pr.user_id = '.(int)$user_id);
		}
		$query = $db->getQuery(true);
		$query->innerJoin('#__joomleague_prediction_member AS pm ON pm.user_id = pr.user_id')
		      ->where('pm.prediction_id = $this->predictionGameID')
		      ->where('pr.prediction_id = $this->predictionGameID')
		      ->where('(m.cancel IS NULL OR m.cancel = 0)')
		      ->order('pm.id,m.match_date,m.id ASC');
		try {
		    $db->setQuery($query);
		    $results=$db->loadObjectList();
		    
		} catch (RunTimeException $e) {
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');		    
		}
		return $results;
	}
*/
	function getPredictionMembersResultsList($project_id,$round1ID,$round2ID=0,$user_id=0,$type=0)
	{
	    if ($round1ID==0){$round1ID=1;}
	    $app = Factory::getApplication();
	    $db = Factory::getDBO();
	    $query = $db->getQuery(true);
	    $query
	    ->select('m.id AS matchID')
	    ->select('m.match_date')
	    ->select('m.team1_result AS homeResult')
	    ->select('m.team2_result AS awayResult')
	    ->select('m.team1_result_decision AS homeDecision')
	    ->select('m.team2_result_decision AS awayDecision')
	    ->select('pr.id AS prID')
	    ->select('pr.user_id AS prUserID')
	    ->select('pr.tipp AS prTipp')
	    ->select('pr.tipp_home AS prHomeTipp')
	    ->select('pr.tipp_away AS prAwayTipp')
	    ->select('pr.joker AS prJoker')
	    ->select('pr.points AS prPoints')
	    ->select('pr.top AS prTop')
	    ->select('pr.diff AS prDiff')
	    ->select('pr.tend AS prTend')
	    ->select('pm.id AS pmID')
	    ->from('#__joomleague_match AS m')
	    ->innerJoin('#__joomleague_round AS r ON r.id = m.round_id');
	    
	    if ((isset($project_id)) && ($project_id > 0))
	    {
	        $query .= 	" AND r.project_id=$project_id";
	    }
	    
	    $query .= 	" AND r.id>=$round1ID";
	    
	    if ((isset($round2ID)) && ($round2ID > 0))
	    {
	        $query .= 	" AND r.id<=$round2ID";
	    }
	    
	    $query .= 	" LEFT JOIN #__joomleague_prediction_result AS pr ON pr.match_id=m.id";
	    
	    if ((isset($user_id)) && ($user_id > 0))
	    {
	        $query .= 	" AND pr.user_id=$user_id";
	    }
	    
	    $query .= 	" INNER JOIN #__joomleague_prediction_member AS pm ON pm.user_id=pr.user_id AND pm.prediction_id=$this->predictionGameID
						WHERE pr.prediction_id=$this->predictionGameID
						AND (m.cancel IS NULL OR m.cancel = 0)
						ORDER BY pm.id,m.match_date,m.id ASC";
	    try {
	        $db->setQuery($query);
	        $results=$db->loadObjectList();
	        
	    } catch (RunTimeException $e) {
	        $app->enqueueMessage(Text::_($e->getMessage()), 'error');
	    }
	    return $results;
	}
	function createProjectSelector(&$predictionProjects,$current,$addTotalSelect=null)
	{
		//$output='<select class="inputbox" name="set_pj" onchange="this.form.submit();" >';
		$output='<select class="inputbox" id="p" name="p" onchange="document.forms[\'resultsRoundSelector\'].r.value=0;this.form.submit();" >';
		if (isset($addTotalSelect))
		{
			$output .= '<option value="0"';
			if ($addTotalSelect==0)
			{
				$output .= " selected='selected'";
			}
			$output .= '>'.Text::_('COM_JOOMLEAGUE_PRED_TOTAL_RANKING').'</option>';
		}
		else
		{
			$addTotalSelect=1;
		}
		foreach ($predictionProjects AS $predictionProject)
		{
			$output .= '<option value="'.$predictionProject->project_id.'"';
			if (($predictionProject->project_id==$current) && ($addTotalSelect > 0))
			{
				$output .= " selected='selected'";
			}
			$output .= '>'.$predictionProject->projectName.'</option>';
		}
		$output .= '</select>';
		return $output;
	}

	function getPredictionProjectNames($predictionID,$ordering='ASC')
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		$query="SELECT	ppj.id,
				pj.id AS prediction_id,
				pj.name AS pjName
				  FROM #__joomleague_project AS pj
				  LEFT JOIN #__joomleague_prediction_project AS ppj ON ppj.prediction_id=$predictionID
				  ORDER BY ppj.id ".$ordering;

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	function savePredictionPoints(&$memberResult,&$predictionProject,$returnArray=false)
	{
/*
		//[matchID] => 14501
		//[match_date] => 2010-08-21 15:30:00
		//[homeResult] => 5
		//[awayResult] => 5
		//[homeDecision] =>
		//[awayDecision] =>
		//[prID] => 3647
		//[prTipp] => 0
		//[prHomeTipp] => 5
		//[prAwayTipp] => 5
		//[prJoker] =>
		//[prPoints] => 7
		//[prTop] => 1
		//[prDiff] =>
		//[prTend] =>
		//[pmID] => 46
*/
		$result=true;

		//echo '<br /><pre>~'.print_r($predictionProject,true).'~</pre><br />';
		//echo '<br /><pre>~'.print_r($memberResult,true).'~</pre><br />';
		$result_home	= $memberResult->homeResult;
		$result_away	= $memberResult->awayResult;

		$result_dHome	= $memberResult->homeDecision;
		$result_dAway	= $memberResult->awayDecision;

		$tipp_home	= $memberResult->prHomeTipp;
		$tipp_away	= $memberResult->prAwayTipp;

		$tipp		= $memberResult->prTipp;
		$joker		= $memberResult->prJoker;

		$points		= $memberResult->prPoints;
		$top		= $memberResult->prTop;
		$diff		= $memberResult->prDiff;
		$tend		= $memberResult->prTend;

		if($tipp_home > $tipp_away){$tipp='1';}
		elseif($tipp_home < $tipp_away){$tipp='2';}
		elseif(!is_null($tipp_home)&&!is_null($tipp_away)){$tipp='0';}
		else{$tipp=null;}

		$points		= null;
		$top		= null;
		$diff		= null;
		$tend		= null;

		if (!is_null($tipp_home)&&!is_null($tipp_away))
		{
			if ($predictionProject->mode==1)	// TOTO prediction Mode
			{
				$points=$tipp;
			}
			else	// Standard prediction Mode
			{
				if ($joker)	// Member took a Joker for this prediction
				{
					if (($result_home==$tipp_home)&&($result_away==$tipp_away))
					{
						//Prediction Result is the same as the match result / Top Tipp
						$points=$predictionProject->points_correct_result_joker;
						$top=1;
					}
					elseif(($result_home==$result_away)&&($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped and the matchresult is draw
						$points=$predictionProject->points_correct_draw_joker;
						$diff=1;
					}
					elseif(($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped
						$points=$predictionProject->points_correct_diff_joker;
						$diff=1;
					}
					elseif (((($result_home - $result_away)>0)&&(($tipp_home - $tipp_away)>0)) ||
							 ((($result_home - $result_away)<0)&&(($tipp_home - $tipp_away)<0)))
					{
						//Prediction Result is not the same as the match result but the tendence of the result is correct
						$points=$predictionProject->points_correct_tendence_joker;
						$tend=1;
					}
					else
					{
						//Prediction Result is totally wrong but we check if there is a point to give
						$points=$predictionProject->points_tipp_joker;
					}
				}
				else	// No Joker was used for this prediction
				{
					if (($result_home==$tipp_home)&&($result_away==$tipp_away))
					{
						//Prediction Result is the same as the match result / Top Tipp
						$points=$predictionProject->points_correct_result;
						$top=1;
					}
					elseif(($result_home==$result_away)&&($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped and the matchresult is draw
						$points=$predictionProject->points_correct_draw;
						$diff=1;
					}
					elseif(($result_home - $result_away)==($tipp_home - $tipp_away))
					{
						//Prediction Result is not the same as the match result but the correct difference between home and
						//away result was tipped
						$points=$predictionProject->points_correct_diff;
						$diff=1;
					}
					elseif (((($result_home - $result_away)>0)&&(($tipp_home - $tipp_away)>0)) ||
							 ((($result_home - $result_away)<0)&&(($tipp_home - $tipp_away)<0)))
					{
						//Prediction Result is not the same as the match result but the tendence of the result is correct
						$points=$predictionProject->points_correct_tendence;
						$tend=1;
					}
					else
					{
						//Prediction Result is totally wrong but we check if there is a point to give
						$points=$predictionProject->points_tipp;
					}
				}
			}
		}
		$db = Factory::getDBO();
		$query = $db->getQuery(true);

		$query =	"	UPDATE	#__joomleague_prediction_result

						SET
							tipp_home=" .	((!is_null($tipp_home))	? "'".$tipp_home."'"	: 'NULL').",
							tipp_away=" .	((!is_null($tipp_away))	? "'".$tipp_away."'"	: 'NULL').",
							tipp=" .		((!is_null($tipp))		? "'".$tipp."'"			: 'NULL').",
							joker=" .		((!is_null($joker))		? "'".$joker."'"		: 'NULL').",
							points=" .		((!is_null($points))	? "'".$points."'"		: 'NULL').",
							top=" .			((!is_null($top))		? "'".$top."'"			: 'NULL').",
							diff=" .		((!is_null($diff))		? "'".$diff."'"			: 'NULL').",
							tend=" .		((!is_null($tend))		? "'".$tend."'"			: 'NULL')."
						WHERE id=".$memberResult->prID;
		$db->setQuery($query);
		if (!$db->execute()){$result= false;}

		if ($returnArray)
		{
			$memberResult->tipp		= $tipp;
			$memberResult->points	= $points;
			$memberResult->top		= $top;
			$memberResult->diff		= $diff;
			$memberResult->tend		= $tend;

			return $memberResult;
		}

		return $result;
	}

	function getRoundNames($project_id,$ordering='ASC')
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if (empty($this->_roundNames))
		{
			$query="SELECT	id AS value,
				name AS text
				FROM #__joomleague_round
				WHERE project_id=".(int)$project_id."
				ORDER BY id ".$ordering;

			$db->setQuery($query);
			$this->_roundNames=$db->loadObjectList();
		}
		return $this->_roundNames;
	}

	// general comparison of two tippers results
	// returns negative values for better tipper no 1
	// returns positive values for better tipper no 2
	// returns zero values for both tippers equal
	//
	// ranking rules are described inside the code
	function compare($a,$b)
	{
		$res	= 0;
		$i		= 1;

		while (array_key_exists('sort_order_'.$i,$this->table_config) and $res==0)
		{
			switch ($this->table_config['sort_order_'.$i++])
			{
				// 1. decision: more points
				case 'points':
					$res=-($a['totalPoints'] - $b['totalPoints']);
					break;

				case 'correct_tips':
					$res=-($a['totalTop'] - $b['totalTop']);
					break;

				case 'correct_diffs':
					$res=-($a['totalDiff'] - $b['totalDiff']);
					break;

				case 'correct_tend':
					$res=-($a['totalTend'] - $b['totalTend']);
					break;

				case 'count_tips_p':
					$res= -($a['predictionsCount'] - $b['predictionsCount']);
					break;

				case 'count_tips_m':
					$res=+($a['predictionsCount'] - $b['predictionsCount']);
					break;

				default;
					break;
			}
		}
		return $res;
	}

	function computeMembersRanking($membersResultsArray,$config)
	{
		$this->table_config=$config;
		$dummy=$membersResultsArray;

		uasort($dummy,array($this,'compare'));

		$i=1;
		foreach ($dummy AS $key => $value)
		{
			$dummy[$key]['rank']=$i;
			$i++;
		}
		return $dummy;
	}

	function getPredictionMembersList(&$config)
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		if ($config['show_full_name']==0){$nameType='username';}else{$nameType='name';}
		$query=	"	SELECT	pm.id AS pmID,
								pm.user_id AS user_id,
								pm.picture AS avatar,
								pm.show_profile AS show_profile,
								pm.champ_tipp AS champ_tipp,

								u.".$nameType." AS name

						FROM #__joomleague_prediction_member AS pm
							INNER JOIN #__users AS u ON u.id=pm.user_id
						WHERE pm.prediction_id=$this->predictionGameID
						ORDER BY pm.id ASC";

		$db->setQuery($query);
		$results=$db->loadObjectList();
		return $results;
	}
function checkStartExtension()
{
$app = Factory::getApplication();
echo "<script type=\"text/javascript\">registerhome('".Uri::base()."','Prediction Game Extension','".$app->getCfg('sitename')."','0');</script>";
}

}
?>