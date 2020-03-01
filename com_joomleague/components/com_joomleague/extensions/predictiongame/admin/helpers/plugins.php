<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 * 
 * @author		Wolfgang Pinitsch <andone@mfga.at>
 */

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

defined('_JEXEC') or die;

//load com_joomleague_sport_types.ini
$extension 	= "com_joomleague_sport_types";
$lang 		= Factory::getLanguage();
$source 	= JPATH_ADMINISTRATOR . '/components/' . $extension;
$lang->load("$extension", JPATH_ADMINISTRATOR, null, false, false)
||	$lang->load($extension, $source, null, false, false)
||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
||	$lang->load($extension, $source, $lang->getDefault(), false, false);

PluginHelper::importPlugin('extension', 'joomleague_esport');
