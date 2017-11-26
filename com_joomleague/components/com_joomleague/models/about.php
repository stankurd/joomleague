<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * About Model
 * 
 * @author	JoomLeague Team <www.joomleague.at>
 */

require_once JLG_PATH_SITE.'/models/project.php';

class JoomleagueModelAbout extends JoomleagueModelProject
{
	/**
	 * @var stdClass Detailed Joomleague information
	 */
	private $_about;

	/**
	 * Get the detailed Joomleague component information
	 *
	 * @return  stdClass  The information to be displayed to the user
	 */
	function getAbout()
	{
		if (empty($this->_about))
		{
			$about = new stdClass();

			//Translations Hosted by
			$about->translations = 'https://opentranslators.transifex.com/projects/p/joomleague/';
			//Repository Hosted by
			$about->repository = 'https://gitlab.com/joomleague/joomleague';
			//version
			$version = JoomleagueHelper::getVersion();
//			$revision = explode('.', $version);
			$about->version = $version;
			$about->author = '<a href="https://gitlab.com/joomleague/joomleague/graphs/master" target="_blank">Joomleague-Team</a>';
			$about->page = 'http://www.joomleague.at';
			$about->email = 'http://www.joomleague.at/forum/index.php?action=contact';
			$about->forum = 'http://forum.joomleague.at';
			$about->bugs = 'http://tracker.joomleague.at/projects/joomleague';
			$about->wiki = 'http://wiki.joomleague.at';
			$about->date = '2013-01-07';	// TODO: should we replace this hardcoded date by something else?
			$about->developer = '<a href="https://gitlab.com/joomleague/joomleague/graphs/master" target="_blank">Joomleague-Team</a>';
			$about->designer = 'Kasi, <a href="http://www.cg-design.net" target="_blank">cg design</a>&nbsp;(Carsten Grob) ';
			$about->icons = '<a href="http://www.hollandsevelden.nl/iconset/" target="_blank">Jersey Icons</a> (Hollandsevelden.nl)';
			$about->icons .= ', <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">Silk / Flags Icons</a> (Mark James)';
			$about->icons .= ', Panel images (Kasi)';
			$about->flash = 'Open Flash Chart 2.x';
			$about->graphic_library = '<a href="http://www.walterzorn.com" target="_blank">www.walterzorn.com</a>';
			$about->phpthumb = 'phpthumb.gxdlabs.com';

			$this->_about = $about;
		}

		return $this->_about;
	}
}
