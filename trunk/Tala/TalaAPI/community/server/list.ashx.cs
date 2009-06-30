using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using System.Xml.Linq;
using log4net;

namespace TalaAPI.community.server
{
    public class list : XMLHttpHandler
    {
        private static readonly ILog log = LogManager.GetLogger(typeof(list));

        public override void ProcessRequest(HttpContext context)
        {            
            string sWebRootPhysicalPath = context.ApplicationInstance.Server.MapPath("/");
            XElement x = XElement.Load(sWebRootPhysicalPath + "serverlist.config");
            foreach(XElement xServer in x.Elements("server"))
            {
                this.StringDirectUnderRoot += xServer.ToString(SaveOptions.DisableFormatting);
            }

            base.Stat = APICommandStatusState.OK;
            base.ProcessRequest(context);
        }
    }
}
