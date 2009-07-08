using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;

namespace TalaAPI.admin.game
{    
    /// <summary>
    /// Nhận soiid, gọi Sòng.DeleteSoi()
    /// </summary>
    public class soi_delete : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {            
            TalaSecurity sec = new TalaSecurity(context);
            APICommandStatus cs;

            string sSoiID = context.Request["soiid"].ToStringSafetyNormalize();
            if (string.IsNullOrEmpty(sSoiID))
            {
                cs = new APICommandStatus(APICommandStatusState.FAIL, "DELETE_SOI", "SoiID không được rỗng");
            }
            else
            {
                lock (Song.Instance.DicSoi)
                {
                    bool bRet = Song.Instance.DeleteSoi(sSoiID);
                    cs = new APICommandStatus(APICommandStatusState.OK, "DELETE_SOI", string.Format("{0}#{1}", sSoiID, bRet));
                }
            }

            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
