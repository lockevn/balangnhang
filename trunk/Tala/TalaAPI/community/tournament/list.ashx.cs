using System.Web;
using System.Linq;
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
            string type = APIParamHelper.GetParam("type", context);

            var listTourOfType = Song.Instance.DicTournament.Values.Where(tour => tour.type.ToString() == type);
            Data.AddRange(listTourOfType.ToArray());
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
