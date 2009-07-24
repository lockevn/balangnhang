<?php
/**
 * Form that allows users to alter settings (all two of them) 
 * for an individual calendar week.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @author n.d.freear@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */
require_once $CFG->libdir.'/formslib.php';


class format_studycal_editweek_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('editweeksettings', 'format_studycal'), '');
        $mform->addElement('hidden', 'course');
        $mform->addElement('hidden', 'section');

        $mform->addElement('checkbox', 'hidenumber', '', get_string('hideweeknumber', 'format_studycal'));
        $mform->addElement('checkbox', 'hidedate', '', get_string('hidedate', 'format_studycal'));

        $resetgroup = array();
        $resetgroup[] =& $mform->createElement('checkbox', 'resetnumberon','', get_string('resetweeknumber', 'format_studycal'));
        $resetgroup[] =& $mform->createElement('text', 'resetnumber');
        $mform->setType('resetnumber', PARAM_INT);
        $mform->addGroup($resetgroup, 'availablefromgroup', '', null, false);

        $mform->addElement('text', 'title', get_string('weektitle','format_studycal'));
        $mform->addElement('submit', '', get_string('savechanges'));
    }
}

?>