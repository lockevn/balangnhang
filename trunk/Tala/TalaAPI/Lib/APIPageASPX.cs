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

namespace TalaAPI.Lib
{
    public class APIPageASPX : System.Web.UI.Page
    {
        public string StringDirectUnderRoot { get; set; }
        public string Stat { get; set; }

        protected virtual void p(string s)
        {
            Response.Write(s);
        }
        protected virtual void pln(string s)
        {
            Response.Write(s + "<br />");
        }
    }
}
