using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service;
using Quantum.Tala.Service.DTO;
using System.Linq;

namespace TalaAPI.community.tournament
{
    public class joint_list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity sec = new TalaSecurity(context);
            
            // đọc các tournament mà user đã join            
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            tournamentDTO[] arrRet = toursvc.GetTournamentOfUser(sec.CurrentAU.Username);
            sec.CurrentAU.AttendingTournament = arrRet.ToList();

            Data.AddRange(arrRet);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
