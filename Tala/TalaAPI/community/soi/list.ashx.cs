using System.Web;
using System.Linq;
using System.Collections;
using System.Collections.Generic;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.soi
{
    public class list : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string tournamentid = APIParamHelper.GetParam("tournamentid", context, false);

            if (string.IsNullOrEmpty(tournamentid))
            {
                tournamentid = "1";
            }
            
            var soiOfTour = Song.Instance.GetSoiByTournamentID(tournamentid);
            Data.AddRange(soiOfTour.ToArray());
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
