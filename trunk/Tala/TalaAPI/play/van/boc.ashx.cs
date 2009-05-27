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
using TalaAPI.Exception;

namespace TalaAPI.play.van
{    
    public class boc : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);
            
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrVan;

            bool result = false;
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "Boc", "action failed");
            try
            {
                result = van.Boc(seat);
            }
            catch (NotInTurnException nite)
            {
                nite.Source = "Boc";
                nite.SendErrorAPICommand(context);
            }
            if (result)
            {
                cs = new APICommandStatus(APICommandStatusState.OK, "Boc", "valid action");
            }
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
