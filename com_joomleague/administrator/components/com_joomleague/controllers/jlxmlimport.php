<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\Archive\Archive;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.archive');

/**
 * JLXMLImport Controller
 *
 * @author	Kurt Norgaz
 */
class JoomleagueControllerJLXMLImport extends JoomleagueController
{
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('edit','display');
		$this->registerTask('insert','display');
		$this->registerTask('selectpage','display');
	}

	public function display($cachable = false, $urlparams = false)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		switch ($this->getTask())
		{
			case 'edit':
				$input->set('hidemainmenu',0);
				$input->set('layout','form');
				$input->set('view','jlxmlimports');
				$input->set('edit',true);
				break;

			case 'insert':
				$input->set('hidemainmenu',0);
				$input->set('layout','info');
				$input->set('view','jlxmlimports');
				$input->set('edit',true);
				break;
		}

		parent::display($cachable, $urlparams);
	}

	public function select()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$selectType=$input->getInt('type',0);
		$recordID=$input->getInt('id',0);
		$app->setUserState('com_joomleague'.'selectType',$selectType);
		$app->setUserState('com_joomleague'.'recordID',$recordID);

		$input->set('hidemainmenu',1);
		$input->set('layout','selectpage');
		$input->set('view','jlxmlimports');

		parent::display();
	}

	public function save()
	{
		$app = Factory::getApplication();
		// Check for request forgeries
		Session::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$msg='';
		JLToolBarHelper::back(JText::_('COM_JOOMLEAGUE_GLOBAL_BACK'),Route::_('index.php?option=com_joomleague&task=jlxmlimport.display'));
		$input = $app->input;
		$post=$input->post->getArray();

		// first step - upload
		if (isset($post['sent']) && $post['sent']==1)
		{
			$upload=$input->files->get('import_package',null,'array');
			$tempFilePath=$upload['tmp_name'];
			$app->setUserState('com_joomleague'.'uploadArray',$upload);
			$filename='';
			$msg='';
			$dest=JPATH_SITE.'/tmp/'.$upload['name'];
			$extractdir=JPATH_SITE.'/tmp';
			$importFile=JPATH_SITE.'/tmp/joomleague_import.jlg';
			if (JFile::exists($importFile))
			{
				JFile::delete($importFile);
			}
			if (JFile::exists($tempFilePath))
			{
					if (JFile::exists($dest))
					{
						JFile::delete($dest);
					}
					if (!JFile::upload($tempFilePath,$dest))
					{
						JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_CANT_UPLOAD'));
						return;
					}
					else
					{
						if (strtolower(JFile::getExt($dest))=='zip')
						{
							$result=Archive::extract($dest,$extractdir);
							if ($result === false)
							{
								JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_EXTRACT_ERROR'));
								return false;
							}
							JFile::delete($dest);
							$src=JFolder::files($extractdir,'jlg',false,true);
							if(!count($src))
							{
								JError::raiseWarning(500,'COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_EXTRACT_NOJLG');
								//todo: delete every extracted file / directory
								return false;
							}
							if (strtolower(JFile::getExt($src[0]))=='jlg')
							{
								if (!@ rename($src[0],$importFile))
								{
									JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_ERROR_RENAME'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(500,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_TMP_DELETED'));
								return;
							}
						}
						else
						{
							if (strtolower(JFile::getExt($dest))=='jlg')
							{
								if (!@ rename($dest,$importFile))
								{
									JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_RENAME_FAILED'));
									return false;
								}
							}
							else
							{
								JError::raiseWarning(21,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_CTRL_WRONG_EXTENSION'));
								return false;
							}
						}
					}
			}
		}
		$link='index.php?option=com_joomleague&task=jlxmlimport.edit';
		$this->setRedirect($link,$msg);
	}

	public function insert()
	{
		$app = Factory::getApplication();
		JLToolBarHelper::back(JText::_('COM_JOOMLEAGUE_GLOBAL_BACK'),Route::_('index.php?option=com_joomleague'));
		$input = $app->input;
		$post=$input->post->getArray();

		$link='index.php?option=com_joomleague&task=jlxmlimport.insert';
		echo $link;

		#$this->setRedirect($link);
	}

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_joomleague&jlxmlimport.display');
	}
}
