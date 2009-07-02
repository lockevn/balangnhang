using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.tournament
{
    public class list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            // TODO: đọc các tournament trong DB, render
            /// from
            /// where
            /// select
                        
            // Data.AddRange(Song.Instance.DicSoi.Values);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
