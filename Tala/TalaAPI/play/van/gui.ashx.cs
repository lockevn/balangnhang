﻿using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Exception;
using Quantum.Tala.Lib.XMLOutput;
using System.Collections.Generic;
using Quantum.Tala.Service;

namespace TalaAPI.play.van
{    
    public class gui : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string bai = APIParamHelper.GetParam("bai", context);
            TalaSecurity security = new TalaSecurity(context);
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;

            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "GUI", "action failed");

            string[] stringArr = bai.Split(CONST.CARDLLIST_SEPERATOR_SYMBOL);
            foreach (string str in stringArr)
            {
                string[] stringCardArr = str.Split(CONST.CARD_SEPERATOR_SYMBOL);
                string phomID = stringCardArr[0];
                int phomIDNumber = -1 ;               
		        int.TryParse(phomID, out phomIDNumber);
	            
                if(stringCardArr.Length < 2 || phomIDNumber < 0)
                {
                    cs = new APICommandStatus(APICommandStatusState.FAIL, "GUI", "invalid value for parameter bai, must be in format: {phomID,string,string,...^phomID,string,...}");
                    this.Cmd.Add(cs);
                    base.ProcessRequest(context);
                }

                /*parse string in stringCardArr to get a list of card gui*/
                List<Card> cardList = new List<Card>();
                for (int i = 1; i < stringCardArr.Length; i++)
                {
                    string cardStr = stringCardArr[i];
                    try
                    {
                        Card tmpCard = cardStr.ToCard();
                        cardList.Add(tmpCard);
                    }
                    catch (CardException ce)
                    {
                        this.SendErrorAPICommand(ce, context);
                    }
                }
                
                /*gui cardList vào phomID*/                
                result = van.Gui(seat, phomIDNumber, cardList.ToArray());
                
                /*chỉ gửi thành công khi tất cả các phỏm gửi, cây gửi là hợp lệ*/
                if (!result)
                {
                    break;
                }                                
            }                                    

            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "GUI", "valid action");
            }

            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
