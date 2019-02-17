<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

if (! defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

if ( !defined('JLG_PATH') )
{
DEFINE( 'JLG_PATH','components/com_joomleague' );
}

// pr�ft vor Benutzung ob die gew�nschte Klasse definiert ist
if ( !class_exists('sportsmanagementHelper') ) 
{
//add the classes for handling
$classpath = JPATH_ADMINISTRATOR.DS.JLG_PATH.DS.'helpers'.DS.'joomleague.php';
JLoader::register('joomleagueHelper', $classpath);
BaseDatabaseModel::getInstance("joomleagueHelper", "JoomleagueModel");
}

/**
 * JFormFieldPredictiongame
 * 
 * @package   
 * @author 
 * @copyright
 * @version 2014
 * @access public
 */
class JFormFieldPredictiongame extends FormField
{

	protected $type = 'predictiongame';
	
	/**
	 * JFormFieldPredictiongame::getInput()
	 * 
	 * @return
	 */
	function getInput() 
    {
		$db = Factory::getDbo();
		$lang = Factory::getLanguage();
        $params = ComponentHelper::getParams( 'com_joomleague' );
        
//		$extension = "com_sportsmanagement";
// 		$source = JPATH_ADMINISTRATOR . '/components/' . $extension;
// 		$lang->load("$extension", JPATH_ADMINISTRATOR, null, false, false)
// 		||	$lang->load($extension, $source, null, false, false)
// 		||	$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
// 		||	$lang->load($extension, $source, $lang->getDefault(), false, false);
		
//		$query = 'SELECT pg.id, pg.name FROM #__sportsmanagement_prediction_game pg WHERE pg.published=1 ORDER BY pg.name';
		$query = $db->getQuery(true);
			
            $query->select('CONCAT_WS( \':\', pg.id, pg.name ) AS id');
			//$query->select('pg.id');
            $query->select('pg.name');

            $query->from('#__joomleague_prediction_game pg');    
            
			$query->where('pg.published = 1');
			$query->order('pg.name');
            
//            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' ' .  ' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
//            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' ' .  ' <br><pre>'.print_r(COM_SPORTSMANAGEMENT_TABLE,true).'</pre>'),'Notice');
            
			$db->setQuery($query);
			$options = $db->loadObjectList();
        
        
        //$db->setQuery( $query );
//		$clubs = $db->loadObjectList();
		$mitems = array(HTMLHelper::_('select.option', '', Text::_('COM_SPORTSMANAGEMENT_GLOBAL_SELECT')));

		foreach ( $options as $option ) {
			$mitems[] = HTMLHelper::_('select.option',  $option->id, '&nbsp;'.$option->name. ' ('.$option->id.')' );
		}
		
		$output = HTMLHelper::_('select.genericlist',  $mitems, $this->name, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $this->value, $this->id );
		return $output;
	}
}
 