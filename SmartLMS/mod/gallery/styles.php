/** $Id: styles.php,v 1.2 2007/01/05 03:17:40 mark-nielsen Exp $
 *
 * Gallery Module CSS style sheet.
 *
 * @author Mark Nielsen
 * @version $Id: styles.php,v 1.2 2007/01/05 03:17:40 mark-nielsen Exp $
 * @copyright http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package gallery
 **/

/*.mod-gallery .gbBlock {
    display:list;
}*/

/* This is the gallery header (Picture only I think) */
.mod-gallery #gsHeader {
    display: none;
}

/* Breadcrumb trail */
.mod-gallery #gsNavBar div.gbBreadCrumb {
    display: none;
}

/* The system links ("Site Admin", "Your Album") should no longer float as the 
   breadcrumb has been removed. Just align right */
.mod-gallery #gsNavBar div.gbSystemLinks {
  text-align: right;
  float: none;
}

/* First link in the breadcrumb trail */
.mod-gallery .BreadCrumb-1 {
    /*display: none;*/
}

/* The side bar */
.mod-gallery #gsSidebar {
    /*display: none;*/
}

/* the search block */
.mod-gallery .block-search-SearchBlock {
    /*display: none;*/
}

/* Footer (pictures only I think) */
.mod-gallery #gsFooter {
    display: none;
}

/* Tells you who you are logged in as */
.mod-gallery .block-core-GuestPreview {
    display: none;
}

/* navigation block */
.mod-gallery .block-core-NavigationLinks {
    /*display: none;*/
}

/* don't show other galleries to the students */
.mod-gallery .block-core-PeerList {
    /*display: none;*/
}
