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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

require_once(JPATH_COMPONENT . '/helpers/pagination.php');
JLoader::register('JoomleagueHelper',JPATH_SITE.'/administrator/components/com_joomleague/helpers/joomleague.php');
/**
 * Joomleague Component prediction View
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100628
 */
class JoomleagueViewPredictionEntry extends JLGView
{

	function display($tpl=null)
	{
    $app = Factory::getApplication();
		// Get a refrence of the page instance in joomla
    $document	= Factory::getDocument();
    $option = $app->input->getCmd('option');
    $optiontext = strtoupper($app->input->getCmd('option').'_');
    $this->optiontext = $optiontext;
    
		$model		= $this->getModel();
		$model->checkStartExtension();
		$this->predictionGame = $model->getPredictionGame();
		if (isset($this->predictionGame))
		{
			//echo '<br /><pre>~' . print_r($this->getName(),true) . '~</pre><br />';
			$config		= $model->getPredictionTemplateConfig($this->getName());
			$overallConfig	= $model->getPredictionOverallConfig();

			$this->debuginfo = $model->getDebugInfo();
			$this->model = $model;
			$this->config = array_merge($overallConfig,$config);
			$configavatar = $model->getPredictionTemplateConfig('predictionusers');
			$this->configavatar = $configavatar;
			$this->predictionMember = $model->getPredictionMember($configavatar);
			$this->predictionProjectS = $model->getPredictionProjectS();
			$this->actJoomlaUser = Factory::getUser();
			//$this->allowedAdmin = $model->getAllowed();
			$mdlPrediction = BaseDatabaseModel::getInstance('Prediction' , 'JoomleagueModel');
			$this->allowedAdmin = $mdlPrediction->getAllowed();
			$this->isPredictionMember = $mdlPrediction->checkPredictionMembership();
			$this->isNotApprovedMember = $mdlPrediction->checkIsNotApprovedPredictionMember();
			$this->isNewMember = $model->newMemberCheck();
			$this->tippEntryDone = $model->tippEntryDoneCheck();
			//$this->websiteName = Factory::getConfig()->getValue('config.sitename');
			
			//echo $this->loadTemplate( 'assignRefs' );
			//echo '<br /><pre>~' . print_r($this->predictionMember,true) . '~</pre><br />';

			if ($this->allowedAdmin)
			{
				$lists = array();
				if ($this->predictionMember->pmID > 0){$dMemberID=$this->predictionMember->pmID;}else{$dMemberID=0;}
				$predictionMembers[] = HTMLHelper::_('select.option','0',Text::_('COM_JOOMLEAGUE_PRED_SELECT_MEMBER'),'value','text');
				if ($res=$model->getPredictionMemberList($this->config)){$predictionMembers=array_merge($predictionMembers,$res);}
				$lists['predictionMembers']=HTMLHelper::_('select.genericList',$predictionMembers,'uid','class="inputbox" onchange="this.form.submit(); "','value','text',$dMemberID);
				unset($res);
				unset($predictionMembers);
				$this->lists = $lists;
			}

			$this->show_debug_info = ComponentHelper::getParams('com_joomleague')->get('show_debug_info',0);
			// Set page title
			$pageTitle = Text::_('COM_JOOMLEAGUE_PRED_ENTRY_TITLE');

			$document->setTitle($pageTitle);

			parent::display($tpl);
		}
		else
		{
			$app->enqueueMessage(500,Text::_('COM_JOOMLEAGUE_PRED_PREDICTION_NOT_EXISTING'),'notice');
		}
	}
	
	function createStandardTippSelect($tipp_home=NULL,$tipp_away=NULL,$tipp=NULL,$pid='0',$mid='0',$seperator,$allow)
	{
		if (!$allow){
			$disabled=' disabled="disabled" ';
			$css = "readonly";
		} else {
			$disabled='';
			$css = "inputbox";
		}
		$output = '';
		$output .= '<input type="hidden" name="tipps[' . $pid . '][' . $mid . ']" value="' . $tipp . '" />';
		$output .= '<input name="homes[' . $pid . '][' . $mid . ']" class="'.$css.'" style="text-align:center; " size="2" value="' . $tipp_home . '" tabindex="1" type="text" ' . $disabled . '/>';
		$output .= ' <b>' . $seperator . '</b> ';
		$output .= '<input name="aways[' . $pid . '][' . $mid . ']" class="'.$css.'" style="text-align:center; " size="2" value="' . $tipp_away . '" tabindex="1" type="text" ' . $disabled . '/>';
		if (!$allow)
		{
			$output .= '<input type="hidden" name="homes[' . $pid . '][' . $mid . ']" value="' . $tipp_home . '" />';
			$output .= '<input type="hidden" name="aways[' . $pid . '][' . $mid . ']" value="' . $tipp_away . '" />';
		}
		return $output;
	}

	function createTotoTippSelect($tipp_home=NULL,$tipp_away=NULL,$tipp=NULL,$pid='0',$mid='0',$allow)
	{
		
if ( $this->debuginfo )
{
echo 'tipp_home -> ' . $tipp_home. '<br>';
echo 'tipp_away -> ' . $tipp_away. '<br>';
echo 'tipp -> ' . $tipp. '<br>';
echo 'pid -> ' . $pid. '<br>';
echo 'mid -> ' . $mid. '<br>';
echo 'allow -> ' . $allow. '<br>';
}
    
    
    if (!$allow){$disabled=' disabled="disabled" ';}else{$disabled='';}
		$output = '';
		$output .= '<input type="hidden" name="homes[' . $pid . '][' . $mid . ']" value="' . $tipp_home . '" />';
		$output .= '<input type="hidden" name="aways[' . $pid . '][' . $mid . ']" value="' . $tipp_away . '" />';
		$outputArray = array	(
									HTMLHelper::_('select.option','',	Text::_('JL_PRED_ENTRY_NO_TIPP'),	'id','name'),
									HTMLHelper::_('select.option','1',	Text::_('JL_PRED_ENTRY_HOME_WIN'),	'id','name'),
									HTMLHelper::_('select.option','0',	Text::_('JL_PRED_ENTRY_DRAW'),		'id','name'),
									HTMLHelper::_('select.option','2',	Text::_('JL_PRED_ENTRY_AWAY_WIN'),	'id','name')
								);
		$output .= HTMLHelper::_('select.genericlist',$outputArray,'tipps['.$pid.']['.$mid.']','class="inputbox" size="1" ' . $disabled,'id','name',$tipp);
		unset($outputArray);
		if (!$allow)
		{
			$output .= '<input type="hidden" name="tipps['.$pid.']['.$mid.']" value="' . $tipp . '" />';
		}
		return $output;
	}

}
?>