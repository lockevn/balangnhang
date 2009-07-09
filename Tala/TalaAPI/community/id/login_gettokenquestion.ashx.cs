using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using GURUCORE.Lib.Core.Security.Cryptography;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Authentication;

namespace TalaAPI.community.id
{    
    public class login_gettokenquestion : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sUsername = context.Request["username"].ToStringSafetyNormalize();
            string sPassword = context.Request["password"].ToStringSafety();

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LOGIN", "wrong username and/or password");

            TalaUser user = Song.Instance.LoginVaoSongChoi(sUsername, sPassword);
            if (user == null)
            {
            }
            else
            {
                // TODO: lấy token ra, tạo question, phun ngược về client
                // ghi answer vào Session[]
                cs = new APICommandStatus(APICommandStatusState.OK, "LOGINTOKEN", user.Authkey);
            }
            this.Cmd.Add(cs);
            base.ProcessRequest(context);



            // TODO: Admin account Use tokencard
//If(Check(username && password) == true)
//{
//// display random token question
//If(tokenIsOK)
//{
//// write authkey to session
//Session["adminauthkey"] = "randomstring";
//// redirect to admin account panel
//}
//Else
//{
//// display fail
//}
//}
//Else
//{
//// display fail
//}


        }
    }
}
