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
                string sID = APIParamHelper.GetParam("soiid", context);
                soi = Song.Instance.GetSoiByID(sID);

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
            else
            {
                // là player, cho chạy tiếp để view
                // ấn luôn bài trên tay vào
                Seat currentAUSeat = security.CheckUserJoinedSeat();
                foreach (Card card in currentAUSeat.BaiTrenTay)
                {
                    dtoVan.BaiTrenTay.Add(card);
                }                
            }

            
            // đến đc đây, là sới != null (hoặc là sới của player, hoặc là sới đc xem, render thôi
            if(soi.IsPlaying)
            {
                
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

                Data.Add(dtoVan);
                base.Stat = APICommandStatusState.OK;
            }
            else
            {
                APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info += "Không có ván nào đang chơi";
                Cmd.Add(cs);
            }


            base.ProcessRequest(context);
        }
    }
}
