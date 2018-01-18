<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
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
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

class JoomleagueControllerClubInfo extends JoomleagueController
{
	public function display($cachable = false, $urlparams = array())
	{
		$viewName = $this->input->get('view', 'clubinfo');
		$view = $this->getView($viewName);

		$this->addModelToView('joomleague', $view);
		$this->addModelToView('clubinfo', $view);

		$this->showprojectheading();
		$view->display();
		$this->showbackbutton();
		$this->showfooter();
	}

	/**
	 * TODO: For now editing/saving of data is not taken into account
	 * When we are going to support editing from the frontend, we should look as well who is responsible for what.
	 * In current "save" implementation of the controller, the binding and interfacing with the table is handled
	 * by the controller, where this is typically a job for the model.
	 */
	public function save()
	{
		// Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');

		$cid = $this->input->getInt('cid', 0);
		if ($cid > 0) {
			$post = $this->input->post->getArray();
			$club = Table::getInstance('Club', 'Table');
			$club->load($cid);
			$club->bind($post);
			$params = ComponentHelper::getParams('com_joomleague');

			if ($club->store() && $params->get('cfg_edit_club_info_update_notify') == '1') {
				$this->sendMailToAdmins($club);
			}
		}
		$this->setRedirect($this->_getShowClubInfoLink());
	}

	private function sendMailToAdmins($club)
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
			$subject = addslashes(sprintf(Text::_('COM_JOOMLEAGUE_ADMIN_EDIT_CLUB_INFO_SUBJECT'), $club->name));
			$message = addslashes(sprintf(Text::_('COM_JOOMLEAGUE_ADMIN_EDIT_CLUB_INFO_MESSAGE'), $user->name, $club->name));
			$message .= $this->_getShowClubInfoLink();

			Mail::sendMail('', '', $to, $subject, $message);
		}
	}

	public function cancel()
	{
		$this->setRedirect($this->_getShowClubInfoLink());
	}

	private function _getShowClubInfoLink()
	{
		$p = $this->input->getInt('p', 0);
		$cid = $this->input->getInt('cid', 0);
		$link = JoomleagueHelperRoute::getClubInfoRoute($p, $cid);
		return $link;
	}
}
