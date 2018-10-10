<?php
/**
DISTINCT( * Joomleague
*
* @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
* @license		GNU General Public License version 2 or later; see LICENSE.txt
* @link			http://www.joomleague.at
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR . '/components/com_joomleague/models/projectteam.php';

/**
 * Projectteam-Form model
 */
class JoomleagueModelProjectteamform extends JoomleagueModelProjectteam
{
	/**
	 * Model typeAlias string. Used for version history.
	 *
	 * @var        string
	 */
	public $typeAlias = 'com_joomleague.projectteam';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

		// Load state from the request.
		$pk = $app->input->getInt('a_id');
		$this->setState('projectteam.id', $pk);

		$return = $app->input->get('return', null, 'base64');
		$this->setState('return_page', base64_decode($return));

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$this->setState('layout', $app->input->getString('layout'));
	}

	/**
	 * Method to get article data.
	 *
	 * @param   integer  $itemId  The id of the article.
	 *
	 * @return  mixed  Content item data object on success, false on failure.
	 */
	public function getItem($itemId = null)
	{
		$itemId = (int) (!empty($itemId)) ? $itemId : $this->getState('projectteam.id');

		// Get a row instance.
		$table = $this->getTable();

		// Attempt to load the row.
		$return = $table->load($itemId);

		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());

			return false;
		}

		$properties = $table->getProperties(1);
		$value = ArrayHelper::toObject($properties, 'CMSObject');

		// Convert attrib field to Registry.
		$value->params = new Registry;
		// $value->params->loadString($value->attribs);

		// Compute selected asset permissions.
		$user   = Factory::getUser();
		$userId = $user->get('id');
		$asset  = 'com_joomleague.project_team.' . $value->id;

		// Check general edit permission first.
		if ($user->authorise('core.edit', $asset))
		{
			$value->params->set('access-edit', true);
		}

		// Check edit state permission.
		if ($itemId)
		{
			// Existing item
			$value->params->set('access-change', $user->authorise('core.edit.state', $asset));
		}
		else
		{
			// New item.
			$value->params->set('access-change', $user->authorise('core.edit.state', 'com_joomleague'));
		}
		
		if($itemId)
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array(
					't.name AS name'
			));
			$query->from('#__joomleague_team AS t');
		
			$query->join('LEFT','#__joomleague_project_team AS pt ON pt.team_id = t.id');
			$query->where('pt.id = ' . $db->quote($itemId));
		
			$db->setQuery($query);
			$db->execute();
			$result = $db->loadResult();
		
			if(empty($result))
			{
				$value->name = '(projectteam: ' . $itemId . ')';
			}
			else
			{
				$value->name = $result;
			}
		}
		
		

		return $value;
	}

	/**
	 * Get the return URL.
	 *
	 * @return  string	The return URL
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 */
	public function save($data)
	{
		return parent::save($data);
	}
}
