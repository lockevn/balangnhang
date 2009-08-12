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
using Quantum.Tala.Service;

namespace TalaAPI.community.soi
{
    public class user_ready : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            // lấy current sới, current seat của user AU, đặt cờ ready = true
            Soi soi = security.CheckUserJoinedSoi();
            soi.Autorun();

            soi.SetReady(security.CurrentAU);

            // nếu sới đầy chỗ rồi
            // thử gọi hàm StartPlaying (trong đấy tự nó kiểm tra điều kiện để bắt đầu ván)
            if (soi.SeatList.Count == CONST.MAX_SEAT_IN_SOI_ALLOW && soi.IsAllPlayerReady())
            {                
                soi.StartPlaying();
            }

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "READY", "1");
            Cmd.Add(cs);

            base.ProcessRequest(context);
        }
    }
}
