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
    /// <summary>
    /// lấy sới đang tham gia
    /// </summary>
    public class soi_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaUser user = null;
            string pu = APIParamHelper.GetParam("pu", context);            
            if (string.IsNullOrEmpty(pu))
            {
                TalaSecurity security = new TalaSecurity(context);
                user = security.CurrentAU;
            }
            else
            {
                user = Song.Instance.GetUserByUsername(pu);
            }

            if (null == user)
            {
                APICommandStatus cs = new APICommandStatus(false, "USER_NOT_FOUND", "không tìm thấy user");
            }

            Data.Add(user.CurrentSoi);
            base.Stat = APICommandStatusState.OK;
            base.ProcessRequest(context);
        }
        
    }
}
