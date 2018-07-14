<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
use Joomla\CMS\FACTORY;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
/**
 * Item Model
 *
 * @author Julien Vonthron <julien.vonthron@gmail.com>
*/
if(!class_exists('JoomleagueModelItem')) {
	class JoomleagueModelItem extends AdminModel
	{
		/**
		 * item id
		 *
		 * @var int
		 */
		var $_id=null;

		/**
		 * Project data
		 *
		 * @var array
		 */
		var $_data=null;

		/**
		 * cache for project data
		 * @var object
		 */
		var $_project=null;

		/**
		 * Constructor
		 */
		function __construct()
		{
			parent::__construct();
			$input = Factory::getApplication()->input;
			$array=$input->get('cid',array(0),'array');
			$edit=$input->get('edit',true);
			if($edit){
				$this->setId((int)$array[0]);
			}
		}

		/**
		 * Method to set the item identifier
		 *
		 * @access	public
		 * @param	int item identifier
		 */
		function setId($id)
		{
			// Set item id and wipe data
			$this->_id=$id;
			$this->_data=null;
		}

		/**
		 * Method to get an item
		 */
		function getData()
		{
			// Load the item data
			if (!$this->_loadData()){
				$this->_initData();
			}
			return $this->_data;
		}

		/**
		 * Tests if item is checked out
		 *
		 * @access	public
		 * @param	int	A user id
		 * @return	boolean	True if checked out
		 */
		function isCheckedOut($uid=0)
		{
			if ($this->_loadData())
			{
				if ($uid){
					return ($this->_data->checked_out && $this->_data->checked_out != $uid);
				}
				return $this->_data->checked_out;
			}
		}

		/**
		 * Method to store the item
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function store($data,$table='')
		{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);			
			if ($table=='')
			{
				$row = $this->getTable();
			}
			else
			{
				$row = Table::getInstance($table,'Table');
			}

			// Bind the form fields to the items table
			if (!$row->bind($data))
			{
				$this->setError(Text::_('COM_JOOMLEAGUE_ADMIN_ITEM_MODEL_ERROR_BIND'));
				return false;
			}

			// Create the timestamp for the date
			$row->checked_out_time=gmdate('Y-m-d H:i:s');

			// if new item,order last,but only if an ordering exist
			if ((isset($row->id)) && (isset($row->ordering)))
			{
				if (!$row->id && $row->ordering!=NULL)
				{
					$row->ordering=$row->getNextOrder();
				}
			}

			// Make sure the item is valid
			if (!$row->check())
			{
				try
					{
						//$db->execute();
					}
					catch (RuntimeException $e)
					{
						throw new Exception($e->getMessage());
					return false;
					}
			}
			// Store the item to the database
			if (!$row->store())
			{
				try
				{
					//$db->execute();
				}
				catch (RuntimeException $e)
				{
					throw new Exception($e->getMessage());
				return false;
				}
		
			}
			return $row->id;
		}

		/**
		 * Method to move an item
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function move($direction)
		{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
			$row = $this->getTable($this->getName());
			if (!$row->load($this->_id))
			{
				try
				{
					//$db->execute();
				}
				catch (RuntimeException $e)
				{
					throw new Exception($e->getMessage());
				return false;
				}
			}
			if (!$row->move($direction))
			{
				try
				{
				//$db->execute();
				}
				catch (RuntimeException $e)
				{
				throw new Exception($e->getMessage());
				return false;
				}
			return true;
			}
		}
		/**
		 * Return project data
		 * @param int id,default to selected project (stored in session)
		 * @return object
		 */
		function getProject($id=0)
		{
			$app = Factory::getApplication();
			$option = $app->input->get('option');
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			if (!$id) {
				$id=$app->getUserState($option.'project',0);
			}

			if (empty($this->_project) || $id != $this->_project->id)
			{
				$query->select('*') 
				->from ('#__joomleague_project')
				->where ('id='.$db->Quote($id));
				$db->setQuery($query,0,1);
				$this->_project=$db->loadObject();
			}
			return $this->_project;
		}

		/**
		 * Method to export one or more leagues
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function export($cid=array(),$table, $record_name)
		{
			$app = Factory::getApplication();
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			if (count($cid))
			{
				$mdlJLXExport = BaseDatabaseModel::getInstance("jlxmlexport", 'JoomleagueModel');
				ArrayHelper::toInteger($cid);
				$cids=implode(',',$cid);
				$query->select ('*')
				->from (' #__joomleague_".$table."')
				->where('id IN ('.$cids.')');
				$db->setQuery($query);
				$exportData=$db->loadObjectList();
				$output="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
				$output .= "<".$table."s>\n";
				// get the version of JoomLeague
				$output .= $mdlJLXExport->_addToXml($mdlJLXExport->_getJoomLeagueVersion());
				$tabVar='  ';
				$record_name=$record_name;
				foreach ($exportData as $name=>$value)
				{
					$output .= "<record object=\"".JoomleagueHelper::stripInvalidXml($record_name)."\">\n";
					foreach ($value as $name2=>$value2)
					{
						if (($name2!='checked_out') && ($name2!='checked_out_time'))
						{
							$output .= $tabVar.'<'.$name2.'><![CDATA['.JoomleagueHelper::stripInvalidXml(trim($value2)).']]></'.$name2.">\n";
						}
					}
					$output .= "</record>\n";
				}
				unset($name,$value);
				$output .= '</'.$table.'s>';
					
				$mdlJLXExport->downloadXml($output, $table);
				$app->close();
			}
		}

		/**
		 * Returns a Table object, always creating it
		 *
		 * @param	type	The table type to instantiate
		 * @param	string	A prefix for the table class name. Optional.
		 * @param	array	Configuration array for model. Optional.
		 * @return	Table	A database object
		 */
		public function getTable($type = 'tablename', $prefix = '', $config = array())
		{
			return Table::getInstance($type, $prefix, $config);
		}

		/**
		 * Method to get the record form.
		 *
		 * @param	array	$data		Data for the form.
		 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
		 * @return	mixed	A JForm object on success, false on failure
		 */
		public function getForm($data = array(), $loadData = true)
		{
			// Get the form.
			$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
					array('load_data' => $loadData) );
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
	}
}
