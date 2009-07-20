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
    public class tournament_reload : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {            
            TalaSecurity sec = new TalaSecurity(context);
            APICommandStatus cs;

            string sSoiID = APIParamHelper.GetParam("soiid", context);

            lock (Song.Instance.DicSoi)            
            {
                // TODO: lấy lại các bản ghi Tour trong DB, ghi lại vào bộ nhớ của Engine                
            }            

            // Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
