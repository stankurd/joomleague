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
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;

jimport('joomla.application.component.model');

// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
// import Joomla modelitem library
jimport('joomla.application.component.modelitem');
// Include dependancy of the dispatcher
jimport('joomla.event.dispatcher');

require_once(JLG_PATH_EXTENSION_PREDICTIONGAME .'models'.DS.'prediction.php' );
/**
 * Joomleague Component prediction Members Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */
class JoomleagueModelPredictionUser extends FormModel
{
    var $predictionGameID = 0;
    var $predictionMemberID = 0;
	
	/**
	 * JoomleagueModelPrediction::__construct()
	 * 
	 * @return void
	 */
	function __construct()
	{
    // Reference global application object
        $app = Factory::getApplication();
        // JInput object
        $input = $app->input;
        $option = $input->getCmd('option');
        
        $prediction = new JoomleagueModelPrediction();  
      
       JoomleagueModelPrediction::$roundID = $input->getVar('r','0');
       JoomleagueModelPrediction::$pjID = $input->getVar('pj','0');
       JoomleagueModelPrediction::$from = $input->getVar('from',$input->getVar('r','0'));
       JoomleagueModelPrediction::$to = $input->getVar('to',$input->getVar('r','0'));
       
        JoomleagueModelPrediction::$predictionGameID = $input->getVar('prediction_id','0');
        
        JoomleagueModelPrediction::$predictionMemberID = $input->getInt('uid',0);
        JoomleagueModelPrediction::$joomlaUserID = $input->getInt('juid',0);
        
        JoomleagueModelPrediction::$pggroup = $input->getInt('pggroup',0);
        JoomleagueModelPrediction::$pggrouprank = $input->getInt('pggrouprank',0);
        
        JoomleagueModelPrediction::$isNewMember = $input->getInt('s',0);
        JoomleagueModelPrediction::$tippEntryDone = $input->getInt('eok',0);
        
        JoomleagueModelPrediction::$type = $input->getInt('type',0);
        JoomleagueModelPrediction::$page = $input->getInt('page',1);
       
		parent::__construct();
	}



  
  /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = Factory::getApplication('site');
    // Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	

		
}
?>