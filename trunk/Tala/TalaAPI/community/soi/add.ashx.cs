using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;

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
                lock (Song.Instance.DicSoi)
                {
                    Soi soi = Song.Instance.CreatSoiMoi(sName, sec.CurrentAU.Username);
                    cs = new APICommandStatus(APICommandStatusState.OK, "ADD_SOI", string.Format("{0}#{1}", soi.ID, soi.Name));
                }                
            }

            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
