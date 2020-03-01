<?php
/*** Joomleague
 *
 * @copyright	Copyright (C) 2006-2015 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Language\Text;
jimport('joomla.filesystem.folder');
FormHelper::loadFieldClass('list');
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * FormFieldseasoncheckbox
 * 
 */
class JFormFieldseasoncheckbox extends FormField
{
	/**
	 * field type
	 * @var string
	 */
	public $type = 'checkboxes';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
	$app = Factory::getApplication();
        $option = Factory::getApplication()->input->getCmd('option');
        $select_id = Factory::getApplication()->input->getVar('id');
        $this->value = explode(",", $this->value);
        $targettable = $this->element['targettable'];
        $targetid = $this->element['targetid'];
        
        
  
    
        // Initialize variables.
		//$options = array();
    
    //$db = Factory::getDbo();
	$query = Factory::getDbo()->getQuery(true);
	// saisons selektieren
	$query->select('id AS value, name AS text');
	$query->from('#__joomleague_season');
	$query->order('name DESC');
            
        $starttime = microtime(); 
	Factory::getDbo()->setQuery($query);
            
            if ( COM_JOOMLEAGUE_SHOW_QUERY_DEBUG_INFO )
        {
            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
        $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(joomleagueModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
	$options = Factory::getDbo()->loadObjectList();
    
    // teilnehmende saisons selektieren
    if ( $select_id )
    {
    $query = Factory::getDbo()->getQuery(true);
			// saisons selektieren
			$query->select('season_id');
			$query->from('#__joomleague_'.$targettable);
			$query->where($targetid.'='.$select_id);
            $query->group('season_id');
            
            $starttime = microtime(); 
			Factory::getDbo()->setQuery($query);
            
            if ( COM_JOOMLEAGUE_SHOW_QUERY_DEBUG_INFO )
        {
            $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' <br><pre>'.print_r($query->dump(),true).'</pre>'),'Notice');
        $app->enqueueMessage(Text::_(__METHOD__.' '.__LINE__.' Ausfuehrungszeit query<br><pre>'.print_r(sportsmanagementModeldatabasetool::getQueryTime($starttime, microtime()),true).'</pre>'),'Notice');
        }
        
			$this->value = Factory::getDbo()->loadColumn();
    }
    else
    {
        $this->value = '';
    }
    
   


// Initialize variables.
            $html = array();
    
            // Initialize some field attributes.
            $class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '"' : ' class="checkboxes"';
    
            // Start the checkbox field output.
            $html[] = '<fieldset id="' . $this->id . '"' . $class . '>';
    
            // Get the field options.
            //$options = $options;
    
            // Build the checkbox field output.
            $html[] = '<ul>';
            foreach ($options as $i => $option)
            {
    
                // Initialize some option attributes.
                $checked = (in_array((string) $option->value, (array) $this->value) ? ' checked="checked"' : '');
                $class = !empty($option->class) ? ' class="' . $option->class . '"' : '';
                $disabled = !empty($option->disable) ? ' disabled="disabled"' : '';
    
                // Initialize some JavaScript option attributes.
                $onclick = !empty($option->onclick) ? ' onclick="' . $option->onclick . '"' : '';
    
                $html[] = '<li>';
                $html[] = '<input type="checkbox" id="' . $this->id . $i . '" name="' . $this->name . '[]"' . ' value="'
                    . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $onclick . $disabled . '/>';
    
                $html[] = '<label for="' . $this->id . $i . '"' . $class . '>' . Text::_($option->text) . '</label>';
                $html[] = '</li>';
            }
            $html[] = '</ul>';
    
            // End the checkbox field output.
            $html[] = '</fieldset>';
    
            return implode($html);    
    
    }
}
