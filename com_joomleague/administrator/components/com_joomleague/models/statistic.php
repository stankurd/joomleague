<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

/**
 * Statistic Model
 */
class JoomleagueModelStatistic extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.statistic';

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param object $record	A record object.
	 *
	 * @return boolean True 	if allowed to delete the record.
	 * Defaults to the	permission for the component.
	 */
	protected function canDelete($record)
	{
		if(!empty($record->id))
		{
			$user = Factory::getUser();
	
			if($user->authorise('core.admin','com_joomleague')
					|| $user->authorise('core.delete','com_joomleague')
					|| $user->authorise('core.delete','com_joomleague.statistic.'.$id))
			{
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	/**
	 * Method to remove a statistic
	 *
	 * @access public
	 * @return boolean on success
	 */
	function delete(&$pks = array())
	{
		$return = array();
		if($pks)
		{
			$pksTodelete = array();
			$errorNotice = array();
			$db = Factory::getDbo();
			foreach($pks as $pk)
			{
				$result = array();
				
				// first check that it not used in any match events
				$query = $db->getQuery(true);
				$query->select('ms.id');
				$query->from('#__joomleague_match_statistic AS ms');
				$query->where('ms.statistic_id = '.$pk);
				
				$db->setQuery($query);
				$db->execute();
				if($db->loadObjectList())
				{
					$result[] = JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_CANT_DELETE_STATS_MATCHES');
				}

				
				// then check that it is not assigned to positions
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__joomleague_position_statistic');
				$query->where('statistic_id = '.$pk);
				$db->setQuery($query);
				$db->execute();
				if($db->loadObjectList())
				{
					$result[] = JText::_('COM_JOOMLEAGUE_ADMIN_STATISTIC_MODEL_CANT_DELETE_STATS_MATCHES');
				}
				
				if($result)
				{
					$pkInfo = array("id:".$pk);
					$result = array_merge($pkInfo,$result);
					$errorNotice[] = $result;
				}
				else
				{
					$pksTodelete[] = $pk;
				}
			}
			
			if($pksTodelete)
			{
				$return['removed'] = parent::delete($pksTodelete);
				$return['removedCount'] = count($pksTodelete);
			}
			else
			{
				$return['removed'] = false;
				$return['removedCount'] = false;
			}
			
			if($errorNotice)
			{
				$return['error'] = $errorNotice;
			}
			else
			{
				$return['error'] = false;
			}
			
			return $return;
		}
		
		$return['removed'] = false;
		$return['error'] = false;
		$return['removedCount'] = false;
		
		return $return;
	}


	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type 	The table type to instantiate
	 * @param	string 	A prefix for the table class name. Optional.
	 * @param	array 	Configuration array for model. Optional.
	 * @return JTable database object
	 */
	public function getTable($type = 'Statistic',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}


	/**
	 * overrides to load params and classparams
	 * @see AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName().'.id');
		$table = $this->getTable();

		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $table->load($pk);

			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Convert to the JObject before adding other data.
		$properties = $table->getProperties(1);
		$item = ArrayHelper::toObject($properties, 'JObject');

		if ($item) {
			// Convert the params field to an array.
			$registry = new Registry;
			$registry->loadString($item->baseparams);
			$item->baseparams = $registry->toArray();

			$registry = new Registry;
			$registry->loadString($item->params);
			$item->params = $registry->toArray();
		}
		return $item;
	}


	/**
	 * Method to get the record form.
	 *
	 * @param array $data		the form.
	 * @param boolean $loadData	the form is to load its own data (default case), false if not.
	 * @return mixed JForm object on success, false on failure
	 */
	public function getForm($data = array(),$loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.statistic','statistic',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}

		return $form;
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return mixed data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app = Factory::getApplication();
		$data = $app->getUserState('com_joomleague.edit.statistic.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	/**
	 * loadForm
	 */
	protected function loadForm($name,$source = null,$options = array(),$clear = false,$xpath = false)
	{
		$app 	= Factory::getApplication();
		$input = $app->input;
		
		$item = $this->loadFormData();
		
		// Handle the optional arguments.
		$options['control'] = ArrayHelper::getValue($options,'control',false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if(isset($this->_forms[$hash]) && ! $clear)
		{
			return $this->_forms[$hash];
		}

		// Get the form.
		Form::addFormPath(JPATH_COMPONENT.'/models/forms');
		Form::addFieldPath(JPATH_COMPONENT.'/models/fields');

		try
		{
			$form = Form::getInstance($name,$source,$options,false,$xpath);
			// load base configuration xml for stats
			$form->loadFile(JLG_PATH_ADMIN.'/statistics/base.xml');

			// specific xml configuration depends on stat type
			$item = $this->loadFormData();
			if($item && $item->class)
			{
				require_once JPATH_COMPONENT_ADMINISTRATOR.'/statistics/base.php';
				$class = JLGStatistic::getInstance($item->class);
				$xmlpath = $class->getXmlPath();
				$form->loadFile($xmlpath);
			}

			if(isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.

			$this->preprocessForm($form,$data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		}
		catch(Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 */
	protected function prepareTable($table)
	{
		$date = Factory::getDate();
		$user = Factory::getUser();
	
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true)
				->select('MAX(ordering)')
				->from('#__joomleague_statistic');
	
				$db->setQuery($query);
				$max = $db->loadResult();
	
				$table->ordering = $max + 1;
			}
		}
		else
		{
			// Set the values
			$table->modified    = $date->toSql();
			$table->modified_by = $user->get('id');
		}
	}
	
	
	/**
	 * Method to save the form data.
	 *
	 * @param array $data The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;
		
		$data['calculated'] = $input->get('calculated',0);
		
		$post = $input->post->getArray();
		if (isset($post['jform']['params'])) {
			$data['params'] = $post['jform']['params'];
		}
		
		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName().'.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}


	/**
	 * Method to return the query that will obtain all ordering versus
	 * statistics
	 * It can be used to fill a list box with value/text data.
	 *
	 * @access public
	 * @return string
	 */
	function getOrderingAndStatisticQuery()
	{
		return 'SELECT ordering AS value,name AS text FROM #__joomleague_statistic ORDER BY ordering';
	}
}
