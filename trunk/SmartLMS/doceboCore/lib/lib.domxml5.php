<?php
/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * This package define classes for XML DOM compatibility with PHP < 5
 * In Docebo we use DOM API from PHP5 but you can use the platform also
 *	in PHP4. So in this package we redefine all the DOM API used in Docebo.
 * For do that we use domxml.
 * 
 * @package admin-library
 * @subpackage domxml
 * @version 	$Id: lib.domxml5.php 113 2006-03-08 18:08:42Z ema $
 * @author		Emanuele Sandri <emanuele (@) docebo (.) com>
 */

class DoceboDOMNode extends DOMNode {}
class DoceboDOMDocument extends DOMDocument {}
class DoceboDOMDocumentType extends DOMDocumentType {}
class DoceboDOMAttr extends DOMAttr {}
class DoceboDOMElement extends DOMElement {}
class DoceboDOMNodeList extends DOMNodeList {}
class DoceboDOMNamedNodeMap extends DOMNamedNodeMap {}
class DoceboDOMXPath extends DOMXPath {}
?>
