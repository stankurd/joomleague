<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * @author 		Julien Vonthron <julien.vonthron@gmail.com>
 */
defined('_JEXEC') or die;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;


/**
 * Item Model
*/
class JLGModelItem extends AdminModel
{
	
	   /**
	 	* Constructor
	 	*/
		public function __construct($config = array())
		{
			parent::__construct($config);
			
			$this->input = Factory::getApplication()->input;	
		}
	
		
		/**
		 * Method to export
		 *
		 * @access	public
		 * @return	boolean	True on success
		 */
		function export($cid=array(),$table, $record_name)
		{
			if ($cid)
			{
				$mdlJLXExport = BaseDatabaseModel::getInstance("jlxmlexport", 'JoomleagueModel');
				ArrayHelper::toInteger($cid);
				$cids = implode(',',$cid);
				
				$db = Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select('*');
				$query->from('#__joomleague_'.$table);
				$query->where('id IN ('.$cids.')');
				$db->setQuery($query);
				$exportData = $db->loadObjectList();
				
				// Output
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
					
				$ignoreProject = false;
				$cFileName = false;
				if ($table == 'sports_type') {
					$ignoreProject	= true;
					$cFileName		= 'sportstype'; 
				}
				
				$mdlJLXExport->downloadXml($output, $table, $ignoreProject,$cFileName);
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
			// Get the form
			$form = $this->loadForm('com_joomleague.'.$this->name,$this->name, array('control' => 'jform', 'load_data' => $loadData));
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
