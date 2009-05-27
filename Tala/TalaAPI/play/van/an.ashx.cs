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
    public class an : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrVan;
            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "An", "action failed");
            try
            {
                result = van.An(seat);
            }
            catch (NotInTurnException nite)
            {
                nite.SendErrorAPICommand(context);
            }
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "An", "valid action");
            }
            this.Cmd.Add(cs);
            base.ProcessRequest(context);

        }    
    }
}
