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

namespace TalaAPI.community.soi
{
    public class add : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity sec = new TalaSecurity(context);
            APICommandStatus cs;

            string sName = context.Request["name"].ToStringSafetyNormalize();
            if (string.IsNullOrEmpty(sName))
            {
                cs = new APICommandStatus(APICommandStatusState.FAIL, "ADD_SOI", "Tên không được rỗng");
            }
            else
            {
                lock (Song.Instance.Soi)
                {
                    Soi soi = Song.Instance.CreatSoiMoi(sName, sec.CurrentAU.Username);
                    cs = new APICommandStatus(APICommandStatusState.OK, "ADD_SOI", string.Format("{0}#{1}", soi.Id, soi.Name));
                }                
            }

            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
