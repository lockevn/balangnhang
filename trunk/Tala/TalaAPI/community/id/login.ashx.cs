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
            if (sUsername.IsNullOrEmpty() || sPassword.IsNullOrEmpty() )
            {
            }
            else
            {
                CryptoUtil cu = new CryptoUtil();
                sPassword = cu.MD5Hash(sPassword);
                User user = DBUtil.GetUserByUsernameAndPassword(sUsername, sPassword);
                if (user != null && user.Username == sUsername)
                {                    
                    // if found user with username and password
                    // generate new authkey
                    
                    user.Authkey = TextUtil.GetRandomGUID();
                    cs = new APICommandStatus(APICommandStatusState.OK, "LOGIN", "authkey=" + user.Authkey);
                     if (Song.Instance.OnlineUser.ContainsKey(user.Username) == false)
                     {
                         // if this is first time login, add to OnlineUser
                         Song.Instance.OnlineUser.Add(user.Username, user);
                     }
                     else
                     {
                         // if login again, change the authkey, and replace in
                         Song.Instance.OnlineUser[user.Username] = user;
                     }
                }
            }
            this.Cmd.Add(cs);
            
            base.ProcessRequest(context);
        }
    }
}
