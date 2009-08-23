using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

using System.Linq;

namespace TalaAPI.community.user
{
    public class online_list : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            Data.AddRange(Song.Instance.DicOnlineUser.Values);
            base.Stat = APICommandStatusState.OK;
            base.ProcessRequest(context);
        }
    }
}
