using System;
using System.Collections;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Xml.Linq;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.server
{
    public partial class list : System.Web.UI.Page
    {
        public string StringDirectUnderRoot { get; set; }
        public string Stat { get; set; }


        protected void Page_Load(object sender, EventArgs e)
        {
            Response.ContentType = "text/xml";

            string sWebRootPhysicalPath = Server.MapPath("/");
            XElement x = XElement.Load(sWebRootPhysicalPath + "Config/serverlist.config");
            foreach (XElement xServer in x.Elements("server"))
            {
                this.StringDirectUnderRoot += xServer.ToString(SaveOptions.DisableFormatting);
            }

            this.Stat = APICommandStatusState.OK;            
        }
    }
}
