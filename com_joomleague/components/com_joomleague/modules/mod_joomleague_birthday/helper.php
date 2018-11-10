<?php
/**
 * Joomleague
 * @subpackage	Module-Birthday
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

    
    /**
     * Method to get the list
     *
     * @access public
     * @return array
     */
   
$db = Factory::getDbo();
$players = array();
$crew = array();
    
if(!function_exists('jl_birthday_sort'))
{
	// snippet taken from http://de3.php.net/manual/en/function.uasort.php
	function jl_birthday_sort ($array, $arguments = array(), $keys = true) {
		$code = "\$result=0;";
		foreach ($arguments as $argument) {
			$field = substr($argument, 2, strlen($argument));
			$type = $argument[0];
			$order = $argument[1];
			$code .= "if (!Is_Numeric(\$result) || \$result == 0) ";
			if (strtolower($type) == "n") $code .= $order == "-" ? "\$result = (intval(\$a['{$field}']) > intval(\$b['{$field}']) ? -1 : (intval(\$a['{$field}']) < intval(\$b['{$field}']) ? 1 : 0));" : "\$result = (intval(\$a['{$field}']) > intval(\$b['{$field}']) ? 1 : (intval(\$a['{$field}']) < intval(\$b['{$field}']) ? -1 : 0));";
			else $code .= $order == "-" ? "\$result = strcoll(\$a['{$field}'], \$b['{$field}']) * -1;" : "\$result = strcoll(\$a['{$field}'], \$b['{$field}']);";
		}
		$code .= "return \$result;";
		$compare = create_function('$a, $b', $code);
		if ($keys) uasort($array, $compare);
		else usort($array, $compare);
		return $array;
	}
}

$usedp = $params->get('project','0');
$p = (is_array($usedp)) ? implode(",", $usedp) : $usedp;
$usedteams = "";
// get favorite team(s), we have to make a function for this
if ($params->get('use_fav')==1)
{
    $db = Factory::getDbo();
    $query =$db->getQuery(true);
	$query='SELECT fav_team FROM #__joomleague_project';
	if ($p!='' && $p>0) $query.= ' WHERE id IN ('.$p.')';

	$db->setQuery($query);
	$temp=$db->loadColumn();

	if (count($temp)>0)
	{
		$usedteams=join(',', array_filter($temp));
	}
}
else
{
	$usedteams = $params->get('teams');
}

$birthdaytext='';

// get player info, we have to make a function for this
$dateformat = "DATE_FORMAT(p.birthday,'%Y-%m-%d') AS date_of_birth";

if ($params->get('use_which') <= 1)
{
    $query = $db->getQuery(true);
    $query
            ->select('p.id')
            ->select('p.birthday')
            ->select('p.firstname')
            ->select('p.nickname')
            ->select('p.lastname')
            ->select('p.picture AS default_picture')
            ->select('p.country')
            ->select('DATE_FORMAT(p.birthday, \'%m-%d\')AS daymonth')
            ->select('YEAR( CURRENT_DATE( ) ) as year')
            ->select('(YEAR( CURRENT_DATE( ) ) - YEAR( p.birthday ) + IF(DATE_FORMAT(CURDATE(), \'%m.%d\') > DATE_FORMAT(p.birthday, \'%m.%d\'), 1, 0)) AS age')
            ->select($dateformat)
            ->select('tp.picture')
            ->select('(TO_DAYS(DATE_ADD(p.birthday, INTERVAL (YEAR(CURDATE()) - YEAR(p.birthday) +
			IF(DATE_FORMAT(CURDATE(), \'%m.%d\') > DATE_FORMAT(p.birthday, \'%m.%d\'), 1, 0)) YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday')
            ->select('\'person\' AS func_to_call')
            ->select('\'\' project_id')
            ->select('\'\' team_id')
            ->select('\'pid\' AS id_to_append')
            ->select('1 AS type')
            ->select('pt.team_id')
            ->select('pt.project_id')
            ->from('#__joomleague_person p')
            ->innerJoin('#__joomleague_team_player tp ON tp.person_id = p.id')
            ->innerJoin('#__joomleague_project_team pt ON pt.id = tp.projectteam_id')
            ->where('p.published = 1')
            ->where('p.birthday != \'0000-00-00\'');
            if ($usedteams!=''){
	    $query->where('pt.team_id IN (' .$usedteams. ')');
            }
            if ($p!='' && $p>0){
                $query->where('pt.project_id IN ('.$p.')');
            }
            $query->group('p.id');

	$maxDays = $params->get('maxdays');
	if ((strlen($maxDays) > 0) && (intval($maxDays) >= 0))
	{
	    $query->having('days_to_birthday <= ' . intval($maxDays));
	}
	$query->order('days_to_birthday ASC');

	if ($params->get('limit') > 0) $query .= " LIMIT " . $params->get('limit');

	$db->setQuery($query);
	$players =$db->loadAssocList();
}

// get staff info, we have to make a function for this
if ($params->get('use_which') == 2 || $params->get('use_which') == 0)
{
    $query = $db->getQuery(true);
    $query
    ->select('p.id')
    ->select('p.birthday')
    ->select('p.firstname')
    ->select('p.nickname')
    ->select('p.lastname')
    ->select('p.picture AS default_picture')
    ->select('p.country')
    ->select('DATE_FORMAT(p.birthday, \'%m-%d\')AS daymonth')
    ->select('YEAR( CURRENT_DATE( ) ) as year')
    ->select('(YEAR( CURRENT_DATE( ) ) - YEAR( p.birthday ) + IF(DATE_FORMAT(CURDATE(), \'%m.%d\') > DATE_FORMAT(p.birthday, \'%m.%d\'), 1, 0)) AS age')
    ->select($dateformat)
    ->select('ts.picture')
    ->select('(TO_DAYS(DATE_ADD(p.birthday, INTERVAL (YEAR(CURDATE()) - YEAR(p.birthday) +
			IF(DATE_FORMAT(CURDATE(), \'%m.%d\') > DATE_FORMAT(p.birthday, \'%m.%d\'), 1, 0)) YEAR)) - TO_DAYS( CURDATE())+0) AS days_to_birthday')
			->select('\'staff\' AS func_to_call')
			->select('\'\' project_id')
			->select('\'\' team_id')
			->select('\'tsid\' AS id_to_append')
			->select('2 AS type')
			->select('pt.team_id')
			->select('pt.project_id')
			->from('#__joomleague_person p')
			->innerJoin('#__joomleague_team_staff ts ON ts.person_id = p.id')
			->innerJoin('#__joomleague_project_team pt ON pt.id = ts.projectteam_id')
			->where('p.published = 1')
			->where('p.birthday != \'0000-00-00\'');
	// Exclude players from the staff query to avoid duplicate persons (if a person is both player and staff)
	if(count($players) > 0)
	{
		$ids = "0";
		foreach ($players AS $player)
		{
			$ids .= "," . $player['id'];
		}
		$query->where('p.id NOT IN (' . $ids . ')');
	}

	if ($usedteams!=''){
	    $query->where('pt.team_id IN (' .$usedteams. ')');
	}
	if ($p!='' && $p>0){
	    $query->where('pt.project_id IN (' . $p . ')');
	}
	$query->group('p.id');
	//$query .= " GROUP BY p.id, ts.picture, pt.team_id, pt.project_id ";

	$maxDays = $params->get('maxdays');
	if ((strlen($maxDays) > 0) && (intval($maxDays) >= 0))
	{
		$query->having('days_to_birthday <= ' . intval($maxDays));
	}
	$query->order('days_to_birthday ASC');
	if ($params->get('limit') > 0) $query .= " LIMIT " . $params->get('limit');

	$db->setQuery($query);
	echo("<hr>".$db->getQuery($query));
	$crew =$db->loadAssocList();

}
    
?>