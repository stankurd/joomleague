<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;

/**
 * Templates Controller
 */
class JoomleagueControllerTemplates extends AdminController
{
    public function changetemplate()
    {
        $post=Factory::getApplication()->input->post->getArray();
        $msg = '';
        $this->setRedirect('index.php?option=com_joomleague&view=template&layout=edit&id='.$post['new_id'],$msg);
    }
    
    
    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'template', $prefix = 'JoomleagueModel', $config = Array() )
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }
}
