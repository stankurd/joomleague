<?php
// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

class JoomleagueHelpersStyle
{
	public static function load()
	{
		$document = Factory::getDocument();
		
		//stylesheets
		$document->addStylesheet(Uri::root().'administrator/components/com_joomleague/assets/css/joomleague.css');

		//javascripts
		$document->addScript(URI::root().'administrator/components/com_joomleague/assets/js/joomleague.js');

	}
}