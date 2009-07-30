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

namespace TalaAPI.play.van
{    
    public class info_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            VanVO dtoVan = new VanVO();

            // TODO: Thêm business mới
            /// Nếu authkey không phải là thành viên của sới player
            /// kiểm tra xem có nằm trong danh sách cho xem không?
            /// nếu ok, cho xem (bài đã đánh, đã ăn, phỏm đã hạ của ván)
            /// nếu không ok, trả về stat=fail, not allow như cũ

            // nếu đã login ok, có 2 trường hợp, 1 là player, 2 là muốn làm viewer
            TalaSecurity security = new TalaSecurity(context);
            
            Soi soi = security.CheckUserJoinedSoi(false);
            if (null == soi)
            {
                // là viewer
                /// cố lấy sới theo soiID truyền vào, mà nếu ko truyền thì cho lượn luôn
                string soiid = APIParamHelper.GetParam("soiid", context);
                soi = Song.Instance.GetSoiByID(soiid);

                if (null == soi)
                {
                    APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                    cs.Info += string.Format("Sới  {0} này không tồn tại", soiid);
                    Cmd.Add(cs);
                    base.ProcessRequest(context);
                }


                if (soi.SoiOption.IsAllowToViewer)
                {
                }
                else
                {
                    APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                    cs.Info += "Sới này không cho xem";
                    Cmd.Add(cs);
                    base.ProcessRequest(context);
                    return;
                }
            }          

            
            // đến đc đây, là sới != null (hoặc là sới của player, hoặc là sới đc xem, render thôi)
                          
            dtoVan.VanInfo = soi.CurrentVan;
            foreach (Seat seat in soi.SeatList)
            {
                foreach (Card card in seat.BaiDaAn)
                {
                    card.Pos = seat.Pos;
                    dtoVan.BaiDaAn.Add(card);
                }
                foreach (Card card in seat.BaiDaDanh)
                {
                    card.Pos = seat.Pos;
                    dtoVan.BaiDaDanh.Add(card);
                }

                #region Phỏm đã hạ

                foreach (Phom phom in seat.PhomList)
                {
                    phom.Pos = seat.Pos;
                    dtoVan.PhomDaHa.Add(phom);
                }

                #endregion
            }

            if (soi.CurrentVan.IsFinished)
            {
                // kết thúc rồi, là cuối ván, show hết hàng họ ra thôi, show cả bài trên tay của mọi người
                foreach (Seat seat in soi.SeatList)
                {
                    foreach (Card card in seat.BaiTrenTay)
                    {
                        card.Pos = seat.Pos;
                        dtoVan.BaiTrenTay.Add(card);
                    }
                }
            }
            else
            {
                // nếu currentAU là player, cho view, ấn luôn bài trên tay của chính họ
                Seat currentAUSeat = security.CheckUserJoinedSeat();
                if (null != currentAUSeat)
                {
                    foreach (Card card in currentAUSeat.BaiTrenTay)
                    {
                        dtoVan.BaiTrenTay.Add(card);
                    }
                }
            }

            Data.Add(dtoVan);
            base.Stat = APICommandStatusState.OK;
           
            base.ProcessRequest(context);
        }
    }
}
