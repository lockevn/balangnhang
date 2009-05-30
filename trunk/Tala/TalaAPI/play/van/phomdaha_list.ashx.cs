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

namespace TalaAPI.play.van
{    
    public class phomdaha_list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            Soi soi = security.CheckUserJoinedSoi();

            string pos = context.Request["pos"].ToStringSafetyNormalize();
            int nPos;

            // nếu parse thành công thành int, và pos trong khoảng 0:3, tức là truyền pos hợp lệ, trả về bài theo vị trí hợp lệ
            if (int.TryParse(pos, out nPos) && 0 <= nPos && nPos <= 3)
            {
                Seat seat = soi.SeatList[nPos];
                Data.AddRange(seat.PhomList);
                base.Stat = APICommandStatusState.OK;
            }
            else
            {
                // tham số không hợp lệ, hoặc không truyền tham số pos, trả về tất cả danh sách bài
                foreach (Seat seat in soi.SeatList)
                {
                    foreach (Phom phom in seat.PhomList)
                    {
                        phom.Pos = seat.Index;
                        Data.Add(phom);                        
                    }
                }
                base.Stat = APICommandStatusState.OK;
            }

            base.ProcessRequest(context);
        }
    }
}
