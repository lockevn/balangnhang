using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.soi
{
    public class list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            Data.AddRange(Song.Instance.DicSoi.Values);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
