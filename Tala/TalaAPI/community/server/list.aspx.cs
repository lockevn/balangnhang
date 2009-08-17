using System;
using System.Collections;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Xml.Linq;
using Quantum.Tala.Lib.XMLOutput;
using System.Text;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;
using GURUCORE.Lib.Net.Http;

namespace TalaAPI.community.server
{
    /// <summary>
    /// File này là Code file của trang aspx (ko được dịch cùng vào dll), cần copy kèm lên server cùng với aspx
    /// </summary>
    public partial class list : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Response.ContentType = "text/xml";

            string sWebRootPhysicalPath = Server.MapPath("/");
            XElement x = XElement.Load(sWebRootPhysicalPath + "Config/serverlist.config");
            foreach (XElement xServer in x.Elements("server"))
            {   
                // lấy user count của từng server
                string sCountUserOfServer = string.Empty;
                XElement xAddress = xServer.Element("address");
                string sURL = xAddress.Value + "/community/user/online_count.ashx";                
                try
                {
                    sCountUserOfServer = XHR.HTTPGetRequest(sURL);
                }
                catch (Exception ex)
                {
                    sCountUserOfServer = ex.Message;
                }
                sCountUserOfServer = string.IsNullOrEmpty(sCountUserOfServer) ? "0" : sCountUserOfServer;

                XElement xCapacity = xServer.Element("capacity");
                xCapacity.SetValue(string.Format(xCapacity.Value, sCountUserOfServer));                
                this.StringDirectUnderRoot += xServer.ToString(SaveOptions.DisableFormatting);
            }
                        
            this.Stat = APICommandStatusState.OK;            
        }
    }
}
