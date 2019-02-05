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
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Client\ClientHelper;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Filter\InputFilter;

jimport('joomla.filesystem.file');
//require_once (JPATH_COMPONENT.DS.'helpers'.DS.'imageselect.php');
//require_once (JPATH_COMPONENT_SITE . DS . 'helpers' . DS . 'imageselect.php');

class JoomleagueControllerImagehandler extends JoomleagueController
{

    /**
     * Constructor
     *
     * @since 0.9
     */
    function __construct() {
        parent::__construct();

        // Register Extra task
    }

    /**
     * logic for uploading an image
     *
     * @access public
     * @return void
     * @since 0.9
     */
    function upload() {
        $app = Factory::getApplication();
        $option = Factory::getApplication()->input->getCmd('option');

        // Check for request forgeries
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        $file = Factory::getApplication()->input->getVar('userfile', '', 'files', 'array');
        $type = Factory::getApplication()->input->getVar('type');
        $folder = ImageSelect::getfolder($type);
        $field = Factory::getApplication()->input->getVar('field');
        $linkaddress = Factory::getApplication()->input->getVar('linkaddress');
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        ClientHelper::setCredentialsFromRequest('ftp');
        //$ftp = ClientHelper::getCredentials( 'ftp' );
        //set the target directory
        $base_Dir = JPATH_SITE . '/images/com_joomleague/database/ . $folder . /';

        $app->enqueueMessage(Text::_($type), '');
        $app->enqueueMessage(Text::_($folder), '');
        $app->enqueueMessage(Text::_($base_Dir), '');

        //do we have an imagelink?
        if (!empty($linkaddress)) {
            $file['name'] = basename($linkaddress);

            if (preg_match("/dfs_/i", $linkaddress)) {
                $filename = $file['name'];
            } else {
                //sanitize the image filename
                $filename = ImageSelect::sanitize($base_Dir, $file['name']);
            }

            $filepath = $base_Dir . $filename;

            if (!copy($linkaddress, $filepath)) {
                echo "<script> alert('" . Text::_('COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_COPY_FAILED') . "'); window.history.go(-1); </script>\n";
                //$app->close();
            } else {
                //echo "<script> alert('" . Text::_( 'COPY COMPLETE'.'-'.$folder.'-'.$type.'-'.$filename.'-'.$field ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
                echo "<script>  window.parent.selectImage_" . $type . "('$filename', '$filename','$field');window.parent.SqueezeBox.close(); </script>\n";
                //$app->close();
            }
        }

        //do we have an upload?
        if (empty($file['name'])) {
            echo "<script> alert('" . Text::_('COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_CTRL_IMAGE_EMPTY') . "'); window.history.go(-1); </script>\n";
            //$app->close();
        }

        //check the image
        $check = ImageSelect::check($file);

        if ($check === false) {
            $app->redirect($_SERVER['HTTP_REFERER']);
        }

        //sanitize the image filename
        $filename = ImageSelect::sanitize($base_Dir, $file['name']);
        $filepath = $base_Dir . $filename;

        //upload the image
        if (!File::upload($file['tmp_name'], $filepath)) {
            echo "<script> alert('" . Text::_('COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_FAILED') . "'); window.history.go(-1); </script>\n";
//          $app->close();
        } else {
//          echo "<script> alert('" . Text::_( 'COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_COMPLETE'.'-'.$folder.'-'.$type.'-'.$filename.'-'.$field ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
//          echo "<script> alert('" . Text::_( 'COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_CTRL_UPLOAD_COMPLETE' ) . "'); window.history.go(-1); window.parent.selectImage_".$type."('$filename', '$filename','$field'); </script>\n";
            echo "<script>  window.parent.selectImage_" . $type . "('$filename', '$filename','$field');window.parent.SqueezeBox.close(); </script>\n";
//          $app->close();
        }
    }

    /**
     * logic to mass delete images
     *
     * @access public
     * @return void
     * @since 0.9
     */
    function delete() {
        $app = Factory::getApplication();
        $option = Factory::getApplication()->input->getCmd('option');
        
        // Set FTP credentials, if given
        jimport('joomla.client.helper');
        ClientHelper::setCredentialsFromRequest('ftp');

        // Get some data from the request
        $images = Factory::getApplication()->input->getVar('rm', array(), '', 'array');
        $type = Factory::getApplication()->input->getVar('type');

        $folder = ImageSelect::getfolder($type);

        if (count($images)) {
            foreach ($images as $image) {
                if ($image !== InputFilter::clean($image, 'path')) {
                    $app->enqueueMessage(100, Text::_('COM_JOOMLEAGUE_ADMIN_IMAGEHANDLER_CTRL_UNABLE_TO_DELETE') . ' ' . htmlspecialchars($image, ENT_COMPAT, 'UTF-8'));
                    continue;
                }

                $fullPath = Path::clean(JPATH_SITE . DS . 'images' . DS . $option . DS . 'database' . DS . $folder . DS . $image);
                $fullPaththumb = Path::clean(JPATH_SITE . DS . 'images' . DS . $option . DS . 'database' . DS . $folder . DS . 'small' . DS . $image);
                if (is_file($fullPath)) {
                    File::delete($fullPath);
                    if (File::exists($fullPaththumb)) {
                        File::delete($fullPaththumb);
                    }
                }
            }
        }

        $app->redirect('index.php?option=' . $option . '&view=imagehandler&type=' . $type . '&tmpl=component');
    }

}

?>