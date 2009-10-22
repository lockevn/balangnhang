<?php // $Id: XMLDBAction.class.php,v 1.4 2007/10/10 05:25:31 nicolasconnault Exp $

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This is the main action class. It implements all the basic
/// functionalities to be shared by each action.

class XMLDBAction {

    var $does_generate;  //Type of value returned by the invoke method
                         //ACTION_GENERATE_HTML have contents to show
                         //set by each specialized invoke

    var $title;          //Title of the Action (class name, by default)
                         //set by parent init automatically

    var $str;            //Strings used by the action
                         //set by each specialized init, calling loadStrings

    var $output;         //Output of the action
                         //set by each specialized invoke, get with getOutput

    var $errormsg;       //Last Error produced. Check when any invoke returns false
                         //get with getError

    var $postaction;     //Action to execute at the end of the invoke script

    /**
     * Constructor
     */
    function XMLDBAction() {
        $this->init();
    }

    /**
     * Constructor to keep PHP5 happy
     */
    function __construct() {
        $this->XMLDBAction();
    }

    /**
     * Init method, every subclass will have its own,
     * always calling the parent one
     */
    function init() {
        $this->does_generate = ACTION_NONE;
        $this->title     = strtolower(get_class($this));
        $this->str       = array();
        $this->output    = NULL;
        $this->errormsg  = NULL;
        $this->subaction = NULL;
    }

    /**
     * returns the type of output of the file
     */
    function getDoesGenerate() {
        return $this->does_generate;
    }

    /**
     * getError method, returns the last error string.
     * Used if the invoke() methods returns false
     */
    function getError() {
        return $this->errormsg;
    }

    /**
     * getOutput method, returns the output generated by the action.
     * Used after execution of the invoke() methods if they return true
     */
    function getOutput() {
        return $this->output;
    }

    /**
     * getPostAtion method, returns the action to launch after executing
     * another one
     */
    function getPostAction() {
        return $this->postaction;
    }

    /**
     * getTitle method returns the title of the action (that is part
     * of the $str array attribute
     */
    function getTitle() {
        return $this->str['title'];
    }

    /**
     * loadStrings method, loads the required strings specified in the
     * array parameter
     */
    function loadStrings($strings) {
    /// Load some commonly used strings
        $this->str['title'] = get_string($this->title, 'xmldb');

    /// Now process the $strings array loading it in the $str atribute
        if ($strings) {
            foreach ($strings as $key => $module) {
                $this->str[$key] = get_string($key, $module);
            }
        }
    }

    /**
     * main invoke method, it simply sets the postaction attribute
     * if possible
     */
    function invoke() {

        global $SESSION;

    /// If we are used any dir, save it in the lastused session object
    /// Some actions can use it to perform positioning
        if ($lastused = optional_param ('dir', NULL, PARAM_PATH)) {
            $SESSION->lastused = stripslashes_safe($lastused);
        }

        $this->postaction = optional_param ('postaction', NULL, PARAM_ALPHAEXT);
    /// Avoid being recursive
        if ($this->title == $this->postaction) {
            $this->postaction = NULL;
        }
    }

    /**
     * launch method, used to easily call invoke methods between actions
     */
    function launch($action) {

        global $CFG;

    /// Get the action path and invoke it
        $actionsroot = "$CFG->dirroot/$CFG->admin/xmldb/actions";
        $actionclass = $action . '.class.php';
        $actionpath = "$actionsroot/$action/$actionclass";

    /// Load and invoke the proper action
        $result = false;
        if (file_exists($actionpath) && is_readable($actionpath)) {
            require_once($actionpath);
            if ($xmldb_action = new $action) {
                $result = $xmldb_action->invoke();
                if ($result) {
                    if ($xmldb_action->does_generate != ACTION_NONE &&
                        $xmldb_action->getOutput()) {
                        $this->does_generate = $xmldb_action->does_generate;
                        $this->title = $xmldb_action->title;
                        $this->str = $xmldb_action->str;
                        $this->output .= $xmldb_action->getOutput();
                    }
                } else {
                    $this->errormsg = $xmldb_action->getError();
                }
            } else {
                $this->errormsg = "Error: cannot instantiate class (actions/$action/$actionclass)";
            }
        } else {
            $this->errormsg = "Error: wrong action specified ($action)";
        }
        return $result;
    }
}
?>
