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
    public class user_add : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            string pu = context.Request["pu"].ToStringSafetyNormalize();
            string soiid = context.Request["soiid"].ToStringSafetyNormalize();

            Soi soi = Song.Instance.GetSoiByID(soiid);
            if (pu.IsNullOrEmpty())
            {
                // AU tự add mình vào, tự join Sới
                int sResult = soi.AddPlayer(security.CurrentAU.Username);
                if (sResult > 0)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Gia nhập sới thành công");
                    Cmd.Add(cs);
                }
                else
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "JOIN_SOI", "Gia nhập sới thất bại");
                    Cmd.Add(cs);
                }
            }
            else
            {
                // player mời người khác vào chơi
                User usertoadd = Song.Instance.GetUserByUsername(pu);
                if (usertoadd == null)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "JOIN_SOI", "Người bạn mời đã rời mạng");
                    Cmd.Add(cs);
                }
                else
                {
                    int nResult = soi.AddPlayer(usertoadd.Username);
                    if (nResult > 0)
                    {
                        APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Mời gia nhập sới thành công");
                        Cmd.Add(cs);
                    }
                    else
                    {
                        APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "JOIN_SOI", "Mời gia nhập sới thất bại");
                        Cmd.Add(cs);
                    }
                }                    
            }

            base.ProcessRequest(context);
        }
    }
}
