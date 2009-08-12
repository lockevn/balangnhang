using System.Web;
using System.Linq;
using System.Collections;
using System.Collections.Generic;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Lib;

namespace TalaAPI.community.soi
{
    public class list : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string tournamentid = APIParamHelper.GetParam("tournamentid", context, false);


            if (string.IsNullOrEmpty(tournamentid))
            {
                tournamentid = ((int)TournamentType.Free).ToString();
            }

            var soiOfTour = Song.Instance.GetSoiByTournamentID(tournamentid);
            Data.AddRange(                
                soiOfTour.Page(APIParamHelper.GetPagingPage(), APIParamHelper.GetPagingItemPerPage()).ToArray()
                );
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
