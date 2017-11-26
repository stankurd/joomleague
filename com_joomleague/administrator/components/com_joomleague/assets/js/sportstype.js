/**
 * Joomleague
 *
 * @copyright	Copyright (C) 2005-2016 joomleague.at. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link		http://www.joomleague.at
 *
 * MODIFICATED BY Various Pro Solution Systems
 * Best Solutions: http://vpss.de
 */

Joomla.submitbutton = function(task) {
    var res = true;
    var validator = document.formvalidator;
    var form = jQuery('#adminForm');
    
    if (task == 'sportstype.cancel') {
        Joomla.submitform(task);
        return;
    }
    
    // do field validation
    if (validator.validate(form.name) === false) {
        alert(Joomla.JText._('COM_JOOMLEAGUE_ADMIN_SPORTSTYPE_CSJS_UNTRANSLATED_NAME'));
        form.name.focus();        
        res = false;
    }
    
    if (res) {
        Joomla.submitform(task);
    } else {
        return false;
    }        
}
