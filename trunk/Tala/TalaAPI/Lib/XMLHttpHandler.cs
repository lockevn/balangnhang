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
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using System.Collections;
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.Lib
{    
    //[WebService(Namespace = "http://api.tala.quantumme.com/")]
    //[WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
    public class XMLHttpHandler : IHttpHandler
    {
        protected internal string Stat = "fail";
        protected internal ArrayList Data = new ArrayList();
        protected internal ArrayList Cmd = new ArrayList();

        /// <summary>
        /// Set ContentType to xml, render Data and Cmd to xml
        /// </summary>
        /// <param name="context"></param>
        public virtual void ProcessRequest(HttpContext context)
        {            
            context.Response.ContentType = "text/xml";
            string sRenderedData = string.Empty;
            if(Data.Count > 0)
            {
                foreach (APIDataEntry data in Data)
                {
                    if (data != null)
                    {
                        sRenderedData += data.ToXMLString();
                    }
                }
                sRenderedData = "<data>" + sRenderedData + "</data>";
            }

            string sRenderedCmd = string.Empty;
            if(Cmd.Count > 0)
            {
                foreach (APICommandStatus cs in Cmd)
                {
                    sRenderedCmd += cs.ToString();
                    Stat = cs.Stat;
                }
                sRenderedCmd = "<cmd>" + sRenderedCmd + "</cmd>";
            }

            context.Response.Write(string.Format("<q stat='{0}'>{1}{2}</q>", Stat, sRenderedData, sRenderedCmd));
        }


        public bool IsReusable
        {
            get
            {
                return false;
            }
        }
    }
}
