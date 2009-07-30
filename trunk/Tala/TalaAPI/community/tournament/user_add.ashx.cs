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
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;

namespace TalaAPI.community.tournament
{
    public class user_add : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            string tournamentid = APIParamHelper.GetParam("tournamentid", context);
            string sCoinUsername = APIParamHelper.GetParam("username", context);
            string sCoinPassword = APIParamHelper.GetParam("password", context);

            APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();

            // tìm tournament muốn tham gia
            tournamentDTO tour = Song.Instance.GetTournamentByID(tournamentid);
            if (null == tour)
            {
                cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info = string.Format("tounrnament id={0} không tồn tại", tournamentid);
                Cmd.Add(cs);
                base.ProcessRequest(context);                
            }

            #region Kiểm tra định danh với kho tiền VTC của user
            
            IAuthenticationService authensvc = ServiceLocator.Locate<IAuthenticationService, AuthenticationService>();
            if (null == authensvc.AuthenticateVTC(sCoinUsername, sCoinPassword))
            {
                cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info = "Sai Username và/hoặc password, không thể xác thực username và password của bạn với VTC";
                Cmd.Add(cs);
                base.ProcessRequest(context);                
            }

            #endregion

            #region Kiểm tra xem đủ tiền chơi không
  
            IMoneyService moneysvc = ServiceLocator.Locate<IMoneyService, MoneyService>();
            int nRemainBalance = moneysvc.GetBalanceOfVTCUser(sCoinUsername);

            if(tour.enrollfee > nRemainBalance /* thiếu tiền */)
            {
                cs = APICommandStatus.Get_NOT_ALLOW_CommandStatus();
                cs.Info = string.Format("User {0} còn số tiền là {1}, không đủ để gia nhập giải đấu {2}:{3} (lệ phí tham gia là {4})", sCoinUsername, nRemainBalance, tournamentid, tour.name, tour.enrollfee);
                Cmd.Add(cs);
                base.ProcessRequest(context);                
            }

            #endregion

            /// thông tin của giải, đã kết thúc chưa ...
            if(tour.isenabled /* đã cho chơi */
                && ((DateTime)tour.endtime).CompareTo(DateTime.Now) > 0 /* còn hạn chơi */                
                )
            {
                ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
                toursvc.AddUserToTournament(security.CurrentAU.Username, sCoinUsername, TalaSecurity.GetClientIP(), tour);               
            }

            cs = new APICommandStatus(true, "JOIN_TOURNAMENT", "thành công, bạn đã bị trừ tiền mua vé là " + tour.enrollfee);
            Cmd.Add(cs);
            base.ProcessRequest(context);
        }
    }
}
