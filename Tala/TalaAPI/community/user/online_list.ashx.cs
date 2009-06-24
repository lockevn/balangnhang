using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;

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
