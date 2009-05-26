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
    public class user_delete : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            string pu = context.Request["pu"].ToStringSafetyNormalize();
            string soiid = context.Request["soiid"].ToStringSafetyNormalize();

            Soi soi = Song.Instance.GetSoiByID(soiid);
            if (pu.IsNullOrEmpty())
            {
                // AU rời khỏi sới
                int sResult = soi.RemovePlayer(security.CurrentAU.Username);
                if (sResult > 0)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "LEAVE_SOI", "Rời sới thành công");
                    Cmd.Add(cs);
                }
                else
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LEAVE_SOI", "Rời sới thất bại");
                    Cmd.Add(cs);
                }
            }
            else
            {
                // Owner đuổi player khác
                if (security.CurrentAU.Username == soi.OwnerUsername)
                {
                    User userNeedToRemove = Song.Instance.GetUserByUsername(pu);
                    int nResult = soi.RemovePlayer(userNeedToRemove.Username);
                    if (nResult > 0)
                    {
                        APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "LEAVE_SOI", "đuổi thành công");
                        Cmd.Add(cs);
                    }
                    else
                    {
                        APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LEAVE_SOI", "không đuổi được, có thể do sai tên username");
                        Cmd.Add(cs);
                    }
                    
                }
                else 
                { 
                    // AU không phải là chủ sới, ko cho đuổi
                    APICommandStatus cs = APICommandStatus.Get_NOT_ALLOW_CommandStatus();
                    cs.Info = "bạn không phải chủ sới, không kick player được";
                    Cmd.Add(cs);
                }                
            }

            base.ProcessRequest(context);
        }
    }
}
