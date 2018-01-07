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
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
 *
 * @author	Kurt Norgaz
 */
class JoomleagueViewUpdates extends JLGView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$app->setUserState($option.'update_part',0); // 0
		$filter_order		= $app->getUserStateFromRequest($option.'updates_filter_order',		'filter_order',		'dates',	'cmd');
		$filter_order_Dir	= $app->getUserStateFromRequest($option.'updates_filter_order_Dir',	'filter_order_Dir',	'',			'word');
		// Set toolbar items for the page
		JLToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_UPDATES_TITLE'),'generic.png');
		JLToolBarHelper::help('screen.joomleague',true);
		$db = Factory::getDbo();
		$uri = Uri::getInstance();
		$model = $this->getModel('updates');
		$versions=$model->getVersions();
		
		$updateFiles = array();
		$lists=array();
		if($updateFiles=$model->loadUpdateFiles()) {
			for ($i=0, $n=count($updateFiles); $i < $n; $i++)
			{
				foreach ($versions as $version)
				{
					if (strpos($version->version,$updateFiles[$i]['file_name']))
					{
						$updateFiles[$i]['updateTime']=$version->date;
						break;
					}
					else
					{
						$updateFiles[$i]['updateTime']="-";
					}
				}
			}
		}
		// table ordering
		$lists['order_Dir']=$filter_order_Dir;
		$lists['order']=$filter_order;
		$this->items = $updateFiles;
		$this->request_url = $uri->toString();
		$this->lists = $lists;
		parent::display($tpl);
	}
}
