using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;

namespace TalaAPI.admin.game
{   
    public class tournament_reload : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {            
            TalaSecurity sec = new TalaSecurity(context);
            // TODO: Super Security needed
            
            APICommandStatus cs = new APICommandStatus(true, "LOAD_TOURNAMENT", "");
            lock (Song.Instance)            
            {
                Song.Instance.LoadTournamentFromDB();
            }            
            Cmd.Add(cs);

            base.ProcessRequest(context);
        }
    }
}
