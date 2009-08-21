using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Exception;

namespace TalaAPI.play.van
{    
    public class boc : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);
            
            Soi soi = security.CheckUserJoinedSoi();
            Seat seat = security.CheckUserJoinedSeat();
            Van van = soi.CurrentVan;

            
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "Boc", "action failed");
            Card cardBoc = null;
            try
            {
                cardBoc = van.Boc(seat);
            }
            catch (NotInTurnException nite)
            {
                this.SendErrorAPICommand(nite, context);
            }

            if (cardBoc != null)
            {
                this.Data.Add(cardBoc);
                cs = new APICommandStatus(APICommandStatusState.OK, "Boc", "valid action");
            }

            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
