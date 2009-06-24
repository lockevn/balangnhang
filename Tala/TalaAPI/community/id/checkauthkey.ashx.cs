using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Business;

namespace TalaAPI.community.id
{
    public class checkauthkey : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "CHECKAUTHKEY", "0");
            if (Song.Instance.DicValidAuthkey.ContainsKey(sAuthkey))
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "CHECKAUTHKEY", "1");
            }
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
