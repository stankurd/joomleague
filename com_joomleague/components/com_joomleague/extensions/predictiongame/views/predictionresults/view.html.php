<?php
/**
* @copyright	Copyright (C) 2007-2012 JoomLeague.net. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport( 'joomla.filesystem.file' );

require_once(JPATH_COMPONENT . DS . 'helpers' . DS . 'pagination.php');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueViewPredictionResults extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document	= Factory::getDocument();
		$model		= $this->getModel();
		
		
		
		
		//$this->limit = $this->model->getLimit();
		//$this->limitstart = $this->model->getLimitStart();
		$this->predictionGame = $model->getPredictionGame();
		$this->allowedAdmin = $model->getAllowed();

		if (isset($this->predictionGame))
		{
			$config		= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();

			$this->model = $model;
			$this->roundID = $this->model->roundID;
			$this->config = array_merge($overallConfig,$config);

			$this->predictionMember = $model->getPredictionMember();
			$this->predictionProjectS = $model->getPredictionProjectS();
			$this->actJoomlaUser = Factory::getUser();
			//$this->rounds = $model->getRounds();
			//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';

			// Set page title
			$pageTitle = Text::_('COM_JOOMLEAGUE_PRED_RESULTS_TITLE');
			// Get data from the model
			$items = $model->getPredictionMembersList($this->config,$this->configavatar,false);
			//$items = $this->get('Data');	
			$pagination = $this->get('Pagination');
			$this->memberList = $items;
			$this->pagination = $pagination;
			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			Factory::getApplication()->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_PRED_PREDICTION_NOT_EXISTING'),'notice');
		}
	}

}
?>