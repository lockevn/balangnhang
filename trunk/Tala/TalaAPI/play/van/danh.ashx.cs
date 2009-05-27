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
using TalaAPI.Exception;

namespace TalaAPI.play.van
{    
    public class danh : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            string cay = APIParamHelper.CheckEmptyParam("cay", context);
            Card card = null;
            try
            {
                card = Card.ParseString(cay);
            }
            catch (CardException ce)
            {
                ce.SendErrorAPICommand(context);
            }

            TalaSecurity security = new TalaSecurity(context);
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;
            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "DANH", "action failed");
            try
            {
                result = van.Danh(seat, card);
            }
            catch (NotInTurnException nite)
            {
                nite.SendErrorAPICommand(context);
            }
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "DANH", "valid action");

            }           
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
                                                                                                                                               
        }
    }
}
