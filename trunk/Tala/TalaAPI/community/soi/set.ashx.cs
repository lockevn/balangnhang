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
    public class set : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);           

            string soiid = context.Request["soiid"].ToStringSafetyNormalize();
            string option = context.Request["option"].ToStringSafetyNormalize();
                        
        
            Soi soi = Song.Instance.GetSoiByID(soiid);
		    if(security.CurrentAU.Username == soi.OwnerUsername)
            {
                // Nếu sới đã lock luật, lỗi
                if (soi.IsLocked)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "SOI_OPTION_IS_LOCKED", "");
                    Cmd.Add(cs);
                }
                else
                {                    
                    //chiprate	Số đơn vị tiền tệ mà một chip đổi được	VD, nếu 1 chip là 3 vcoin, Chiprate:3
                    //gaenable	Có bật chế độ gà hay không, int=[0.1]	gaenable:1
                    //randomposafteru	Có đổi chỗ ngồi sau khi ù không, int=[0,1]	
                    //Turntimeout	Số giây để suy nghĩ khi đến lượt. Nếu quá thời gian này, hệ thống sẽ tự đánh một cây (bên trái ngoài cùng của bài trên tay)	
                    Hashtable htbOption = GURUCORE.Lib.Core.Text.TextHelper.ParseNameValueString(option, '^', ':');
                    
                    int chiprate = 0;
                    int.TryParse(htbOption["chiprate"] as string, out chiprate);
                    soi.SoiOption.TiGiaChip = chiprate;
                    
                    bool isga = false;
                    bool.TryParse(htbOption["isga"] as string, out isga);
                    soi.SoiOption.IsGa = isga;

                    // set option is OK
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "SOI_OPTION", "1");
                    Cmd.Add(cs);
                }
            }
            else
            {
                Cmd.Add(APICommandStatus.NOT_ALLOW_CommandStatus);
            }

            base.ProcessRequest(context);
        }
    }
}
