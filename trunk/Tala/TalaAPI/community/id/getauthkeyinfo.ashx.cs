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
    public class getauthkeyinfo : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            User AUuser = Song.Instance.GetUserByAuthkey(sAuthkey);
            Data.Add(AUuser);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
