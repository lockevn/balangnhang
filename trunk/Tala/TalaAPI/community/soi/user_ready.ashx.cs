using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.community.soi
{
    public class user_ready : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            // lấy current sới, current seat của user AU, đặt cờ ready = true
            Soi soi = security.CheckUserJoinedSoi();
            soi.SetReady(security.CurrentAU);
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "READY", "1");
            Cmd.Add(cs);

            base.ProcessRequest(context);
        }
    }
}
