<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;


/**
 * Treetomatch Model
 */
class JoomleagueModelTreetomatch extends JLGModelItem
{

	public $typeAlias = 'com_joomleague.treetomatch';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type 	The table type to instantiate
	 * @param	string 	A prefix for the table class name. Optional.
	 * @param	array 	Configuration array for model. Optional.
	 * @return Table database object
	 */
	public function getTable($type = 'Treetomatch',$prefix = 'Table',$config = array())
	{
		return Table::getInstance($type,$prefix,$config);
	}

	/**
	 * Method to get a single record.
	 *
	 * @param integer $pk	The id of the primary key.
	 *
	 * @return mixed Object on success, false on failure.
	 */
	public function getItemDisabled($pk = null)
	{
		if($item = parent::getItem($pk))
		{
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
		$form = $this->loadForm('com_joomleague.treetomatch','treetomatch',array('control' => 'jform','load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}

		/*
		 * $input = JFactory::getApplication()->input;
		 *
		 * if ($this->getState('treetomatch.id'))
		 * {
		 * $pk = $this->getState('treetomatch.id');
		 * $item = $this->getItem($pk);
		 * }
		 */

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
		$data = $app->getUserState('com_joomleague.edit.treetomatch.data',array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param array $data	The form data.
	 *
	 * @return boolean True on success.
	 */
	public function save($data)
	{
		$app = Factory::getApplication();
		$input = $app->input;

		if(parent::save($data))
		{
			$pk = (int) $this->getState($this->getName() . '.id');
			$item = $this->getItem($pk);

			return true;
		}

		return false;
	}
}
