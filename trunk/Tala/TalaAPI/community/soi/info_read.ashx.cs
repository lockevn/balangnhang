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
