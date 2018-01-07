<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;


/**
 * Projectreferee Controller
 */
class JoomleagueControllerProjectReferee extends JLGControllerForm
{

	public function __construct($config = array())
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$jinput->set('layout','form');

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


	/**
	 */
	public function saveassigned()
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$cid = $jinput->get('cid',array(),'array');
		ArrayHelper::toInteger($cid);
		$post = $jinput->post->getArray();
		if(!is_array($cid) || count($cid) < 1)
		{
			$app->enqueueMessage(Text::_('Select person to assign as referee'),'error');
		}
		else
		{
			$project_id = $app->getUserState('com_joomleagueproject');
			$model = $this->getModel('projectreferees');

			if($model->storeassigned($cid,$project_id))
			{
				$msg = Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_PERSON_ASSIGNED_AS_REFEREE');
			}
			else
			{
				$msg = Text::_('COM_JOOMLEAGUE_ADMIN_PERSON_CTRL_ERROR_PERSON_ASSIGNED_AS_REFEREE') . $model->getError();
			}
		}

		$link = 'index.php?option=com_joomleague&view=projectreferees&layout=assignplayers';
		$this->setRedirect($link,$msg);
	}
}
