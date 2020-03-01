<?php


// Check to ensure this file is included in Joomla!
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Database\DatabaseDriver;

defined( '_JEXEC' ) or die( 'Restricted access' );

// import Joomla table library
jimport('joomla.database.table');
// Include library dependencies
jimport( 'joomla.filter.input' );


/**
 * joomleagueTablePredictionGroup
 * 
 * @package   
 * @author 
 * @copyright
 * @version 2013
 * @access public
 */
class TablePredictionGroups extends JLTable
{
	

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
    function __construct(DatabaseDriver $db)
	{
		parent::__construct( '#__joomleague_prediction_groups', 'id', $db );
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	public function check()
	{
		if ( trim( $this->name ) == '' )
		{
			$this->setError( Text::_( 'CHECK FAILED - Empty name of prediction game' ) );
			return false;
		}

		$alias = OutputFilter::stringURLSafe( $this->name );
		if ( empty( $this->alias ) || $this->alias === $alias )
		{
			$this->alias = $alias;
		}

		return true;
	}

}
?>