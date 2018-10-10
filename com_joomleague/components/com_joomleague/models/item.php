<?php
/**
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Model-Item
 */
if(!class_exists('JoomleagueModelItem')) {
class JoomleagueModelItem extends AdminModel
{
	/**
	 * item id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * Project data
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$app = Factory::getApplication();
		$array = $app->input->get( 'cid', array(0), '', 'array' );
		$edit = $app->input->get( 'edit', true );
		if( $edit )
		{
			$this->setId( (int)$array[0] );
		}
	}

	/**
	 * Method to set the item identifier
	 *
	 * @access	public
	 * @param	int item identifier
	 */
	function setId( $id )
	{
		// Set item id and wipe data
		$this->_id	  = $id;
		$this->_data	= null;
	}

	/**
	 * Method to get an item
	 */
	function &getData()
	{
		// Load the item data
		if ( !$this->_loadData() )
		{
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
	function isCheckedOut( $uid = 0 )
	{
		if ( $this->_loadData() )
		{
			if ( $uid )
			{
				return ( $this->_data->checked_out && $this->_data->checked_out != $uid );
			}
			else
			{
				return $this->_data->checked_out;
			}
		}
	}

	/**
	 * Method to store the item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store( $data, $table = '' )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		if ( $table == '')
		{
			$row = $this->getTable();
		}
		else
		{
			$row = Table::getInstance( $table, 'Table' );
		}

		// Bind the form fields to the items table
		if ( !$row->bind( $data ) )
		{
			$this->setError( Text::_('Binding failed') );
			return false;
		}

		// Create the timestamp for the date
		$row->checked_out_time = gmdate( 'Y-m-d H:i:s' );

		// if new item, order last, but only if an ordering exist
		if ( ( isset( $row->id ) ) && ( isset( $row->ordering ) ) )
		{
			if ( !$row->id && $row->ordering != NULL )
			{
				$row->ordering = $row->getNextOrder();
			}
		}

		// Make sure the item is valid
		if ( !$row->check() )
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
		if ( !$row->store() )
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

		return true;
	}

	/**
	 * Method to move an item
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function move( $direction )
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$row = $this->getTable();
		if ( !$row->load( $this->_id ) )
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
