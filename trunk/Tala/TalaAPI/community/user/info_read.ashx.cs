using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;

namespace TalaAPI.community.user
{    
    public class info_read : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            // TODO: Cài đặt thân hàm
            /// lấy các thông tin về user ra để hiển thị
            /// tìm trong cache trước
            /// nếu không thấy, fetch từ DB lên, lưu vào cache
            /// trả lại cho người xem

            //Data.AddRange(Song.Instance.DicOnlineUser.Values);
            base.Stat = APICommandStatusState.OK;

            base.ProcessRequest(context);
        }
        
    }
}
