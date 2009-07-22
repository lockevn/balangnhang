using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;

namespace TalaAPI.community.tournament
{
    public class list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            // đọc các tournament trong DB, render
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            tournamentDTO[] arrRet = toursvc.GetTournamentList();  
            Data.AddRange(arrRet);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
