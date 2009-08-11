using System.Web;
using System.Linq;
using System.Collections.Generic;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using TalaAPI.Lib;
using System;

namespace TalaAPI.community.soi
{
    /// <summary>
    /// bố trí cho chú nào gọi hàm này vào chơi trước, sau đó cố gắng phân bố 4n+3 người vào n sới
    /// </summary>
    public class user_add_auto : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            #region Chơi sới khác rồi thì mời lượn            
            
            if (security.CurrentAU.CurrentSoi != null)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, 
                    "JOIN_SOI", "Bạn đã gia nhập sới khác rồi");
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }

            #endregion

            #region ID Giải đấu sai, hoặc giải đã kêt thúc, không cho chơi
            
            string tournamentid = APIParamHelper.GetParam("tournamentid", context);
            tournamentDTO tournament = Song.Instance.GetTournamentByID(tournamentid);
            if (tournament == null)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "NOT_FOUND", "Không tìm thấy giải đấu");
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }            

            // hết thời gian chơi
            if (((DateTime)tournament.endtime).CompareTo(DateTime.Now) < 0)
            {
                APICommandStatus cs = APICommandStatus.Get_NOT_VALID_CommandStatus();
                cs.Info = "Hiện tại giải đấu đã kết thúc, không cho chơi nữa";
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }

            #endregion



            /// tìm danh sách các sới của tour hiện tại, còn chỗ, chưa chơi
            List<Soi> arrAvailableSoiOfTour = Song.Instance.GetSoiByTournamentID(tournamentid).
                Where(soi => soi.SeatList.Count < 4 && soi.IsPlaying == false).
                OrderBy(soi => Guid.NewGuid()).
                ToList();


            #region Giải quyết riêng trường hợp FREE TOUR
            if (tournament.id == (int)TournamentType.Free)
            {
                Soi soiFreeToAdd = null;
                if (arrAvailableSoiOfTour.Count > 0)
                {
                    soiFreeToAdd = arrAvailableSoiOfTour.First();
                    soiFreeToAdd.AddPlayer(security.CurrentAU);
                }
                else
                {
                    soiFreeToAdd = Song.Instance.CreatNewFreeSoi(security.CurrentAU.Username, security.CurrentAU.Username);
                }
                
                APICommandStatus cs = new APICommandStatus(true, "JOIN_SOI", "Đã gia nhập sới " + soiFreeToAdd.ID);
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }
            #endregion 
            
            
            List<string> arrWaitingListCurrentTour = Song.Instance.DicTournamentWaitingList[tournament.id];            
            
            // Add currentAU vào danh sách chờ
            arrWaitingListCurrentTour.Add(security.CurrentAU.Username);


            // xử lý người chờ trong danh sách chờ
            /// nhét vào các sới còn trống trước
            /// sau đó nếu danh sách chờ vẫn còn, nếu còn > 6 thì tạo sới mới nhét vào
            
                List<TalaUser> arrUserWaitingInThisTournament = Song.Instance.DicOnlineUser.Values.
                Where(
                    user => user.CurrentSoi == null  /* chưa chơi*/ &&
                    user.AttendingTournament.Any(tour => tour.id == tournament.id /* đã đăng ký tour này*/) &&
                    arrWaitingListCurrentTour.Contains(user.Username)  /* đã nằm trong danh sách chờ */
                ).
                OrderBy(user => Guid.NewGuid()) /* random sort*/
                .ToList();

                Soi soiConCho = null;
                if (arrAvailableSoiOfTour.Count > 0)
                {
                    soiConCho = arrAvailableSoiOfTour.First();
                    if (soiConCho.SeatList.Count < 4)
                    {
                        // còn chỗ
                        soiConCho.AddPlayer(userWillBeAdd);
                    }

                    // nhét xong kiểm lại
                    if (soiConCho.SeatList.Count == 4)
                    {
                        // nhét vào đầy ứ rồi
                        arrAvailableSoiOfTour.Remove(soiConCho);
                        soiConCho.Autorun();
                    }
                }
                else
                {
                    // vào đến đây là danh sách sới cũ còn chỗ đã hết
                    // chả còn sới nào mà nhét nữa rồi, tạo mới để nhét
                    if (soiConCho == null || soiConCho.SeatList.Count >= 4)
                    {

                    }
                    soiConCho.AddPlayer(userWillBeAdd);
                }


                int nNumOfSoiToCreate = 0;
                if (arrUserWaitingInThisTournament.Count > 6)
                {
                    nNumOfSoiToCreate = arrUserWaitingInThisTournament.Count / 4;
                }

                for (int i = 0; i < nNumOfSoiToCreate; i++)
                {
                    Soi soiConCho = Song.Instance.CreatNewSoiOfTour("Tala", tournament.id);
                    var Take4Users = arrUserWaitingInThisTournament.Take(4);
                    foreach (TalaUser userWillBeAdd in Take4Users)
                    {

                    }   // end for each 4 User
                }



            // đến đây là xong quá trình tự tìm và phân bố, nếu nhét vào sới pro thành công thì báo cho biết
            // nếu không thể nhét được thì báo cho chờ
            if (security.CurrentAU.CurrentSoi != null)
            {
                // TODO: gỡ khỏi danh mục chờ

                APICommandStatus cs = new APICommandStatus(true, "JOIN_SOI", "Gia nhập sới thành công");
                Cmd.Add(cs);
            }
            else
            {
                // TODO: ấn vào danh mục chờ


                APICommandStatus csWAIT = new APICommandStatus(true, "WAIT", "Số người chơi chưa đủ để thành lập sới. Xin hãy chờ đợi để chúng tôi thu xếp");
                Cmd.Add(csWAIT);
            }
            
            base.ProcessRequest(context);
        }
    }
}
