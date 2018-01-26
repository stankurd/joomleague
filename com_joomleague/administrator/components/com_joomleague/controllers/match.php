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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;


/**
 * Match Model
 */
class JoomleagueControllerMatch extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('layout','form');
		
		parent::__construct($config);

		// Register Extra tasks
		$this->registerTask('saveroster2','saveroster');
		$this->registerTask('savereferees2','savereferees');
	}
	
	
	/**
	 * Function that allows child controller access to model data after the data
	 * has been saved.

	 *
	 * @param BaseDatabaseModel $model	The data model object.
	 * @param array $validData		The validated data.
	 *
	 * @return void
	 */
	protected function postSaveHook(BaseDatabaseModel $model,$validData = array())
	{
		return;
	}
	
	public function editEvents()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$match_id = $input->getInt('match_id');
		
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=match&layout=editevents&match_id='.$match_id,false));
	}

	public function editEventsbb()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$match_id = $input->getInt('match_id');
		
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=match&layout=editeventsbb&match_id='.$match_id,false));
	}

	public function editstats()
	{
		$app 		= Factory::getApplication();
		$input		= $app->input;
		$option 	= $input->get('option');
		$document 	= Factory::getDocument();
		$project_id	= $app->getUserState($option.'project',0);
		$post		= $input->post->getArray();
		$cid 		= $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=match&layout=editstats',false));
	}

	
	/**
	 * editReferees
	 */
	public function editReferees()
	{
		$app 		= Factory::getApplication();
		$input		= $app->input;
		$option 	= $input->get('option');
		$document 	= Factory::getDocument();
		$project_id	= $app->getUserState($option.'project',0);
		$post		= $input->post->getArray();
		$cid		= $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);

		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=match&layout=editreferees',false));
	}

	
	/**
	 * editLineup
	 */
	public function editlineup()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$document 	= Factory::getDocument();
		$match_id = $input->getInt('match_id');
		$team_id = $input->getInt('team_id');
		
		$this->setRedirect(Route::_('index.php?option=com_joomleague&view=match&layout=editlineup&match_id='.$match_id.'&team_id='.$team_id,false));
	}

	
	/**
	 * saveroster
	 */
	public function saveroster()
	{
		// Check for request forgeries
		Session::checkToken() or die;
		
		$app 		= Factory::getApplication();
		$input      = $app->input;
		$option 	= $input->get('option');
		$match_id 	= $input->getInt('match_id');
		$document 	= Factory::getDocument();
		$model		= $this->getModel('match');
		$task 		= $this->getTask();
		
		$positions	= $model->getProjectPositions();
		$staffpositions = $model->getProjectStaffPositions();
		$post		= $input->post->getArray();
		$cid		= $input->get('cid',array(0),'array');
		ArrayHelper::toInteger($cid);
		$post['mid']			= $cid[0];
		$post['positions'] 		= $positions;
		$post['staffpositions'] = $staffpositions;
		$team_id=$post['team_id'];

		$model->updateRoster($post);
		$model->updateStaff($post);

		$model=$this->getModel('match');
		$model->checkout();
		
		if ($task == 'saveroster') {
			$link='index.php?option=com_joomleague&view=match&layout=editlineup&match_id='.$cid[0].'&team_id='.$team_id;
		} else {
			$link='index.php?option=com_joomleague&view=match&view=matches';
		}
		
		$this->setRedirect($link);
	}

	
	/**
	 * Save referees
	 */
	public function saveReferees()
	{
		// Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$document = Factory::getDocument();
		$model=$this->getModel('match');		
		$task = $this->getTask();		
		$positions=$model->getProjectRefereePositions();
		$post		= $input->post->getArray();
		$cid		= $input->post->get('cid',array(0),'array');
		ArrayHelper::toInteger($cid);
		$post['mid']=$cid[0];
		$post['positions']=$positions;
		$team_id=$post['team_id'];

		if ($model->updateReferees($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_MR'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVE_MR').'<br />'.$model->getError(),'error');
		}

		// Checkout the match
		$model=$this->getModel('match');
		$model->checkout();
		
		if ($task == 'saveReferees') {
			$link='index.php?option=com_joomleague&view=match&layout=editreferees&match_id='.$cid[0].'&team_id='.$team_id;
		} else {
			$link='index.php?option=com_joomleague&view=match&view=matches';
		}
		
		$this->setRedirect($link,$msg);
	}

	
	public function copyfrom()
	{
		$app 	= Factory::getApplication();
		$input	= $app->input;
		$option = $input->get('option');
		$msg	= '';
		$post 	= $input->post->getArray();
		$model	= $this->getModel('match');
		$add_match_count = $input->getInt('add_match_count');
		$round_id = $input->getInt('rid');
		$project_id	= $app->getUserState($option.'project',0);
		
		$post['project_id']	= $app->getUserState($option.'project',0);
		$post['round_id']	= $app->getUserState($option.'round_id',0);
		$post['id'] = 0;
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);
		$project_tz = new DateTimeZone($project->timezone);
		
		// Add matches (type=1)
		if ($post['addtype']==1)
		{
			if ($add_match_count > 0) // Only MassAdd a number of new and empty matches
			{
				if (!empty($post['autoPublish'])) // 1=YES Publish new matches
				{
					$post['published']=1;
				}

				$matchNumber = $input->getInt('firstMatchNumber',1);
				$roundFound  = false;
				
				if ($projectRounds=$model->getProjectRoundCodes($post['project_id']))
				{
					
					// convert date and time to utc
					$uiDate = $post['match_date'];
					$uiTime = $post['startTime'];
					$post['match_date'] = $this->convertUiDateTimeToMatchDate($uiDate, $uiTime, $project_tz);
			
					foreach ($projectRounds AS $projectRound)
					{
						if ($projectRound->id==$post['round_id']){
							$roundFound=true;
						}
						if ($roundFound)
						{
							$post['round_id']=$projectRound->id;
							$post['roundcode']=$projectRound->roundcode;
							for ($x=1; $x <= $add_match_count; $x++)
							{
								if (!empty($post['firstMatchNumber'])) // 1=YES Add continuous match Number to new matches
								{
									$post['match_number']=$matchNumber;
								}

								if ($model->save($post))
								{
								    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ADD_MATCH'),'notice');
									$matchNumber++;
								}
								else
								{
								    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError(),'error');
									break;
								}
							}
							if (empty($post['addToRound'])) // 1=YES Add matches to all rounds
							{
								break;
							}
						}
					}
				}
			}
		}
		
		
		// Copy matches (type=2)
		if ($post['addtype']==2)// Copy or mirror new matches from a selected existing round
		{
			if ($matches=$model->getRoundMatches($round_id))
			{
				// convert date and time to utc
				$uiDate = $post['date'];
				$uiTime = $post['startTime'];
				$post['match_date'] = $this->convertUiDateTimeToMatchDate($uiDate, $uiTime, $project_tz);

				foreach($matches as $match)
				{
					//aufpassen,was uebernommen werden soll und welche daten durch die aus der post ueberschrieben werden muessen
					//manche daten muessen auf null gesetzt werden

 					$dmatch['match_date'] = $post['match_date'];
					
					if ($post['mirror'] == '1')
					{
						$dmatch['projectteam1_id']	= $match->projectteam2_id;
						$dmatch['projectteam2_id']	= $match->projectteam1_id;
					}
					else
					{
						$dmatch['projectteam1_id']	= $match->projectteam1_id;
						$dmatch['projectteam2_id']	= $match->projectteam2_id;
					}
					$dmatch['project_id']	= $post['project_id'];
					$dmatch['round_id']		= $post['round_id'];
					if ($post['start_match_number'] != '')
					{
						$dmatch['match_number']=$post['start_match_number'];
						$post['start_match_number']++;
					}
					
					$dmatch['id'] = 0;

					if ($model->save($dmatch))
					{
					    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_COPY_MATCH'),'notice');
					}
					else
					{
					    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH').$model->getError(),'error');
					}
				}
			}
			else
			{
			    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_COPY_MATCH2').$model->getError(),'error');
			}
		}
		
		$link='index.php?option=com_joomleague&view=matches';
		$this->setRedirect($link,$msg);
	}

	
	//	add a match to round
	public function addmatch()
	{
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$post 	= $input->post->getArray();
		
		$project_id	= $app->getUserState($option.'project',0);
		$post['project_id']=$app->getUserState($option.'project',0);
		$post['round_id']=$app->getUserState($option.'round_id',0);
		//get the home team standard playground
		if(!empty($post['projectteam1_id']))  {
			$tblProjectHomeTeam = Table::getInstance('ProjectTeam', 'Table');
			$tblProjectHomeTeam->load($post['projectteam1_id']);
			$standard_playground_id = (!empty($tblProjectHomeTeam->standard_playground) && $tblProjectHomeTeam->standard_playground > 0) ? $tblProjectHomeTeam->standard_playground : null;
			$post['playground_id'] = $standard_playground_id;
		}

		// convert date and time to utc
		$model=$this->getModel('match');
		list($uiDate, $uiTime) = explode(" ", $post['match_date']);
		
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project 	= $mdlProject->getItem($project_id);
		
		$project_tz = new DateTimeZone($project->timezone);
		if (is_null($project_tz) || empty($project_tz)) {
			$project_tz = 'UTC';
		}
		
		$post['match_date'] = $this->convertUiDateTimeToMatchDate($uiDate, $uiTime, $project_tz);
		
		if ($model->save($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ADD_MATCH'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_ADD_MATCH').$model->getError(),'error');
		}
		$link='index.php?option=com_joomleague&view=matches';
		$this->setRedirect($link,$msg);
	}

	
	/**
	 * Function to save a new event
	 * Layout: editevents
	 */
	public function saveevent()
	{
		// Check for request forgeries
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		
		$data = array();
		$data['teamplayer_id']	= $input->getInt('teamplayer_id');
		$data['projectteam_id']	= $input->getInt('projectteam_id');
		$data['event_type_id']	= $input->getInt('event_type_id');
		$data['event_time']		= $input->getVar('event_time', '');
		$data['match_id']		= $input->getInt('match_id');
		$data['event_sum']		= $input->getString('event_sum', '');
		$data['notice']			= $input->getString('notice', '');
		$data['notes']			= $input->getString('notes', '');
		
		$model 		= $this->getModel('match');
		$project_id = $app->getUserState($option.'project',0);
		$result		= $model->saveevent($data, $project_id);
		$return 	= new stdClass();
		
		if (!$result) {
			$return->success = false;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_EVENT').': '.$model->getError();
		} else {
			$rowid = $result;
			$return->success = true;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_EVENT');
			$return->id = $rowid;
		}
		echo json_encode($return);
		$app->close();
	}
	
	
	/**
	 * Function to save a new comment
	 * Layout: editevents
	 */
	public function savecomment()
	{
		// Check for request forgeries
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
	
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
	
		$data = array();
		
		$data['teamplayer_id']	= $input->getInt('teamplayer_id');
		$data['projectteam_id']	= $input->getInt('projectteam_id');
		$data['event_type_id']	= $input->getInt('event_type_id');
		$data['event_time']		= $input->getString('event_time', '');
		$data['match_id']		= $input->getInt('match_id');
		$data['event_sum']		= $input->getString('ctype', '');
		$data['notice']			= $input->getString('notice', '');
		$data['notes']			= $input->getString('notes', '');
	
		$model 		= $this->getModel('match');
		$project_id = $app->getUserState($option.'project',0);
		$result		= $model->savecomment($data, $project_id);
		$return 	= new stdClass();
		
		if (!$result) {
			$return->success = false;
			if ($model->getError()) {
				$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_COMMENT').': '.$model->getError();
			} else {
				$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_COMMENT');
			}
		} else {
			$rowid = $result;
			$return->success = true;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_COMMENT');
			$return->id = $rowid;			
		}
		
		echo json_encode($return);		
		$app->close();
	}
	

	public function savesubst()
	{
		// Check for request forgeries
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$data = array();
		$data['in'] 					= $input->getInt('in');
		$data['out'] 					= $input->getInt('out');
		$data['matchid'] 				= $input->getInt('matchid');
		$data['in_out_time'] 			= $input->getString('in_out_time');
		$data['project_position_id'] 	= $input->getInt('project_position_id');

		$model=$this->getModel('match');
		$newId = $model->savesubstitution($data);
		$result = new stdClass();
		if (!$newId){
			$result->success = false;
			$result->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_SAVED_SUBST').': '.$model->getError();
			$result->id = 0;
		} else {
			$result->success = true;
			$result->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_SAVED_SUBST');
			$result->id = $newId;
		}
		echo json_encode($result);
		$app->close();
	}

	/**
	 * removeSubst
	 */
	public function removeSubst()
	{
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app = Factory::getApplication();
		$input = $app->input;
		
		$substid = $input->getInt('substid',0);
		$model=$this->getModel('match');
		$result = new stdClass();
		if (!$model->deleteSubstitution($substid))
		{
			$result->success = false;
			$result->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_REMOVE_SUBST').': '.$model->getError();
		}
		else
		{
			$result->success = true;
			$result->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_REMOVE_SUBST');
		}
		echo json_encode($result);
		$app->close();
	}

	// save the checked rows inside matcheventsbb list
	public function saveeventbb()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->get('option');
		$post 	= $input->post->getArray();
		$cid 	= $input->get('cid',array(),'array');
		$match_id = $input->get('match_id');
		
		$model  = $this->getModel('match');
		$project_id = $app->getUserState($option.'project',0);
		if ($model->saveeventbb($post,$project_id,$match_id))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_UPDATE_EVENTS'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_UPDATE_EVENTS').$model->getError(),'error');
		}
		
		$link = 'index.php?option=com_joomleague&view=match&layout=editeventsbb&match_id='.$match_id;
		
		$this->setRedirect($link, $msg);
	}


	/**
	 * save the match stats
	 */
	public function savestats()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app 	= Factory::getApplication();
		$input = $app->input;
		$post 	= $input->post->getArray();
		
		$match_id = $input->getInt('match_id');
		$model	= $this->getModel('match');
		if ($model->savestats($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_UPDATE_STATS'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_UPDATE_STATS').$model->getError(),'error');
		}
		
		$link = 'index.php?option=com_joomleague&view=match&layout=editstats&match_id='.$match_id;
		$this->setRedirect($link, $msg);
	}

	
	/**
	 * Function to remove selected comment
	 * Layout: editevents
	 */
	public function removeComment()
	{
		// Check for request forgeries
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
				
		$app 	= Factory::getApplication();
		$input = $app->input;
		
		$comment_id = $input->getInt('comment_id');
		$model	  = $this->getModel('match');
		$result	  = $model->deletecomment($comment_id);
		$return = new stdClass();
		
		if (!$result) {
			$return->success = false;
			$return->id = $comment_id;
			if ($model->getError()) {
				$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_DELETE_COMMENT').': '.$model->getError();
			} else {
				$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_DELETE_COMMENT');
			}
		} else {	
			$return->success = true;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_DELETE_COMMENT');
			$return->id = $comment_id;
		}
		echo json_encode($return);
		$app->close();
	}
	
	
	
	/**
	 * Function to remove selected event
	 * Layout: editevents
	 */
	public function removeEvent()
	{
		// Check for request forgeries
		Session::checkToken('GET') or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));
		
		$app 	= Factory::getApplication();
		$input = $app->input;

		$event_id	= $input->getInt('event_id');
		$model		= $this->getModel('match');
		$result	  	= $model->deleteevent($event_id);
		$return 	= new stdClass();
		
		if (!$result)
		{
			$return->success = false;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_ERROR_DELETE_EVENTS').': '.$model->getError();
			$return->id 	 = $event_id;
		}
		else
		{
			$return->success = true;
			$return->message = Text::_('COM_JOOMLEAGUE_ADMIN_MATCH_CTRL_DELETE_EVENTS');
			$return->id 	 = $event_id;
		}
		echo json_encode($return);
		$app->close();
	}
	

	private function convertUiDateTimeToMatchDate($uiDate, $uiTime, $timezone)
	{
		$format = Text::_('COM_JOOMLEAGUE_ADMIN_MATCHES_DATE_FORMAT');

		if (((!strpos($uiDate,'-')!==false) && (!strpos($uiDate,'.')!==false)) && (strlen($uiDate) <= 8 ))
		{
			// to support short date inputs
			if (strlen($uiDate) == 8 )
			{
				if ($format == 'Y-m-d') 
				{
					// for example 20111231 is used for 31 december 2011
					$dateStr = substr($uiDate,0,4) . '-' . substr($uiDate,4,2) . '-' . substr($uiDate,6,2);
				} 
				elseif ($format == 'd-m-Y')
				{
					// for example 31122011 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr($uiDate,4,4);
				}
				elseif ($format == 'd.m.Y')
				{
					// for example 31122011 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '.' . substr($uiDate,2,2) . '.' . substr($uiDate,4,4);
				}
			}
			
			elseif (strlen($uiDate) == 6 )
			{
				if ($format == 'Y-m-d') 
				{
					// for example 111231 is used for 31 december 2011
					$dateStr = substr(date('Y'),0,2) . substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr($uiDate,4,2);
				} 
				elseif ($format == 'd-m-Y')
				{
					// for example 311211 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '-' . substr($uiDate,2,2) . '-' . substr(date('Y'),0,2) . substr($uiDate,4,2);
				}
				elseif ($format == 'd.m.Y')
				{
					// for example 311211 is used for 31 december 2011
					$dateStr = substr($uiDate,0,2) . '.' . substr($uiDate,2,2) . '.' . substr(date('Y'),0,2) . substr($uiDate,4,2);
				}
			}
		}
		else
		{
			$dateStr = $uiDate;
		}
		
		if (!empty($uiTime))
		{
			$format  .= ' H:i';

			if(strpos($uiTime,":")!==false)
			{
				$dateStr .= ' '.$uiTime;
			}
			// to support short time inputs
			// for example 2158 is used instead of 21:58
			elseif (strlen($uiTime) == 4 )
			{
				$dateStr .= ' '.substr($uiTime, 0, -2) . ':' . substr($uiTime, -2);  
			}
			// for example 21 is used instead of 2100
			elseif (strlen($uiTime) == 2 )
			{
				$dateStr .= ' '.$uiTime. ':00';  
			}
		} else {
			// $dateStr .= ' 00:00:00';
		}

		// Todo: fix
		$timestamp = DateTime::createFromFormat($format, $dateStr, $timezone);	
		if(is_object($timestamp))  {
			$timestamp->setTimezone(new DateTimeZone('UTC'));
			return $timestamp->format('Y-m-d H:i:s');
		} else {
			return '0000-00-00 00:00:00';
		}
	}
	
}
