using System.Web;
using System.Collections.Generic;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using TalaAPI.Lib;
using System;

namespace TalaAPI.community.soi
{
    public class user_add_auto : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);
            
            string tournamentid = APIParamHelper.GetParam("tournamentid", context);
            tournamentDTO tournament = Song.Instance.GetTournamentByID(tournamentid);
            if (tournament == null)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "NOT_FOUND", "không tìm thấy tournament");
                Cmd.Add(cs);
                base.ProcessRequest(context);
                return;
            }
            
            /// tìm danh sách các sới của tour hiện tại, còn chỗ
            /// bố trí thu xếp cho user này vào một sới random nào đấy            
            List<Soi> arrSoiOfTournament = new List<Soi>(); // TODO: 
            Random random = new Random(DateTime.Now.Millisecond);
            int nRandomIndex = random.Next(0, arrSoiOfTournament.Count - 1);                        
            Soi soiAvailableRandom = arrSoiOfTournament[nRandomIndex];

            // AU tự add mình vào, tự join Sới
            int sResult = soiAvailableRandom.AddPlayer(security.CurrentAU.Username);
            if (sResult >= 0)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Gia nhập sới thành công");
                Cmd.Add(cs);
            }
            else
            {
                APICommandStatus cs = new APICommandStatus(false);
                switch (sResult)
                {
                    case -1:
                        cs.ID = "SOI_FULL_PLAYER";
                        cs.Info = "Sới đầy rồi nhé";
                        break;
                    case -2:
                        cs.ID = "NOT_VALID";
                        cs.Info = "User này chưa login";
                        break;
                    case -3:
                        cs.ID = "NOT_ALLOW";
                        cs.Info = "Bạn đã vào sới khác rồi, không vào đây được nữa";
                        break;
                }
                Cmd.Add(cs);
            }            

            base.ProcessRequest(context);
        }
    }
}
