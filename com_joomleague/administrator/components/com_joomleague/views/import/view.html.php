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
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;


/**
 * View class for the import screen
 */
class JoomleagueViewImport extends JLGView
{

	function display($tpl = null)
	{
		$table = Factory::getApplication()->input->get('table');
		//initialise variables
		$document	= Factory::getDocument();
		$user 		= Factory::getUser();

		//build toolbar
		#JLToolBarHelper::title(Text::_('IMPORT'), 'home');
		ToolbarHelper::title(Text::_('JoomLeague CSV-Import - Step 1 of 2'), 'generic.png');
		ToolBarHelper::back();
		ToolBarHelper::help('joomleague.import',true);

		// Get data from the model
		$model = $this->getModel("import");
		$tablefields = $model->getTablefields('#__joomleague_' . $table);
		
		

		//assign vars to the template
		$this->tablefields=$tablefields;
		$this->table=$table;
		parent::display($tpl);
	}
}
