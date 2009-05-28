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
    public class baidadanh_list : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            string pos = context.Request["pos"].ToStringSafetyNormalize();
            int nPos;           

            // nếu parse thành công thành int, và pos trong khoảng 0:3, tức là truyền pos hợp lệ, trả về bài theo vị trí hợp lệ
            if (int.TryParse(pos, out nPos) && 0 <= nPos && nPos <= 3)
            {                
                Data.AddRange (security.CurrentAU.CurrentSoi.SeatList[nPos].BaiDaDanh);
                base.Stat = APICommandStatusState.OK;
            }
            else
            {
                // tham số không hợp lệ, hoặc không truyền tham số pos, trả về tất cả danh sách bài
                foreach (Seat seat in security.CurrentAU.CurrentSoi.SeatList)
                {
                    foreach (Card card in seat.BaiDaDanh)
                    {
                        card.Pos = seat.Index;
                        Data.Add(card);                        
                    }
                }
                base.Stat = APICommandStatusState.OK;
            }

            base.ProcessRequest(context);
        }
    }
}
