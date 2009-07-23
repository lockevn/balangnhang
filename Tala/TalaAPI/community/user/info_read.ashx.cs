using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using TalaAPI.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.DTO;
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;

namespace TalaAPI.community.user
{    
    public class info_read : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            string pu = APIParamHelper.GetParam("pu", context);
                        
            /// lấy các thông tin về user ra để hiển thị
            /// tìm trong cache trước
            /// nếu không thấy, fetch từ DB lên, lưu vào cache
            /// trả lại cho người xem           
                        
            IUserProfileService userprofilesvc = ServiceLocator.Locate<IUserProfileService, UserProfileService>();
            user_statDTO ustat = userprofilesvc.GetUserPlayStat(pu);

            if (null == ustat)
            {
                base.Stat = APICommandStatusState.FAIL;
            }
            else
            {
                userDTO userToView = new userDTO
                {
                    Username = pu,
                    Win = ustat.win,
                    Lose = ustat.lose,
                    Point = ustat.point
                };
                Data.Add(userToView);
                base.Stat = APICommandStatusState.OK;
            }
            
            base.ProcessRequest(context);
        }
        
    }
}
