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
using GURUCORE.Lib.Core.Security.Cryptography;
using TalaAPI.Business;

namespace TalaAPI.community.id
{
    public class login : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sUsername = context.Request["username"].ToStringSafetyNormalize();
            string sPassword = context.Request["password"].ToStringSafety();

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LOGIN", "wrong username and/or password");

            User user = Song.Instance.LoginVaoSongChoi(sUsername, sPassword);
            if (user == null)
            {
            }
            else
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "LOGIN", user.Authkey);
            }            
            this.Cmd.Add(cs);            
            base.ProcessRequest(context);
        }
    }
}
