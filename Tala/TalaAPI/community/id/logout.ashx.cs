using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;


namespace TalaAPI.community.id
{
    public class logout : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            string sAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            string sUsername = Song.Instance.DicValidAuthkey[sAuthkey];
            try
            {
                Song.Instance.DicValidAuthkey.Remove(sAuthkey);
                Song.Instance.DicOnlineUser.Remove(sUsername);
            }
            catch { }

            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "LOGOUT", "authkey=" + sAuthkey + "u=" + sUsername);
            this.Cmd.Add(cs);

            base.ProcessRequest(context);
        }
    }
}
