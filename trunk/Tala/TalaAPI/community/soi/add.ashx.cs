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
                cs = new APICommandStatus(APICommandStatusState.FAIL, "add_soi", "Tên không được rỗng");
            }
            else
            {
                lock (Song.Instance.Soi)
                {
                    Soi soi = new Soi(Song.Instance.Soi.Count + 1, sName, sec.CurrentAU.Username);
                    Song.Instance.Soi.Add(soi.Id.ToString(), soi);
                    soi.AddPlayer(sec.CurrentAU.Username);
                    cs = new APICommandStatus(APICommandStatusState.OK, "add_soi", string.Format("{0}#{1}", soi.Id, soi.Name));
                }                
            }

            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
