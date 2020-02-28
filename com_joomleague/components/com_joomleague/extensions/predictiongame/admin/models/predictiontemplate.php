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
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . DS . 'models' . DS . 'item.php' );

/**
 * Joomleague Component Prediction template Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */
class JoomleagueModelPredictionTemplate extends JoomleagueModelItem
{
	/**
	 * Method to load content template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function _loadData()
	{
	$db = Factory::getDBO();
	$query = $db->getQuery(true);
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_data ) )
		{
			$query = '	SELECT *
						FROM #__joomleague_prediction_template
						WHERE id = ' . (int) $this->_id;

			$db->setQuery( $query );
			$this->_data = $db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the template data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_data ) )
		{
			$template					= new stdClass();
			$template->id				= 0;
			$template->title			= '';
			$template->prediction_id	= 0;
			$template->template			= '';
			$template->params			= null;
			$template->published		= 1;
			$template->checked_out		= 0;
			$template->checked_out_time	= 0;
			$this->_data				= $template;
			
			return (boolean) $this->_data;
			var_dump($template);
			
		}
		return true;
	}

  /**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	Table	A database object
	 * @since	1.6
	 */
	
	public function getTable($type = 'PredictionTemplate', $prefix = 'Table', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
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
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
		    array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
	
	/**
	 * Method to (un)publish a template
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5.0a
	 */
	function publish( $cid = array(), $publish = 1 )
	{
	    $app = Factory::getApplication();
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		$user = Factory::getUser();
		if ( count( $cid ) )
		{
			ArrayHelper::toInteger( $cid );
			$cids = implode( ',', $cid );

			$query =	'	UPDATE #__joomleague_prediction_template
							SET published = ' . (int) $publish . '
							WHERE id IN ( ' . $cids . ' )
							AND ( checked_out = 0 OR ( checked_out = ' . (int) $user->get( 'id' ) . ' ) )';
			try{
			    $db->setQuery($query)->execute();
			}
			catch (RunTimeException $e)
			{
			    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
			    return false;
			}
		}
		return true;
	}

	/**
	 * Method to remove selected items
	 * from #__joomleague_prediction_template
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */

	function delete( $cid = array() )
	{
	    $app = Factory::getApplication();
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		if ( count( $cid ) )
		{
			ArrayHelper::toInteger( $cid );
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM #__joomleague_prediction_template WHERE id IN ( ' . $cids . ' )';
			try{
			    $db->setQuery($query)->execute();
			}
			catch (RunTimeException $e)
			{
			    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
			   // return false;
			}
		}
		return true;
	}

	/**
	* Method to return a prediction game item array
	*
	* @access  public
	* @return  object
	*/
	function getPredictionGame( $id )
	{
	    $app = Factory::getApplication();
    	$db = Factory::getDBO();
    	$query = $db->getQuery(true);
		$query = '	SELECT	*
					FROM #__joomleague_prediction_game
					WHERE id = ' . (int) $id;
		try{
		    $db->setQuery($query);
		    $result = $db->loadObject();
		}
		catch (RunTimeException $e)
		{
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		    return false;
		}
			return $result;
	}
	/*public function getScript() 
	{
		return 'JLG_PATH_EXTENSION_PREDICTIONGAME./admin/models/forms/joomleague.js';
	}*/
	public function save($data)
	{
	   $app = Factory::getApplication();
       $date = Factory::getDate();
	   $user = Factory::getUser();
	   $post = $app->input->post->getArray();
	   // Set the values
	   $data['modified'] = $date->toSql();
	   $data['modified_by'] = $user->get('id');
       
       
       if (isset($post['params']) && is_array($post['params'])) 
		{
			// Convert the params field to a string.
			$parameter = new Registry;
			$parameter->loadArray($post['params']);
            //$paramsString = json_encode( $post['params'] );
			$data['params'] = (string)$parameter;
            //$data['params'] = $paramsString;
           
		}
        
        
       if ( parent::save($data) )
       {
			$id =  (int) $this->getState($this->getName().'.id');
            $isNew = $this->getState($this->getName() . '.new');
            $data['id'] = $id;
            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($this->getState(),true).'</pre>'),'Notice');
            
            if ( $isNew )
            {
                //Here you can do other tasks with your newly saved record...
                $app->enqueueMessage(Text::plural(strtoupper($option) . '_N_ITEMS_CREATED', $id),'');
            }
           
		}
        
        return true;    
    }

}
?>