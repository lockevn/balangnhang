using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using GURUCORE.Lib.Core.Security.Cryptography;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;

namespace TalaAPI.community.id
{
    public class login : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sUsername = APIParamHelper.GetParam("username", context);
            string sPassword = APIParamHelper.GetParam("password", context);

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LOGIN", "wrong username and/or password");

            TalaUser user = Song.Instance.LoginVaoSongChoi(sUsername, sPassword);
            if (user == null)
            {
            }
            else
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "LOGIN", user.Authkey);

                #region Log lại hành vi login này

                try
                {
                    IAuthenticationService authensvc = ServiceLocator.Locate<IAuthenticationService, AuthenticationService>();
                    
                    authensvc.LogLoginAction(
                        TalaSecurity.GetClientIP(),
                        string.Format("{0}#{1}#{2}", cs.ToXMLString(), context.Request.UserHostName, context.Request.UserAgent),
                        sUsername);
                }
                catch { }               

                #endregion

                #region Tạo stat nếu cần

                try
                {
                    IPlayingService playingsvc = ServiceLocator.Locate<IPlayingService, PlayingService>();
                    playingsvc.CreateUserStat(sUsername);
                }
                catch { }

                #endregion
                
            }            
            this.Cmd.Add(cs);            
            base.ProcessRequest(context);
        }
    }
}
