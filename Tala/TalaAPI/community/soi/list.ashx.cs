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

namespace TalaAPI.community.soi
{
    public class list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            Data.AddRange(Song.Instance.Soi.Values);
            base.ProcessRequest(context);
        }
    }
}
