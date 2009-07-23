using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.user
{
    public class online_count : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            context.Response.Write(Song.Instance.DicOnlineUser.Count);            
        }
    }
}
