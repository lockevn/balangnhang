using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;

namespace TalaAPI.community.soi
{
    public class info_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sID = APIParamHelper.GetParam("soiid", context);

            if (string.IsNullOrEmpty(sID))
            {
            }
            else
            {
                Soi soi = Song.Instance.GetSoiByID(sID);
                if (soi != null)
                {
                    soi.Autorun();
                    Data.Add(soi);
                    base.Stat = APICommandStatusState.OK;
                }
            }

            base.ProcessRequest(context);
        }
    }
}
