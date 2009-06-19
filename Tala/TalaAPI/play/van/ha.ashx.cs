﻿using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.Business;
using System.Collections.Generic;
using TalaAPI.Exception;
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.play.van
{    
    public class ha : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string bai = APIParamHelper.CheckEmptyParam("bai", context);
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
                ce.SendErrorAPICommand(context);
            }

            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "Ha", "action failed");
            try
            {
                result = van.Ha(seat, cardArrList);
            }
            catch (NotInTurnException nite)
            {
                nite.SendErrorAPICommand(context);
            }

            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "Ha", "valid action");
            }

            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
