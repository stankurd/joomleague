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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Updates Model
 *
 * @author	Kurt Norgaz
 */
class JoomleagueModelUpdates extends BaseDatabaseModel
{

	function loadUpdateFile($myfilename,$file)
	{
	    $app = Factory::getApplication();
	    
		include_once($myfilename);
	
		$data = array();
		$updateArray = array();
		$file_name = $file;
		
		if ($file=='jl_upgrade-0_93b_to_1_5.php'){
			return '';
		}
		
		$tableVersion = Table::getInstance('Version','Table');
		
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select(array('id','count'));
		$query->from('#__joomleague_version');
		$query->where('file = '.$db->Quote($file));
		$db->setQuery($query);
		try {
		$result = $db->loadObject();
		}
			
		catch (Exception $e)
		{
		
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				
		}
		
		
			$data['id']=$result->id;
			$data['count']=(int) $result->count + 1;
		
		$data['file']=$file_name;

		$query="SELECT * FROM #__joomleague_version where file='joomleague'";
		
		$db->setQuery($query);
		try {
			$result = $db->loadObject();
		}
			
		catch (Exception $e)
		{
		
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
		
		}
		
		
			$data['version']=!empty($version) ? $version : $result->version;
			$data['major']=!empty($major) ? $major : $result->major;
			$data['minor']=!empty($minor) ? $minor : $result->minor;
			$data['build']=!empty($build) ? $build : $result->build ;
			$data['revision']=!empty($revision) ? $revision : $result->revision;			
		
		try{
		$tableVersion->bind($data);
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			$this->setError(Text::_('Binding failed'));
			return false;
		}
		// Store the item to the database
		try{
		$store=$tableVersion->store();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		return '';
	}

	function getVersions()
	{
	    $app = Factory::getApplication();
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query='SELECT id, version, DATE_FORMAT(date,"%Y-%m-%d %H:%i") date FROM #__joomleague_version';
		$db->setQuery($query);
		try {
			$result = $db->loadObjectList();
		}
			
		catch (Exception $e)
		{
		
			$app->enqueueMessage(Text::_($e->getMessage()), 'error');
			return false;
		}
		return $result;
	}

	function _cmpDate($a,$b)
	{
		$ua=strtotime($a['updateFileDate']);
		$ub=strtotime($b['updateFileDate']);
		if ($ua==$ub){return 0;}
		return ($ua > $ub ? -1 : 1);
	}

	function _cmpName($a,$b)
	{
		return strcasecmp($a['file_name'],$b['file_name']);
	}

	function _cmpVersion($a,$b)
	{
		return strcasecmp($a['last_version'],$b['last_version']);
	}

	function loadUpdateFiles()
	{
		$app = Factory::getApplication();
		$option = $app->input->get('option');
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		//$updateFileList=Folder::files(JPATH_COMPONENT_ADMINISTRATOR.'/'.'assets'.'/'.'updates'.'/','.php$',false,true,array('',''));
		$updateFileList=Folder::files(JPATH_COMPONENT_ADMINISTRATOR.'/assets/updates/','.php$');
		// installer for extensions
		$extensions=Folder::folders(JPATH_COMPONENT_SITE.'/extensions');
		foreach ($extensions as $ext)
		{
			$path=JPATH_COMPONENT_SITE.'/extensions/'.$ext.'/admin/install';
			if (Folder::exists($path))
			{
				foreach (Folder::files($path,'.php$') as $file)
				{
					$updateFileList[]=$ext.'/'.$file;
				}
			}
		}
		$updateFiles=array();
		$i=0;
		foreach ($updateFileList AS $updateFile)
		{
			$path=explode('/',$updateFile);
			if (count($path) > 1)
			{
				$filepath=JPATH_COMPONENT_SITE.'/extensions/'.$path[0].'/admin/install/'.$path[1];
			}
			else
			{
				$filepath=JPATH_COMPONENT_ADMINISTRATOR.'/assets/updates/'.$path[0];
			}
			if ($fileContent=file_get_contents($filepath))
			{
				$version='';
				$updateDescription='';
				$lastVersion='';
				$updateDate='';
				$updateTime='';
				$pos=strpos($fileContent,'$version');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$version=trim(substr($dDummy,0,$pos4));
				}
				$pos=strpos($fileContent,'$updateDescription');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$updateDescription=trim(substr($dDummy,0,$pos4));
				}
				$pos=strpos($fileContent,'$lastVersion');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$lastVersion=trim(substr($dDummy,0,$pos4));
				}
				$pos=strpos($fileContent,'$updateFileDate');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$updateFileDate=trim(substr($dDummy,0,$pos4));
				}
				$pos=strpos($fileContent,'$updateFileTime');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$updateFileTime=trim(substr($dDummy,0,$pos4));
				}
				$pos=strpos($fileContent,'$excludeFile');
				if ($pos !== false)
				{
					$dDummy=substr($fileContent,$pos);
					$pos2=strpos($dDummy,'=');
					$dDummy=substr($dDummy,$pos2);
					$pos3=strpos($dDummy,"'");
					$dDummy=substr($dDummy,$pos3 + 1);
					$pos4=strpos($dDummy,"'");
					$excludeFile=trim(substr($dDummy,0,$pos4));
					if($excludeFile=='true') continue;
				}
				$updateFiles[$i]['id']=$i;
				$updateFiles[$i]['file_name']=$updateFile;
				$updateFiles[$i]['version']=$version;
				$updateFiles[$i]['last_version']=$lastVersion;
				$updateFiles[$i]['updateFileDate']=trim($updateFileDate);
				$updateFiles[$i]['updateFileTime']=$updateFileTime;
				$updateFiles[$i]['updateTime']='0000-00-00 00:00:00';
				$updateFiles[$i]['updateDescription']=$updateDescription;
				$updateFiles[$i]['date']='';
				$updateFiles[$i]['count']=0;
				$query="SELECT date,count FROM #__joomleague_version where file=".$db->Quote($updateFile);
				$db->setQuery($query);
				try{
				$result=$db->loadObject();
				}
				catch (Exception $e)
				{
					$app->enqueueMessage(Text::_($e->getMessage()), 'error');
				}
				
					$updateFiles[$i]['date']=$result->date;
					$updateFiles[$i]['count']=$result->count;
					
				$i++;
			}
		}
		$filter_order		= $app->getUserState($option.'updates_filter_order',		'filter_order',		'dates',	'cmd');
		$filter_order_Dir	= $app->getUserState($option.'updates_filter_order_Dir',	'filter_order_Dir',	'',			'word');
		$orderfn='_cmpDate';
		switch ($filter_order)
		{
			case 'name':
				$orderfn='_cmpName';
				break;

			case 'version':
				$orderfn='_cmpVersion';
				break;

			case 'date':
				$orderfn='_cmpDate';
				break;
		}
		usort($updateFiles,array($this,$orderfn));
		if (strcasecmp($filter_order_Dir,'ASC')==0){
			$updateFiles=array_reverse($updateFiles);
		}
		return $updateFiles;
	}
}
