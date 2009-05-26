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
    public class lockoption : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            string soiid = context.Request["soiid"].ToStringSafetyNormalize();

            Soi soi = Song.Instance.GetSoiByID(soiid);
            lock (soi)
            {
                soi.IsLocked = true;
            }

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "SOI_LOCK", "1");
            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
