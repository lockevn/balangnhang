﻿using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;

namespace TalaAPI.community.id
{
    public class checkauthkey : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = APIParamHelper.GetParam("authkey", context);

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "CHECKAUTHKEY", "0");
            if (Song.Instance.GetUserByAuthkey(sAuthkey) != null)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "CHECKAUTHKEY", "1");
            }
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
