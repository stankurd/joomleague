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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;


/**
 * Treeto Controller
 */
class JoomleagueControllerTreeto extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$input->set('layout','form');

		parent::__construct($config);
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


	public function generatenode()
	{
		// Check for token
		Session::checkToken() or jexit(Text::_('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;
		$option = $input->getCmd('option');
		$post = $input->post->getArray();
		$model = $this->getModel('treeto');

		// set Projectid
		$project_id = $app->getUserState($option . 'project');
		// trigger model function
		if($model->setGenerateNode($post))
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_GENERATE_NODE'),'notice');
			$link = 'index.php?option=com_joomleague&view=treetonodes&tid[]=' . $post['id'];
		}
		else
		{
		    $this->setMessage(Text::_('COM_JOOMLEAGUE_ADMIN_TREETO_CTRL_ERROR_GENERATE_NODE') . $model->getError(),'error');
			$link = 'index.php?option=com_joomleague&view=treetos';
		}
		$this->setRedirect($link,$msg);
	}
}
