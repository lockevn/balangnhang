using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Business;

namespace TalaAPI.play.van
{    
    public class boc : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            APICommandStatus cs;           
            TalaSecurity security = new TalaSecurity(context);
            
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrVan;
            bool result = van.Boc(seat);
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "Boc", "valid action");
            }
            else
            {
                cs = new APICommandStatus(APICommandStatusState.FAIL, "Boc", "action failed");
            }
            this.Cmd.Add(cs);
            base.ProcessRequest(context);                                           
        }
    }
}
