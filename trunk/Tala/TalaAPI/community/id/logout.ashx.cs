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
    public class logout : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            string sUsername = Song.Instance.ValidAuthkey[sAuthkey];
            try
            {
                Song.Instance.ValidAuthkey.Remove(sAuthkey);
                Song.Instance.OnlineUser.Remove(sUsername);
            }
            catch { }

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "LOGOUT", "authkey=" + sAuthkey + "u=" + sUsername);
            this.Cmd.Add(cs);

            base.ProcessRequest(context);
        }
    }
}
