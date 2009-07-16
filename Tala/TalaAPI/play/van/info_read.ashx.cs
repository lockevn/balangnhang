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
            // TODO: Thêm business mới
            /// Nếu authkey không phải là thành viên của sới player
            /// kiểm tra xem có nằm trong danh sách cho xem không?
            /// nếu ok, cho xem (bài đã đánh, đã ăn, phỏm đã hạ của ván)
            /// nếu không ok, trả về stat=fail, not allow như cũ

            TalaSecurity security = new TalaSecurity(context);
            Soi soi = security.CheckUserJoinedSoi();

            DTOVan dtoVan = new DTOVan();

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


                ///TODO: for testing only
                dtoVan.Noc = soi.CurrentVan.Noc;


                Data.Add(dtoVan);
                base.Stat = APICommandStatusState.OK;
            }
            else
            {
                APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info += " . Chưa có ván chơi";
                Cmd.Add(cs);
            }


            base.ProcessRequest(context);
        }
    }
}
