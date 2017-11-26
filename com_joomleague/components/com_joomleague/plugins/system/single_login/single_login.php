<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

use Joomla\Application\AbstractApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;

defined('_JEXEC') or die;

class plgSystemSingle_Login extends CMSPlugin
{

    public function onAfterInitialise()
    {
        $app = Factory::getApplication();

        if ($app->isClient('administrator') && in_array($this->params->get('login', 'both'), array('both', 'backend'))) {
            $this->loginAdmin();
        }

        if ($app-> isClient('site') && in_array($this->params->get('login', 'both'), array('both', 'frontend'))) {
            $this->loginSite();
        }
    }

    private function loginAdmin()
    {
        $app = Factory::getApplication();
        $db = Factory::getDbo();

        // already logedin
        if (Factory::getUser()->id) {
            return;
        }

        $query = $db->getQuery(true)
            ->select('userid')
            ->from('#__session')
            ->where('session_id = ' . $db->quote($app->input->cookie->get(md5(AbstractApplication::getHash('site')))))
            ->where('client_id = 0')
            ->where('guest = 0');

        $db->setQuery($query);

        $userid = $db->loadResult();

        // no frontend session found;
        if (!$userid) {
            return;
        }

        $user = Factory::getUser($userid);

        // user load failed
        if ($user instanceof Exception || $user->get('block') == 1) {
            return;
        }

        // user has no admin permissions
        if (!$user->authorise('core.admin')) {
            return;
        }

        $session = $app->getSession();
        $session->set('user', $user);

        $app->checkSession();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__session'))
            ->set($db->quoteName('guest') . ' = ' . $db->quote($user->get('guest')))
            ->set($db->quoteName('username') . ' = ' . $db->quote($user->get('username')))
            ->set($db->quoteName('userid') . ' = ' . (int)$user->get('id'))
            ->set($db->quoteName('client_id') . ' = ' . 1)
            ->where($db->quoteName('session_id') . ' = ' . $db->quote($session->getId()));
        $db->setQuery($query);
        $db->execute();

        $app->redirect('index.php');
    }

    private function loginSite()
    {
        $app = Factory::getApplication();
        $db = Factory::getDbo();

        // already logedin
        if (Factory::getUser()->id) {
            return;
        }

        $query = $db->getQuery(true)
            ->select('userid')
            ->from('#__session')
            ->where('session_id = ' . $db->quote($app->input->cookie->get(md5(AbstractApplication::getHash('administrator')))))
            ->where('client_id = 1')
            ->where('guest = 0');

        $db->setQuery($query);

        $userid = $db->loadResult();

        // no backend session found;
        if (!$userid) {
            return;
        }

        $user = Factory::getUser($userid);

        // user load failed
        if ($user instanceof Exception || $user->get('block') == 1) {
            return;
        }

        $session = $app->getSession();
        $session->set('user', $user);

        $app->checkSession();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__session'))
            ->set($db->quoteName('guest') . ' = ' . $db->quote($user->get('guest')))
            ->set($db->quoteName('username') . ' = ' . $db->quote($user->get('username')))
            ->set($db->quoteName('userid') . ' = ' . (int)$user->get('id'))
            ->where($db->quoteName('session_id') . ' = ' . $db->quote($session->getId()));
        $db->setQuery($query);
        $db->execute();

        $app->redirect('index.php');
    }
}