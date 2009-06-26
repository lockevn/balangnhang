using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.play.van
{    
    public class baitrentay_list : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            Soi soi = security.CheckUserJoinedSoi();

            // chỉ lấy current sới, current seat của authkey hiện tại, trả ra bài trên tay của Seat đó
            Data.AddRange(soi.GetSeatByUsername(security.CurrentAU.Username).BaiTrenTay);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
    }
}
