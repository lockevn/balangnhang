using System.Web;
using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using TalaAPI.Lib;
using System.Collections;
using System.Text;

namespace TalaAPI.admin.game
{
    public class autorun_incache_view : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {   
            StringBuilder sb = new StringBuilder();
            IDictionaryEnumerator enumer = context.Cache.GetEnumerator();
            while(enumer.MoveNext())
            {
                sb.Append("<key>").Append(enumer.Key).Append("</key>").Append("<value>").Append((enumer.Value as TalaUser).Username).Append("</value>");
            }
            this.StringDirectUnderRoot += sb.ToString();
            base.ProcessRequest(context);
        }
    }
}
