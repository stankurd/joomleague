<?php
/**
 * @copyright	Copyright (C) 2006-2012 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\Response;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Client\ClientHelper;

jimport('joomla.application.component.view');

class JoomleagueViewImagehandler extends JLGView {

    /**
     * Image selection List
     *
     * @since 0.9
     */
    function display($tpl = null) {
        $app = Factory::getApplication();
        $document = Factory::getDocument();
        $uri = Uri::getInstance();


        if ($this->getLayout() == 'upload') {
            $this->_displayupload($tpl);
            return;
        }

        //get vars
        $type = Factory::getApplication()->input->getVar('type');
        $folder = ImageSelect::getfolder($type);
        $field = Factory::getApplication()->input->getVar('field');
        $fieldid = Factory::getApplication()->input->getVar('fieldid');
        $search = $app->getUserStateFromRequest('com_joomleague.imageselect', 'search', '', 'string');
        $search = trim(StringHelper::strtolower($search));

        Factory::getApplication()->input->setVar('folder', $folder);

        // Do not allow cache        
        //\Joomla\Http\Response::allowCache(false);

        //get images
        $images = $this->get('Images');
        $pageNav = $this->get('Pagination');

        $this->request_url = $uri->toString();

        if (count($images) > 0 || $search) {
            $this->images = $images;
            $this->type = $type;
            $this->folder = $folder;
            $this->search = $search;
            $this->state = $this->get('state');
            $this->pageNav = $pageNav;
            $this->field = $field;
            $this->fieldid = $fieldid;
            parent::display($tpl);
        } else {
            //no images in the folder, redirect to uploadscreen and raise notice
            Factory::getApplication()->enqueueMessage('SOME_ERROR_CODE', Text::_('COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_NO_IMAGES'),'notice');
            $this->setLayout('upload');
            $this->form = $this->get('form');
            $this->_displayupload($tpl);
            return;
        }
    }

    function setImage($index = 0) {
        if (isset($this->images[$index])) {
            $this->_tmp_img = &$this->images[$index];
        } else {
            $this->_tmp_img = new CMSObject;
        }
    }

    /**
     * Prepares the upload image screen
     *
     * @param $tpl
     *
     * @since 0.9
     */
    function _displayupload($tpl = null) {
        $app = Factory::getApplication();
        $option = $app->input->getCmd('option');
        

        //initialise variables
        $document = Factory::getDocument();
        $uri = Uri::getInstance();
        $params = ComponentHelper::getParams($option);
        $type = $app->input->getVar('type');
        $folder = ImageSelect::getfolder($type);
        $field = $app->input->getVar('field');
        $fieldid = $app->input->getVar('fieldid');
        $menu = $app->input->setVar('hidemainmenu', 1);
        //get vars
        $task = $app->input->getVar('task');

        jimport('joomla.client.helper');
        $ftp = ClientHelper::setCredentialsFromRequest('ftp');

        //assign data to template
        $this->params = $params;
        $this->request_url = $uri->toString();
        $this->ftp = $ftp;
        $this->folder = $folder;
        $this->field = $field;
        $this->fieldid = $fieldid;
        $this->menu = $menu;
        parent::display($tpl);
    }

}

?>
