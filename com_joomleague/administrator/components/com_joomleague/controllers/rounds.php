<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;


/**
 * Rounds Model
 */
class JoomleagueControllerRounds extends JLGControllerAdmin
{

	public function __construct()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$project_id = $app->getUserState('com_joomleagueproject');

		parent::__construct();
	}


	/**
	 * Function to add 1 new round
	 */
	public function quickAdd()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$model = $this->getModel('round');

		$post = $input->post->getArray();
		$now = Factory::getDate()->format('Y-m-d');
		
		// convert dates back to mysql date format
		if (isset($post['round_date_first']))
		{
			$post['round_date_first']=strtotime($post['round_date_first']) ? strftime('%Y-%m-%d',strtotime($post['round_date_first'])) : $now;
		}
		else
		{
			$post['round_date_first']=$now;
		}
		if (isset($post['round_date_last']))
		{
			$post['round_date_last']=strtotime($post['round_date_last']) ? strftime('%Y-%m-%d',strtotime($post['round_date_last'])) : $now;
		}
		else
		{
			$post['round_date_last']=$now;
		}
		
		
		$max = $model->getMaxRound($app->getUserState($option.'project',0));
		$max++;

		if (!isset($post['roundcode']))
		{
			$post['roundcode'] = $max;
		}
		if (!isset($post['name']))
		{
			$post['name'] = Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_NAME',$max);
		}
	
		$table = Table::getInstance('Round','Table');
		if($table->save($post))
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_ADDED'),'notice');
		}
		else
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_ADD') . $model->getError(),'error');
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		$link = 'index.php?option=com_joomleague&view=rounds';
		$this->setRedirect($link);
	}


	public function save()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$post = $input->post->getArray();
		$model = $this->getModel('round');

		// convert dates back to mysql date format
		if(isset($post['round_date_first']))
		{
			$post['round_date_first'] = strtotime($post['round_date_first']) ? strftime('%Y-%m-%d',strtotime($post['round_date_first'])) : null;
		}
		else
		{
			$post['round_date_first'] = null;
		}
		if(isset($post['round_date_last']))
		{
			$post['round_date_last'] = strtotime($post['round_date_last']) ? strftime('%Y-%m-%d',strtotime($post['round_date_last'])) : null;
		}
		else
		{
			$post['round_date_last'] = null;
		}

		$max = $model->getMaxRound($app->getUserState($option . 'project',0));
		$max ++;

		if(! isset($post['roundcode']) || empty($post['roundcode']))
		{
			$post['roundcode'] = $max;
		}
		if(! isset($post['name']) || empty($post['name']))
		{
			$post['name'] = Text::sprintf('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_NAME',$max);
		}
		if($model->store($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_SAVE') . $model->getError(),'error');
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();

		$task = $this->getTask();

		if($task == 'save')
		{
			$link = 'index.php?option=com_joomleague&view=rounds';
		}
		else
		{
			$link = 'index.php?option=com_joomleague&task=round.edit&id=' . $post['id'];
		}
		$this->setRedirect($link,$msg);
	}


	/**
	 * save the checked rows inside the rounds list
	 */
	public function saveshort()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$post = $input->post->getArray();
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$model = $this->getModel('round');
		for($x = 0;$x < count($cid);$x ++)
		{
			$post['round_date_first'.$cid[$x]] = JoomleagueHelper::convertDate($post['round_date_first' . $cid[$x]],0);
			$post['round_date_last'.$cid[$x]] = JoomleagueHelper::convertDate($post['round_date_last' . $cid[$x]],0);
			if(isset($post['roundcode'.$cid[$x]]))
			{
				if($post['roundcode'.$cid[$x]] == '0')
				{
					$max = $model->getMaxRound($app->getUserState($option.'project',0));
					$post['roundcode'.$cid[$x]] = $max + 1;
				}
			}
		}
		if($model->storeshort($cid,$post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ROUND_SAVED'),'notice');
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_ERROR_SAVE') . $model->getError(),'error');
		}
		$link = 'index.php?option=com_joomleague&view=rounds';
		$this->setRedirect($link,$msg);
	}


	/**
	 * save the checked rows inside the rounds list
	 */
	public function saveshortAjax()
	{
		header('Content-Type: application/json');

		$app 	= Factory::getApplication();
		$input = $app->input;
		$name   = $input->getString('name');
		$value  = $input->getString('value');
		$token   = $input->getString('token',false);
		$tokenValue = $input->getInt('tokenvalue',false);
		$pk		= $input->get('pk');
		
		if (empty($token) || empty ($tokenValue) || $tokenValue != '1') {
			jexit();
		}
		
		$token2 = Session::getFormToken();
		if (!($token == $token2)) {
			jexit();
		}

		if ($name) {
			$name = str_replace('_'.$pk, "", $name);
			$model = $this->getModel('rounds');
			$result = $model->storeshortAjax($name,$value,$pk);
			echo '{"success":true}';
		} else {
			echo '{"success":false}';
		}
		$app->close();
	}



	public function remove()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'),'error');
		} else {
			$mdlMatches = $this->getModel('matches');
			$mdlMatch = $this->getModel('match');
			$model = $this->getModel('round');
			if(! $model->delete($cid,$mdlMatches,$mdlMatch))
			{
				echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			}
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_DELETED'),'notice');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=rounds');
	}


	public function deletematches()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$cid = $input->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		if(!is_array($cid) || count($cid) < 1)
		{
		    $app->enqueueMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_SELECT_TO_DELETE_MATCHES'),'error');
		} else {
			$mdlMatches = $this->getModel('matches');
			$mdlMatch = $this->getModel('match');
			$model = $this->getModel('round');
			if(! $model->deleteMatches($cid,$mdlMatches,$mdlMatch,true))
			{
				echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			}
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_CTRL_MATCHES_DELETED'),'notice');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=rounds');
	}


	public function cancel()
	{
		$this->setRedirect('index.php?option=com_joomleague&view=rounds');
	}


	/**
	 * display the populate form
	 */
	public function populate()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$division_id = $input->getInt('division_id',0);
		
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&layout=populate&division_id='.$division_id);
	}


	/**
	 * Perform the mass addition of rounds
	 */
	public function startmassadd()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$addRoundCount = $input->getInt('add_round_count');
		$interval = $input->getInt('interval');
		$projectId = $input->getInt('project_id');
		$scheduling = $input->getString('mass_add_method');
		$startDate = $input->getString('start_date');
		$model = $this->getModel('rounds');
		$result = $model->massAddRounds($projectId,$scheduling,$addRoundCount,$startDate,$interval);
		$link = 'index.php?option=com_joomleague&view=rounds';
		$this->setMessage($result['msg'],$result['msgType']);
		$this->setRedirect($link);
	}


	/**
	 * does the populate operation
	 */
	public function startpopulate()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$model = $this->getModel('rounds');
		$interval = $input->getInt('interval');
		$project_id = $input->getInt('project_id');
		$scheduling = $input->getString('scheduling','');
		$time = $input->get('time');
		$start = $input->get('start');
		$roundname = $input->getString('roundname');
		$matchnumber = $input->get('matchnumber');

		$teamsorder = $input->get('teamsorder',array(),'array');
		ArrayHelper::toInteger($teamsorder);

		$bSuccess = $model->populate($project_id,$scheduling,$time,$interval,$start,$roundname,$teamsorder,$matchnumber);
		if($bSuccess)
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_SUCCESSFULL'),'notice');
		}
		else
		{
			$this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_ROUNDS_POPULATE_ERROR').': '.$model->getError(),'error');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=rounds');
	}



	public function massadd()
	{
		$this->setRedirect('index.php?option=com_joomleague&view=rounds&massadd=true');
	}
}
