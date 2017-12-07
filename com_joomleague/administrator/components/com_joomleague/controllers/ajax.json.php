<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * Ajax Controller
 */
class JoomleagueControllerAjax extends JLGController
{

	public function __construct()
	{
		// Get the document object.
		$document = Factory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		parent::__construct();
	}

	public function projectdivisionsoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectDivisionsOptions($app->input->getInt('p'), $required));
		$app->close();
	}

	public function projecteventsoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectEventsOptions($app->input->getInt('p'), $required));
		$app->close();
	}

	public function projectteamsbydivisionoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectTeamsByDivisionOptions($app->input->getInt('p'), $app->input->getInt( 'division' ), $required));
		$app->close();
	}

	public function projectsbysportstypesoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectsBySportsTypesOptions($app->input->getInt('sportstype'), $required));
		$app->close();
	}

	public function projectsbycluboptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectsByClubOptions($app->input->getInt( 'cid' ), $required));
		$app->close();
	}

	public function projectteamsoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectTeamOptions($app->input->getInt('p'),$app->input->getInt('division'),$required));
		$app->close();
	}
	
	public function projectplayeroptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectPlayerOptions($app->input->getInt('p'),$app->input->getInt('division'),$required));
		$app->close();
	}

	public function projectstaffoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectStaffOptions($app->input->getInt('p'),$app->input->getInt('division'),$required));
		$app->close();
	}

	public function projectclubsoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectClubOptions($app->input->getInt('p'), $required));
		$app->close();
	}

	public function projectstatsoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getProjectStatOptions($app->input->getInt('p'), $required));
		$app->close();
	}

	public function matchesoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getMatchesOptions($app->input->getInt('p'),$app->input->getInt('division'), $required));
		$app->close();
	}

	public function refereesoptions()
	{
	    $app = Factory::getApplication();
		$model = $this->getModel('ajax');
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) $model->getRefereesOptions($app->input->getInt('p'), $required));
		$app->close();
	}

	public function roundsoptions()
	{
	    $app = Factory::getApplication();
		$req = $app->input->get('required', false);
		$required = ($req == 'true' || $req == '1') ? true : false;
		echo json_encode((array) JoomleagueHelper::getRoundsOptions($app->input->getInt('p'),'ASC', $required));
		$app->close();
	}

	public function projecttreenodeoptions()
	{
	}
	
	public function sportstypesoptions()
	{
		echo json_encode((array) JoomleagueModelSportsTypes::getSportsTypes());
	}


}
