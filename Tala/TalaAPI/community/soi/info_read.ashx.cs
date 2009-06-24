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
    public class info_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sID = context.Request["soiid"].ToStringSafetyNormalize();

            if (string.IsNullOrEmpty(sID))
            {
            }
            else
            {
                Soi soi = Song.Instance.DicSoi.ContainsKey(sID) ? Song.Instance.DicSoi[sID] : null;
                if (soi != null)
                {
                    Data.Add(soi);
                    base.Stat = APICommandStatusState.OK;
                }
            }

            base.ProcessRequest(context);
        }
    }
}
