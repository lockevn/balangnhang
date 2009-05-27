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

namespace TalaAPI.community.soi
{
    public class start : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            //o	Nếu AU không phải player của sới, lỗi, id=NOT_ALLOW
            // bỏ qua chưa giải quyết, vì 1 người chơi đc mỗi 1 sới

            APICommandStatus cs = new APICommandStatus(false, "SOI_START", string.Empty);
            int nRet = security.CurrentAU.CurrentSoi.StartPlaying();
            switch (nRet)
            {
                case -2:
                    //o	sới đã bắt đầu chơi rồi
                    break;
                case -1:
                    //o	Có người chưa sẵn sàng
                    break;
                default:
                    cs.Stat = APICommandStatusState.OK;
                    break;
            }

            Cmd.Add(cs);

            base.ProcessRequest(context);
        }
        
    }
}
