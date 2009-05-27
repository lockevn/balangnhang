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
    public class danh : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            string cay = context.Request["cay"].ToStringSafetyNormalize();           
            Card card = Card.ParseString(cay);
            APICommandStatus cs;

            if (card == null)
            {
                cs = new APICommandStatus(APICommandStatusState.FAIL, "DANH", "invalid format of paramater cay. Must be: SoSoChat e.g: 01C (at co*)");
                this.Cmd.Add(cs);
                base.ProcessRequest(context);
            }

            TalaSecurity security = new TalaSecurity(context);
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrVan;
            bool result = van.Danh(seat, card);
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "DANH", "valid action");

            }
            else
            {
                cs = new APICommandStatus(APICommandStatusState.FAIL, "DANH", "action failed");
            }
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
                                                                                                                                               
        }
    }
}
