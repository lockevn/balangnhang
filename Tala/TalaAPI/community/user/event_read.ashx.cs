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
    public class event_read : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity sec = new TalaSecurity(context);
            string paramCleanQueue = APIParamHelper.GetParam("clean", context, false);

            lock (sec.CurrentAU.MessageQueue)
            {
                Data.AddRange(sec.CurrentAU.MessageQueue);

                // clean queue, trừ phi explicit báo với mình là không cần clean
                if (paramCleanQueue != "0")
                {
                    sec.CurrentAU.MessageQueue.Clear();
                }
            }

            base.Stat = APICommandStatusState.OK;
            
            base.ProcessRequest(context);
        }
        
    }
}
