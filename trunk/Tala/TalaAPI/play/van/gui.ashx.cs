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
using TalaAPI.Exception;
using TalaAPI.XMLRenderOutput;
using System.Collections.Generic;

namespace TalaAPI.play.van
{    
    public class gui : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string bai = APIParamHelper.CheckEmptyParam("bai", context);

            TalaSecurity security = new TalaSecurity(context);

            /*check if user has joined soi, seat*/
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;

            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "GUI", "action failed");

            string[] stringArr = bai.Split('^');            
            foreach (string str in stringArr)
            {
                string[] stringCardArr = str.Split(',');
                string phomID = stringCardArr[0];
                int phomIDNumber = -1 ;
                try 
	            {	        
		            int.TryParse(phomID, out phomIDNumber);
	            }
	            catch (System.Exception)
	            {            				            
	            }
                if(stringCardArr.Length < 2 || phomIDNumber == -1)
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
                        Card tmpCard = Card.ParseString(cardStr);
                        cardList.Add(tmpCard);
                    }
                    catch (CardException ce)
                    {
                        ce.SendErrorAPICommand(context);
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
