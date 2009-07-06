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
using System.Collections.Generic;
using Quantum.Tala.Service.Exception;

namespace TalaAPI.play.van
{    
    public class u : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            
            //string bai = APIParamHelper.CheckEmptyParam("bai", context);                               
            TalaSecurity security = new TalaSecurity(context);
            
            /*check if user has joined soi, seat*/
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;
            
            /*tạo List<Card[]> tu stringArr*/
            //List<Card[]> cardArrList = null;
            //try
            //{
            //    cardArrList = TalaBusinessUtil.StringToCardList(bai);
            //}
            //catch (CardException ce)
            //{
            //    this.SendErrorAPICommand(ce, context);                    
            //}                
            
            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "U", "action failed");
            try
            {
                result = van.U(seat);
            }
            catch (NotInTurnException nite)
            {
                this.SendErrorAPICommand(nite, context);
            }
            
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "U", "valid action");
            }
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
