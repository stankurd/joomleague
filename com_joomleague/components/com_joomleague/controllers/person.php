<?php
/**
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

class JoomleagueControllerPerson extends JoomleagueController
{

	public function display($cachable = false, $urlparams = array())
	{
	    $app = Factory::getApplication();
		$viewName = $app->input->get('view', 'person');
		$view = $this->getView($viewName);

		$this->addModelToView('project', $view);
		$this->addModelToView('person', $view);

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	public function save()
	{
		// Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$app = Factory::getApplication();
		$pid = $app->input->getInt('pid', 0);
		$tpid = $app->input->getInt('tpid', 0); //teamplayer
		
		if ($pid > 0)
		{
			$post = $app->input->post->getArray();
			$post['id'] = $pid;
			$post['birthday'] = JoomleagueHelper::convertDate($post['birthday'], 0);
			$post['deathday'] = JoomleagueHelper::convertDate($post['deathday'], 0);
			
			$model = $this->getModel('person');
			if ($model->store($post, 'Person'))
			{
				$params = ComponentHelper::getParams('com_joomleague');
				if ($params->get('cfg_edit_person_info_update_notify') == '1')
				{
					$person = $model->getPerson($pid);
					$this->sendMailToAdmins($person);
				}
				
				// save player information
				if (JoomleagueControllerPerson::_saveTeamplayer($tpid, $post))
				{
					$msg = Text::_('COM_JOOMLEAGUE_EDIT_PERSON_SAVED');
				}
				else
				{
					$msg = Text::_('COM_JOOMLEAGUE_EDIT_PERSON_SAVE_ERROR') . $model->getError();
				}
			}
			else
			{
				$msg = Text::_('COM_JOOMLEAGUE_EDIT_PERSON_SAVE_ERROR') . $model->getError();
			}
		}
		else
		{
			$msg = Text::_('COM_JOOMLEAGUE_EDIT_PERSON_MISSING_PARAMETER');
		}
		$this->setRedirect($this->_getShowPlayerLink(), $msg);
	}

	private function sendMailToAdmins($person)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('email')
			->from('#__users')
			->join('INNER', $db->quoteName('#__user_usergroup_map', 'ugm') .
				' ON (' . $db->quoteName('ugm.user_id') . ' = ' . $db->quoteName('id') . ')')
			->join('INNER', $db->quoteName('#__usergroups', 'g') .
				' ON (' . $db->quoteName('g.id') . ' = ' . $db->quoteName('ugm.group_id'))
			->where($db->quoteName('g.title') . ' IN (\'Super Users\', \'Administrator\'');
		$db->setQuery($query);
		$to = $db->loadColumn();
		if (!empty($to))
		{
			$user = Factory::getUser();

			$nickname = !empty($person->nickname) ? "'" . $person->nickname . "'" : '';
			$subject = addslashes(sprintf(Text::_('COM_JOOMLEAGUE_EDIT_PERSON_SUBJECT'),
				$person->firstname, $nickname, $person->lastname));
			$message = addslashes(sprintf(Text::_('COM_JOOMLEAGUE_EDIT_PERSON_MESSAGE'),
				$user->name, $person->firstname, $nickname, $person->lastname));
			$message .= $this->_getShowPlayerLink();

			Mail::sendMail('', '', $to, $subject, $message);
		}
	}

	private function _saveTeamplayer($tpid, $post)
	{
	    $app = Factory::getApplication();
		if ($tpid > 0)
		{
			$post['id'] = $tpid;
			$model = $this->getModel('player');
			// Allow HTML in the notes
			$post['notes'] = $app->input->post->get('notes', 'none', JREQUEST_ALLOWHTML);
			
			return $model->store($post, 'TeamPlayer');
		}
		return false;
	}

	public function cancel()
	{
		$this->setRedirect($this->_getShowPlayerLink());
	}
	
	
	private function _getShowPlayerLink()
	{
	    $app = Factory::getApplication();
		$p = $app->input->getInt('p', 0);
		$tid = $app->input->getInt('tid', 0);
		$pid = $app->input->getInt('pid', 0);
		$link = JoomleagueHelperRoute::getPlayerRoute($p, $tid, $pid);
		return $link;
	}
}
