using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;

namespace TalaAPI.admin.game
{    
    /// <summary>
    /// Nhận soiid, gọi Sòng.DeleteSoi()
    /// </summary>
    public class song_reset : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {            
            TalaSecurity sec = new TalaSecurity(context);
            base.ProcessRequest(context);
        }
    }
}
