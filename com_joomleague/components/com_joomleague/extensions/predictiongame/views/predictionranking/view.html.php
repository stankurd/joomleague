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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100627
 */
class JoomleagueViewPredictionRanking extends JLGView
{
	function display($tpl=null)
	{
		// Get a refrence of the page instance in joomla
		$document	= Factory::getDocument();
		$model		= $this->getModel();

		$this->predictionGame = $model->getPredictionGame();

		if (isset($this->predictionGame))
		{
			$config		= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();

			$this->model=$model;
			$this->roundID = $this->model->roundID;
			$this->config = array_merge($overallConfig,$config);

			$this->predictionMember = $model->getPredictionMember();
			$this->predictionProjectS = $model->getPredictionProjectS();
			$this->actJoomlaUser = Factory::getUser();
			//echo '<br /><pre>~' . print_r( $this->config, true ) . '~</pre><br />';

			$type_array = array();
			$type_array[]=HTMLHelper ::_('select.option','0',Text::_('COM_JOOMLEAGUE_PRED_RANK_FULL_RANKING'));
			$type_array[]=HTMLHelper ::_('select.option','1',Text::_('COM_JOOMLEAGUE_PRED_RANK_FIRST_HALF'));
			$type_array[]=HTMLHelper ::_('select.option','2',Text::_('COM_JOOMLEAGUE_PRED_RANK_SECOND_HALF'));
			$lists['type']=$type_array;
			unset($type_array);

			$this->lists = $lists;

			// Set page title
			$pageTitle = Text::_('COM_JOOMLEAGUE_PRED_RANK_TITLE');

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