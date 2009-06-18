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
            Van van = soi.CurrentVan;

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "An", "action failed");
            Card anCard = null;
            try
            {
                anCard = van.An(seat);
            }
            catch (NotInTurnException nite)
            {
                nite.SendErrorAPICommand(context);
            }

            if (anCard != null)
            {
                this.Data.Add(anCard);
                cs = new APICommandStatus(APICommandStatusState.OK, "An", string.Format("Ăn cây {0} thành công", anCard) );
            }

            this.Cmd.Add(cs);
            base.ProcessRequest(context);

        }    
    }
}