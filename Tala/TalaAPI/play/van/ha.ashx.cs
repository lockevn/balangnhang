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
using System.Collections.Generic;
using Quantum.Tala.Service.Exception;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.play.van
{    
    public class ha : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string bai = APIParamHelper.GetParam("bai", context);
            TalaSecurity security = new TalaSecurity(context);

            /*check if user has joined soi, seat*/
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;

            /*tạo List<Card[]> tu stringArr*/
            List<Card[]> cardArrList = null;
            try
            {
                cardArrList = TalaBusinessUtil.StringToCardList(bai);
            }
            catch (CardException ce)
            {
                this.SendErrorAPICommand(ce, context);
            }

            string sResult = string.Empty;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "Ha", "");
            try
            {
                sResult = van.Ha(seat, cardArrList);
            }
            catch (NotInTurnException nite)
            {
                this.SendErrorAPICommand(nite, context);
            }

            if (sResult.IsNullOrEmpty())
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "Ha", "valid");
            }
            else
            {
                // phi thông báo lỗi ra
                cs.Info = sResult;
            }
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
