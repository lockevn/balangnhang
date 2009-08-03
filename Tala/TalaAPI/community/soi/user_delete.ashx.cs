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
using Quantum.Tala.Service.Authentication;

namespace TalaAPI.community.soi
{
    public class user_delete : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);            
            Soi soi = security.CheckUserJoinedSoi();
            soi.Autorun();

            string pu = APIParamHelper.GetParam("pu", context, false);
            if (pu.IsNullOrEmpty())
            {
                // AU rời khỏi sới
                int sResult = soi.RemovePlayer(security.CurrentAU.Username);
                if (sResult >= 0)
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
                if (security.CurrentAU.Username != soi.OwnerUsername)
                {
                    // AU không phải là chủ sới, ko cho đuổi
                    APICommandStatus cs = APICommandStatus.Get_NOT_ALLOW_CommandStatus();
                    cs.Info = "bạn không phải chủ sới, không kick player được";
                    Cmd.Add(cs);
                    base.ProcessRequest(context);
                }

                TalaUser userNeedToRemove = Song.Instance.GetUserByUsername(pu);
                if (null == userNeedToRemove)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LEAVE_SOI", "username không tồn tại");
                    Cmd.Add(cs);
                    base.ProcessRequest(context);
                }


                int nResult = soi.RemovePlayer(userNeedToRemove.Username);
                if (nResult >= 0)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "LEAVE_SOI", "đuổi thành công");
                    Cmd.Add(cs);
                }
                else
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "LEAVE_SOI", "lỗi không đuổi được");
                    Cmd.Add(cs);
                }
            }     

            base.ProcessRequest(context);
        }
    }
}
