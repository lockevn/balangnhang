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

            APICommandStatus cs = APICommandStatus.Get_WRONG_AUTHKEY_CommandStatus();
            string sUsername = string.Empty;
            if (Song.Instance.DicValidAuthkey.TryGetValue(sAuthkey, out sUsername))
            {
                // nếu tìm thấy authkey
                try
                {
                    Song.Instance.DicValidAuthkey.Remove(sAuthkey);
                    Song.Instance.DicOnlineUser.Remove(sUsername);
                    
                    // TODO: tìm sới đang đánh, gỡ ra khỏi seatlist, chỗ này còn nhiều lo lắng, chưa nghĩ kỹ hết side effect
                }
                catch { }                
                cs = new APICommandStatus(APICommandStatusState.OK, "LOGOUT", "u=" + sUsername);
            }            
            
            this.Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
