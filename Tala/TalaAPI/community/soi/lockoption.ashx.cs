using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.soi
{
    public class lockoption : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            string soiid = APIParamHelper.GetParam("soiid", context);
            Soi soi = Song.Instance.GetSoiByID(soiid);

            if (soi != null)
            {
                soi.Autorun();
                soi.IsLocked = true;
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "SOI_LOCK", "1");
                Cmd.Add(cs);
            }
            else
            {
                APICommandStatus cs = APICommandStatus.Get_NOT_JOINED_SOI_CommandStatus();
                Cmd.Add(cs);
            }

            base.ProcessRequest(context);
        }
    }
}
