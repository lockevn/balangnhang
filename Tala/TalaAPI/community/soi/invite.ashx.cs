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
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.DTO;

namespace TalaAPI.community.soi
{
    public class invite : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity sec = new TalaSecurity(context);           
            
            Soi soi = sec.CurrentAU.CurrentSoi;
            if (soi == null)
            {
                APICommandStatus cs = APICommandStatus.Get_NOT_JOINED_SOI_CommandStatus();
                cs.Info = "bạn chưa ở trong sới nào, sao mời người khác được";
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }            
            
            tournamentDTO tour = soi.GetCurrentTournament();
            if (tour.type != (int)TournamentType.Free)
            {
                // không phải free tour, không cho vào tự do, đuổi luôn
                APICommandStatus cs = APICommandStatus.Get_NOT_ALLOW_CommandStatus();
                cs.Info += string.Format("Tournament {0}:{1} không cho phép mời", tour.id, tour.name);
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }


            string pu = context.Request["pu"].ToStringSafetyNormalize();
            TalaUser userToInvite = Song.Instance.GetUserByUsername(pu);
            if (null == userToInvite)
            {
                // tìm không thấy thì mời nỗi gì
                APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info += string.Format("User {0} không online", pu);
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }

            lock (userToInvite.MessageQueue)
            {
                Message msgEvent = new Message(Message.EVENT_INVITE, string.Format("{0},{1}", sec.CurrentAU.Username, soi.ID));
                userToInvite.MessageQueue.Add(msgEvent);
            }

            APICommandStatus csOK = new APICommandStatus(true, "INVITE", "Mời thành công user " + pu);
            Cmd.Add(csOK);
            base.ProcessRequest(context);
        }
    }
}
