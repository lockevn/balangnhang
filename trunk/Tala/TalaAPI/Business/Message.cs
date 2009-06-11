using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;
using System.Collections.Generic;
using TalaAPI.Exception;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Lib;

namespace TalaAPI.Business
{
    
    public class Message : APIDataEntry
    {
        int _id = 0;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int ID
        {
            get { return _id; }
            set { _id = value; }
        }

        string _code;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string Code
        {
            get { return _code.ToLower(); }
            set { _code = value; }
        }

        string _msg;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string Msg
        {
            get { return _msg.ToLower(); }
            set { _msg = value; }
        }

        public Message(string code, string msg)
        {
            _code= code;
            _msg = msg;
        }
    }    
}
