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

namespace TalaAPI.XMLRenderOutput
{
    public class TalaPage : System.Web.UI.Page
    {
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
