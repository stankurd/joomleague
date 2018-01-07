<?php
/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Import Controller
 */
class JoomleagueControllerImport extends JoomleagueController 
{
	
	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct ();
		
		// Register Extra tasks
		$this->registerTask ( 'csvpersonimport', 'dispatch' );
		$this->registerTask ( 'csvseasonimport', 'dispatch' );
		$this->registerTask ( 'csvleagueimport', 'dispatch' );
		$this->registerTask ( 'csvprojectimport', 'dispatch' );
		$this->registerTask ( 'csvclubimport', 'dispatch' );
		$this->registerTask ( 'csvteamimport', 'dispatch' );
		$this->registerTask ( 'csvsports_typeimport', 'dispatch' );
		$this->registerTask ( 'csveventtypeimport', 'dispatch' );
		$this->registerTask ( 'csvpositionimport', 'dispatch' );
		$this->registerTask ( 'csvplaygroundimport', 'dispatch' );
	}
	public function dispatch() {
		switch ($this->getTask ()) {
			case 'csvseasonimport' :
				{
					$table = "Season";
					$view = "seasons";
				}
				break;
			case 'csvpersonimport' :
				{
					$table = "Person";
					$view = "persons";
				}
				break;
			case 'csvleagueimport' :
				{
					$table = "League";
					$view = "leagues";
				}
				break;
			case 'csvprojectimport' :
				{
					$table = "Project";
					$view = "projects";
				}
				break;
			case 'csvclubimport' :
				{
					$table = "Club";
					$view = "clubs";
				}
				break;
			case 'csvteamimport' :
				{
					$table = "Team";
					$view = "teams";
				}
				break;
			case 'csvsports_typeimport' :
				{
					$table = "SportsType";
					$view = "sportstypes";
				}
				break;
			case 'csveventtypeimport' :
				{
					$table = "Eventtype";
					$view = "eventtypes";
				}
				break;
			case 'csvpositionimport' :
				{
					$table = "Position";
					$view = "positions";
				}
				break;
			case 'csvplaygroundimport' :
				{
					$table = "Playground";
					$view = "playgrounds";
				}
				break;
			
			default :
				$msg = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_NOT_EXIST' );
				$this->setRedirect ( 'index.php?option='.$this->option.'&view=projects', $msg, 'error' );
				return;
		}
		$this->import ( $table, $view );
	}
	
	public function import($table, $view) {
		JLToolBarHelper::back ( 'Back', 'index.php?option='.$this->option.'&view=' . $view );
		
		$msg = array ();
		$input = $this->input;
		$replace = $input->post->getInt('replace', 0);
		$delimiter = $input->post->get('csvdelimiter', ",");
		$tblObject = Table::getInstance ( $table, 'Table' );
		$filename = '';
		$csvimport = false;
		
		$file = $input->files->get('FileCSV', null, 'array');
		if (isset ( $file ['tmp_name'] ) && trim ( $file ['tmp_name'] ) != '') {
			$filename = $file ['tmp_name'];
			$csvimport = true;
		}
		
		if ($csvimport) {
			$handle = fopen ( $filename, 'r' );
			
			if (! $handle) {
				$msg = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_CANNOT_OPEN' );
				$this->setRedirect ( 'index.php?option='.$this->option.'&view=' . $view, $msg, 'error' );
				return;
			}
			
			// get fields, on first row of the file
			$fields = array ();
			if (($data = fgetcsv ( $handle, 1000, $delimiter, '"' )) !== FALSE) {
				$numfields = count ( $data );
				for($c = 0; $c < $numfields; $c ++) {
					// here, we make sure that the field match one of the fields of table or special fields,
					// otherwise, we don't add it
					$value = JoomleagueHelper::removeBOM(trim ( $data [$c] ) );
					if (property_exists ( $tblObject, $value )) {
						$fields [$c] = $value;
					}
				}
			}
			
			// If there is no validated fields, there is a problem...
			if (! count ( $fields )) {
				$msg = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_ERROR_PARSING' );
				$this->setRedirect ( 'index.php?option='.$this->option.'&view=' . $view, $msg, 'error' );
				return;
			} else {
				$msg [] = $numfields . " fields found in first row";
				$msg [] = count ( $fields ) . " fields were kept";
			}
			
			// Now get the records, meaning the rest of the rows.
			$records = array ();
			$row = 1;
			while ( ($data = fgetcsv ( $handle, 10000, $delimiter, '"' )) !== FALSE ) {
				$num = count ( $data );
				if ($numfields != $num) {
					$msg [] = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_WRONG_NUMBER_OF_FIELDS' );
				} else {
					$r = array ();
					// only extract columns with validated header, from previous step.
					foreach ( $fields as $k => $v ) {
						$r [$k] = $this->_formatcsvfield ( $v, $data [$k] );
					}
					$records [] = $r;
				}
				$row ++;
			}
			fclose ( $handle );
			
			$msg [] = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_RECORDS_FOUND' ) . count ( $records );
			
			// database update
			if (count ( $records )) {
				$model = $this->getModel ( 'import' );
				$result = $model->import ( $fields, $records, $replace, $table );
				$msg [] = $result ['errormsg'];
				$msg [] = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_ADDED_RECORDS' ) . ' ' . $result ['added'];
				$msg [] = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_UPDATED_RECORDS' ) . ' ' . $result ['updated'];
				$msg [] = Text::_ ( 'COM_JOOMLEAGUE_ADMIN_IMPORT_CTRL_TOTAL_EXISTS_RECORDS' ) . ' ' . $result ['exists'];
			}
			$this->setRedirect ( 'index.php?option='.$this->option.'&view=' . $view, implode ( '<p>', $msg ) );
		}
	}
	
	/**
	 * handle specific fields conversion if needed
	 *
	 * @param
	 *        	string column name
	 * @param string $value        	
	 * @return string
	 */
	private function _formatcsvfield($type, $value) {
		switch ($type) {
			// here we should check some consistency...
			case 'birthday' :
				if ($value != '') {
					// strtotime does a good job in converting various date formats...
					$date = strtotime ( $value );
					$field = strftime ( '%Y-%m-%d', $date );
				} else {
					$field = null;
				}
				break;
			default :
				$field = $value;
				break;
		}
		return $field;
	}
}
