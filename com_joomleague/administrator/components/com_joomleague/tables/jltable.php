<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;
use Joomla\CMS\Access\Rules;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

// Include library dependencies
jimport('joomla.filter.input');

/**
 * JLTable Table class
 */
class JLTable extends Table 
{

	public function bind($array, $ignore = '') 
	{
		if (isset($array['extended']) && is_array($array['extended'])) {
			$registry = new Registry();
			$registry->loadArray($array['extended']);
			$array['extended'] = (string) $registry;
		}
		
		// Bind the rules
		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new Rules($array['rules']);
			$this->setRules($rules);
		}
		
		return parent::bind($array, $ignore);
	}
	

	/**
	 * try to insert first, update if fails
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	public function insertIgnore($updateNulls = false) {
		$k = $this->_tbl_key;

		$ret = $this->_insertIgnoreObject($this->_tbl, $this, $this->_tbl_key);
		if (!$ret) {
			$this->setError(get_class($this) . '::store failed - ' . $this->getDbo()->getErrorMsg());
			return false;
		}
		return true;
	}

	
	/**
	 * Inserts a row into a table based on an objects properties, ignore if already exists
	 *
	 * @access  public
	 * @param string  The name of the table
	 * @param object  An object whose properties match table fields
	 * @param string  The name of the primary key. If provided the object property is updated.
	 * @return int number of affected row
	 */
	public function _insertIgnoreObject($table, & $object, $keyName = NULL) {
		$fmtsql = 'INSERT IGNORE INTO ' . $this->getDbo()->quoteName($table) . ' ( %s ) VALUES ( %s ) ';
		$fields = array ();
		foreach (get_object_vars($object) as $k => $v) {
			if (is_array($v) or is_object($v) or $v === NULL) {
				continue;
			}
			if ($k[0] == '_') {
				// internal field
				continue;
			}
			$fields[] = $this->getDbo()->quoteName($k);
			$values[] = $this->getDbo()->isQuoted($k) ? $this->getDbo()->Quote($v) : (int) $v;
		}
		$this->getDbo()->setQuery(sprintf($fmtsql, implode(",", $fields), implode(",", $values)));
		if (!$this->getDbo()->query()) {
			return false;
		}
		$id = $this->getDbo()->insertid();
		if ($keyName && $id) {
			$object-> $keyName = $id;
		}
		return $this->getDbo()->getAffectedRows();
	}

	
	/**
	 * Method to determine if a row is checked out and therefore uneditable by
	 * a user. If the row is checked out by the same user, then it is considered
	 * not checked out -- as the user can still edit it.
	 *
	 * @param   integer  $with     The userid to preform the match with, if an item is checked
	 * out by this user the function will return false.
	 * @param   integer  $against  The userid to perform the match against when the function
	 * is used as a static function.
	 *
	 * @return  boolean  True if checked out.
	 *
	 * @link    http://docs.joomla.org/Table/isCheckedOut

	 * @todo    This either needs to be static or not.
	 */
	public static function _isCheckedOut($with = 0, $against = null)
	{
		// Handle the non-static case.
		if (isset($this) && ($this instanceof Table) && is_null($against))
		{
			$against = $this->get('checked_out');
		}
	
		// The item is not checked out or is checked out by the same user.
		if (!$against || ($against == $with))
		{
			return false;
		}
	
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$db->setQuery('SELECT COUNT(userid)' . 
						' FROM ' . $db->quoteName('#__session') . 
						' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $against);
		$checkedOut = (boolean) $db->loadResult();
	
		// If a session exists for the user then it is checked out.
		return $checkedOut;
	}
	
/**'
	 * Override Store function
	 * @see Table::store()
	 */
	public function store($updateNulls = false)
	{
	    $db = Factory::getDbo();
	    $query = $db->getQuery(true);
		$k = $this->_tbl_keys;

		// Implement JObservableInterface: Pre-processing by observers
		//$this->_observers->update('onBeforeStore', array($updateNulls, $k));

		$currentAssetId = 0;

		if (!empty($this->asset_id))
		{
			$currentAssetId = $this->asset_id;
		}

		// The asset id field is managed privately by this class.
		if ($this->_trackAssets)
		{
			unset($this->asset_id);
		}

		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey())
		{
			$result = $db->updateObject($this->_tbl, $this, $this->_tbl_keys, $updateNulls);
		}
		else
		{
			$result = $db->insertObject($this->_tbl, $this, $this->_tbl_keys[0]);
		}

		// If the table is not set to track assets return true.
		if ($this->_trackAssets)
		{
			if ($this->_locked)
			{
				$this->_unlock();
			}

			/*
			 * Asset Tracking
			 */
			$parentId = $this->_getAssetParentId();
			$name     = $this->_getAssetName();
			$title    = $this->_getAssetTitle();

			$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
			$asset->loadByName($name);

			// remove added "alias" column (by joomla core files)
			if ($asset->alias == null) {
				unset ($asset->alias);
			}
			
			// Re-inject the asset id.
			$this->asset_id = $asset->id;

			// Check for an error.
			$error = $asset->getError();

			if ($error)
			{
				$this->setError($error);

				return false;
			}
			else
			{
				// Specify how a new or moved node asset is inserted into the tree.
				if (empty($this->asset_id) || $asset->parent_id != $parentId)
				{
					$asset->setLocation($parentId, 'last-child');
				}

				// Prepare the asset to be stored.
				$asset->parent_id = $parentId;
				$asset->name      = $name;
				$asset->title     = $title;
				
				if ($this->_rules instanceof Rules)
				{
					$asset->rules = (string) $this->_rules;
				}

				if (!$asset->check() || !$asset->store($updateNulls))
				{
					$this->setError($asset->getError());

					return false;
				}
				else
				{
					// Create an asset_id or heal one that is corrupted.
					if (empty($this->asset_id) || ($currentAssetId != $this->asset_id && !empty($this->asset_id)))
					{
						// Update the asset_id field in this table.
						$this->asset_id = (int) $asset->id;

						$query = $db->getQuery(true)
							->update($db->quoteName($this->_tbl))
							->set('asset_id = ' . (int) $this->asset_id);
						$this->appendPrimaryKeys($query);
						$db->setQuery($query)->execute();
					}
				}
			}
		}

		// Implement JObservableInterface: Post-processing by observers
		//$this->_observers->update('onAfterStore', array(&$result));

		return $result;
	}
	
	
	
	/**
	 * We provide our global ACL as parent
	 * @see Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(Table $table = null, $id = null)
	{
		$asset = Table::getInstance('Asset');
		$asset->loadByName('com_joomleague');
		return $asset->id;
	}
}
