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
    public class start : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            //o	Nếu AU không phải player của sới, lỗi, id=NOT_ALLOW
            // bỏ qua chưa giải quyết, vì 1 người chơi đc mỗi 1 sới

            APICommandStatus cs = new APICommandStatus(false);            
            Soi soi =  security.CheckUserJoinedSoi();
            int nRet = soi.StartPlaying();
            switch (nRet)
            {
                case -3:
                    //o	số người chơi chưa đúng (1 người ko chơi đc, hơn 4 người không chơi đc)
                    // Hiện tại đúng 4 mới được chơi
                    cs.ID = "NUMBER_OF_PLAYER";
                    cs.Info = "Thiếu người chơi";
                    break;

                case -2:
                    //o	sới đã bắt đầu chơi rồi
                    cs.ID = "SOI_IS_PLAYING";
                    cs.Info = "Sới đã chơi rồi";
                    break;
                case -1:
                    //o	Có người chưa sẵn sàng
                    cs.ID = "NOT_READY";
                    foreach (Seat seat in security.CurrentAU.CurrentSoi.SeatList)
                    {
                        if (seat.IsReady == false)
                        {
                            cs.Info += seat.Player.Username  + ",";
                        }
                    }
                    cs.Info = cs.Info.Trim(',');
                    break;
                default:
                    cs.Stat = APICommandStatusState.OK;
                    cs.ID = "SOI_START";
                    cs.Info = "1";
                    break;
            }

            Cmd.Add(cs);

            base.ProcessRequest(context);
        }
        
    }
}
