using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;
using System.Linq;


namespace TalaAPI.community.id
{
    public class logout : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            APICommandStatus cs = APICommandStatus.Get_WRONG_AUTHKEY_CommandStatus();
            string sUsername = string.Empty;
            if (Song.Instance.DicValidAuthkey.TryGetValue(sAuthkey, out sUsername))
            {
                Song.Instance.Logout(sAuthkey, sUsername);                
                cs = new APICommandStatus(APICommandStatusState.OK, "LOGOUT", sUsername);
            }            
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
