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
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;


/**
 * HTML View class
*/
class JoomleagueViewTemplate extends JLGView
{
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$uri = Uri::getInstance();
		$user = Factory::getUser();
		$model = $this->getModel();
		$baseurl = Uri::root(true);
		$lists=array();
		$document = Factory::getDocument();
		$document->addScript($baseurl .'/administrator/components/com_joomleague/assets/js/template.js');
		//get template data
		$template = $this->get('data');
		$isNew=($template->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_THETEMPLATE'),$template->name);
			$app->redirect('index.php?option='.$option,$msg);
		}

		//$mdlProject = new JoomleagueModelProject();
		$mdlProject = BaseDatabaseModel::getInstance('project','JoomleagueModel');
		$project_id = $app->getUserState($option.'project');
		$project 	= $mdlProject->getItem($project_id);
		
		$templatepath=JPATH_COMPONENT_SITE.'/settings';
		$xmlfile=$templatepath.'/default/'.$template->template.'.xml';

		$extensions = JoomleagueHelper::getExtensions($app->input->getInt('p'));
		foreach ($extensions as $e => $extension) {
			$extensiontpath =  JPATH_COMPONENT_SITE.'/extensions/'.$extension;
			if (is_dir($extensiontpath.'/settings/default'))
			{
				if (file_exists($extensiontpath.'/settings/default/'.$template->template.'.xml'))
				{
					$xmlfile=$extensiontpath.'/settings/default/'.$template->template.'.xml';
				}
			}
		}
		
		$form = Form::getInstance($template->template, $xmlfile,array('control'=> 'params'));
		$form->bind($template->params);
		
		$master_id=($project->master_template) ? $project->master_template : '-1';
		$templates=array();
		$templates[]=HTMLHelper::_('select.option','0',JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_OTHER_TEMPLATE' ),'value','text');
		if ($res=$model->getAllTemplatesList($project->id,$master_id)){
			$templates=array_merge($templates,$res);
		}
		$lists['templates']=HTMLHelper::_('select.genericlist',	$templates,
		    'select_id',
		    'class="inputbox" size="1" onchange="javascript: Joomla.submitbutton(\'template.apply\');"',
		    'value',
		    'text',
		    $template->id);
		unset($res);
		unset($templates);

		$this->request_url=$uri->toString();
		$this->template=$template;
		$this->form=$form;
		$this->project=$project;
		$this->lists=$lists;
		$this->user=$user;

		$this->addToolbar();
		parent::display($tpl);
	}
	
	
	/**
	 * Add the page title and toolbar
	 */
	protected function addToolbar()
	{
	    
		// Set toolbar items for the page
		$edit=Factory::getApplication()->input->get('edit',true);
		ToolbarHelper::saveGroup(
		    [
		        ['apply', 'template.apply'],
		        ['save', 'template.save'],
		    ],
		    'btn-success'
		    );
		//ToolbarHelper::save('template.save');
		//ToolbarHelper::apply('template.apply');

		if (!$edit)
		{
			ToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_ADD_NEW'),'jl-FrontendSettings');
			ToolbarHelper::divider();
			ToolbarHelper::cancel('template.cancel');
		}
		else
		{
			ToolbarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_TEMPLATE_EDIT').': '. $this->form->getName(),'jl-FrontendSettings');
			//ToolbarHelper::custom('template.reset','restore','restore','COM_JOOMLEAGUE_GLOBAL_RESET');
			ToolbarHelper::divider();
			
			// for existing items the button is renamed `close`
			ToolbarHelper::cancel('template.cancel','COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		ToolbarHelper::help('screen.joomleague',true);
	}
	
}

