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
            
            string sUsername = Song.Instance.ValidAuthkey[sAuthkey];
            User AUuser = Song.Instance.OnlineUser[sUsername];            
            Data.Add(AUuser);            
            
            base.ProcessRequest(context);
        }
    }
}
