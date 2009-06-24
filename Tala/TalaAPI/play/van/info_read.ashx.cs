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
    public class info_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);
            Soi soi = security.CheckUserJoinedSoi();

            DTOVan dtoVan = new DTOVan();
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

            base.ProcessRequest(context);
        }
    }
}
