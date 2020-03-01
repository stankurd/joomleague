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
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JPATH_COMPONENT . '/models/list.php' );

/**
 * Joomleague Component prediction templates Model
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.100625
 */

class JoomleagueModelPredictionTemplates extends JoomleagueModelList
{

	var $_identifier = "predictiontemplates";
	
	var $_prediction_id	= null;

	public function __construct($config = array())
	{
		 $config['filter_fields'] = array(
                        'tmpl.title',
                        'tmpl.template',
                        'tmpl.id',
                        'tmpl.ordering',
                        'tmpl.modified',
                        'tmpl.modified_by'
						 );
	parent::__construct($config);
		$app		= Factory::getApplication();
		$prediction_id = $app->getUserState( 'com_joomleague' . 'prediction_id', 0 );
		$this->set( '_prediction_id', $prediction_id );
	}
/**	protected function populateState($ordering = null, $direction = null)
	{
		// Reference global application object
        $app = Factory::getApplication();
        // JInput object
        $input = $app->input;
        $option = $input->getCmd('option');
        // Initialise variables.
		//$app = Factory::getApplication('administrator');
        
        $app->enqueueMessage(JText::_(__METHOD__.' '.__LINE__.' context -> '.$this->context.''),'');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);
        
        $temp_user_request = $this->getUserStateFromRequest($this->context.'.filter.prediction_id', 'filter_prediction_id', '');
		$this->setState('filter.prediction_id', $temp_user_request);
        $app->setUserState( "com_joomleague.prediction_id", $temp_user_request );

		// List state information.
		parent::populateState('tmpl.title', 'asc');
	}*/
	function getData()
	{
		$this->checklist();
		return parent::getData();
	}

	function _buildQuery()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();
        
        // Create a new query object.
        $query = $this->_db->getQuery(true);
        $query->select(array('tmpl.*', 'u.name AS editor'))
        ->from('#__joomleague_prediction_template AS tmpl')
        ->join('LEFT', '#__users AS u ON u.id = tmpl.checked_out');

        if ($where)
        {
            $query->where($where);
        }
        if ($orderby)
        {
            $query->order($orderby);
        }

		
		return $query;
	}

	function _buildContentWhere()
	{
		$app				= Factory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		$where = array();
		$prediction_id = (int) $app->getUserState( 'com_joomleague' . 'prediction_id' );
		if ( $prediction_id > 0 )
		{
			$where[] = 'tmpl.prediction_id = ' . $prediction_id;
		}
		$where 	= ( count( $where ) ? ''. implode( ' AND ', $where ) : '' );

		return $where;
	}

	function _buildContentOrderBy()
	{
		$app				= Factory::getApplication();
		$option				= 'com_joomleague';

		$filter_order		= $app->getUserStateFromRequest( $option . 'tmpl_filter_order',		'filter_order',		'tmpl.title',	'cmd' );
		$filter_order_Dir	= $app->getUserStateFromRequest( $option . 'tmpl_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		if ( $filter_order == 'tmpl.title' )
		{
			$orderby 	= 'tmpl.title ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= '' . $filter_order . ' ' . $filter_order_Dir . ' , tmpl.title ';
		}

		return $orderby;
	}

	/**
	* Method to return a prediction games array
	*
	* @access  public
	* @return  array
	*/
	function getPredictionGames()
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query = "	SELECT	id AS value,
							name AS text
					FROM #__joomleague_prediction_game
					ORDER by name";

		try{
		    $db->setQuery($query);
		    $result = $db->loadObjectList();
		}
		catch (RunTimeException $e)
		{
		    $app->enqueueMessage(Text::_($e->getMessage()), 'error');
		    return false;
		}
		
		return $result;
	}

	/**
	* Method to return a prediction game item array
	*
	* @access  public
	* @return  object
	*/
	function getPredictionGame($id)
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
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

	/**
	 * check that all prediction templates in default location have a corresponding record, except if game has a master template
	 *
	 */
	function checklist()
	{
		// Reference global application object
        $app = Factory::getApplication();
        // JInput object
        $input = $app->input;
        $option = $input->getCmd('option');
        // Create a new query object.		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$prediction_id	= $this->_prediction_id;
		$defaultpath	= JLG_PATH_EXTENSION_PREDICTIONGAME . '/settings';
		$extensionspath	= JPATH_COMPONENT_SITE . '/extensions/';
		//$defaultpath	= JLG_PATH_SITE.'/extensions/predictiongame/settings';
		//$extensionspath	=  JLG_PATH_SITE.'/extensions/';
		$templatePrefix	= 'prediction';
		$path = JLG_PATH_EXTENSION_PREDICTIONGAME . '/views';

		if (!$prediction_id)
		{
			return;
		}
	
		// get info from prediction game
		// Select some fields
        $query->select('master_template');
		// From table
		$query->from('#__joomleague_prediction_game ');
        $query->where('id = ' . $prediction_id );
		$db->setQuery($query);
		$params = $db->loadObject();
	
		// if it's not a master template, do not create records.
		if ($params->master_template){return true;}
	
		// otherwise, compare the records with the files // get records
		$query->clear('');
		$query->select('template');
		// From table
		$query->from('#__joomleague_prediction_template ');
        $query->where('prediction_id = ' . $prediction_id );
		$db->setQuery($query);
		$records = $db->loadColumn();
		if (empty($records)){$records=array();}
	
		// first check extension template folder if template is not default
		if ((isset($params->extension)) && ($params->extension!=''))
		{
			if (is_dir($extensionspath . $params->extension . '/settings'))
			{
				$xmldirs[] = $extensionspath . $params->extension . '/settings';
			}
		}
	
		// add default folder
		$xmldirs[] = $defaultpath . '/default';
	
		// now check for all xml files in these folders
		foreach ($xmldirs as $xmldir)
		{
			if ($handle = opendir($xmldir))
			{
				/* check that each xml template has a corresponding record in the
					database for this project. If not, create the rows with default values
				from the xml file */
				while ($file = readdir($handle))
				{
					if ($file!='.'&&$file!='..'&&strtolower(substr($file,(-3)))=='xml'&&
							strtolower(substr($file,0,strlen($templatePrefix)))==$templatePrefix)
					{
						$template = substr($file,0,(strlen($file)-4));
						// Determine if a metadata file exists for the view.
				        //$metafile = $path.'/'.$template.'/metadata.xml';
                        $metafile = $path.'/'.$template.'/tmpl/default.xml';
                        $attributetitle = '';
                        if (is_file($metafile)) 
                        {
                        // Attempt to load the xml file.
					   if ($metaxml = simplexml_load_file($metafile)) 
                        {
                        //$app->enqueueMessage(JText::_('PredictionGame template metaxml-> '.'<br /><pre>~' . print_r($metaxml,true) . '~</pre><br />'),'');    
                        // This will save the value of the attribute, and not the objet
                        //$attributetitle = (string)$metaxml->view->attributes()->title;
                        $attributetitle = (string)$metaxml->layout->attributes()->title;
                        //$app->enqueueMessage(Text::_('PredictionGame template attribute-> '.'<br /><pre>~' . print_r($attributetitle,true) . '~</pre><br />'),'');
                        if ($menu = $metaxml->xpath('view[1]')) 
                        {
							$menu = $menu[0];
                            //$app->enqueueMessage(JText::_('PredictionGame template menu-> '.'<br /><pre>~' . print_r($menu,true) . '~</pre><br />'),'');
                            }
                        }
                        }
	
						if ((empty($records)) || (!in_array($template,$records)))
						{
							//template not present, create a row with default values
							$params = new JLParameter(null, $xmldir .'/' . $file);
							$Registry = new Registry;
							$form = Form::getInstance($file, $xmldir . DS . $file);
							$fieldsets = $form->getFieldsets();
							//get the values
							$defaultvalues = array();
							foreach ($fieldsets as $fieldset) 
              {
								foreach($form->getFieldset($fieldset->name) as $field) 
                {
									//echo 'field<br /><pre>~' . print_r($field,true) . '~</pre><br />';
                  $Registry->set($field->name, $field->value);
                  $defaultvalues[] = $field->name.'='.$field->value;
								}				
							}
							
                            $defaultvalues = $Registry->toString('ini');
                            
                            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' defaultvalues<br><pre>'.print_r($defaultvalues,true).'</pre>'),'');
                            
                            $parameter = new Registry;
			              
							$ini = $parameter->loadString($defaultvalues);
       
			                $ini = $parameter->toArray($ini);
			                $defaultvalues = json_encode( $ini );
                            
		// otherwise, compare the records with the files // get records
        $query->clear('');
        // Select some fields
        $query->select('id');
		// From table
		$query->from('#__joomleague_prediction_template ');
        $query->where('prediction_id = ' . $prediction_id );
        $query->where('template LIKE '.$db->Quote(''.$template.''));
        
        //$app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' dump<br><pre>'.print_r($query->dump(),true).'</pre>'),'');
        

		$db->setQuery($query);
		$record_tpl = $db->loadResult();
        if( !$record_tpl )
        {                   
                           $mdl = BaseDatabaseModel::getInstance("predictiontemplate", "JoomleagueModel");
                            $tblTemplate_Config = $mdl->getTable();
							
                            $tblTemplate_Config->template = $template;
                            if ( $attributetitle )
                            {
                                $tblTemplate_Config->title = $attributetitle;
                            }
                            else
                            {
                                $tblTemplate_Config->title = $file;
                            }
							
							$tblTemplate_Config->params = $defaultvalues;
							$tblTemplate_Config->prediction_id = $prediction_id;
							
							// Store the item to the database
							if (!$tblTemplate_Config->store())
							{
								$this->setError($this->_db->getErrorMsg());
								return false;
							}
							array_push($records,$template);
		}					
							
						}
					}
				}
				closedir($handle);
			}
		}
	}

}
?>