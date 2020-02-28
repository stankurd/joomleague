<?php
/**
 * @copyright	Copyright (C) 2006-2013 JoomLeague.net. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @static
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueViewpredictiongroup extends JLGView
{

	public function display($tpl=null)
	{
		$app = Factory::getApplication();

		if ($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}

		//get the project
		$season = $this->get('data');

		parent::display($tpl);
	}

	public function _displayForm($tpl)
	{
		$option = Factory::getApplication()->input->getCmd('option');
		$app = Factory::getApplication();
		$db = Factory::getDBO();
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$model = $this->getModel();
		
		//get the season
		$season = $this->get('data');
		$isNew=($season->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=Text::sprintf('DESCBEINGEDITTED',Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIOGROUP'),$season->name);
			$app->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
		else
		{
			// initialise new record
			$season->order=0;
		}

		$this->season = $season;
		$this->form = $this->get('Form');
		//$this->cfg_which_media_tool = JComponentHelper::getParams($option)->get('cfg_which_media_tool',0);
		//$extended = $this->getExtended($season->extended, 'season');
		//$this->assignRef( 'extended', $extended );
		$this->addToolbar();			
		parent::display($tpl);
	}

	/**
	* Add the page title and toolbar.
	*
	* @since	1.7
	*/
	protected function addToolbar()
	{	

		// Set toolbar items for the page
		$edit=Factory::getApplication()->input->getVar('edit',true);
		$text=!$edit ? Text::_('COM_JOOMLEAGUE_GLOBAL_NEW') : Text::_('COM_JOOMLEAGUE_GLOBAL_EDIT');

		JLToolBarHelper::save('predictiongroup.save');

		if (!$edit)
		{
			JToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIOGROUP_ADD_NEW'),'predictiongroups');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('predictiongroup.cancel');
		}
		else
		{
			// for existing items the button is renamed `close` and the apply button is showed
			JToolBarHelper::title(Text::_('COM_JOOMLEAGUE_ADMIN_PREDICTIOGROUP_EDIT'),'predictiongroups');
			JLToolBarHelper::apply('predictiongroup.apply');
			JToolBarHelper::divider();
			JLToolBarHelper::cancel('predictiongroup.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JToolBarHelper::divider();
	}		
}
?>