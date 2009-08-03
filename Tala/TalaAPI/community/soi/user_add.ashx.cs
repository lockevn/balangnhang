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
    public class user_add : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);
            
            string soiid = APIParamHelper.GetParam("soiid", context);
            Soi soi = Song.Instance.GetSoiByID(soiid);
            if (soi == null)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "SOI_NOT_FOUND", "không tìm thấy sới");
                Cmd.Add(cs);
                base.ProcessRequest(context);
            }

            soi.Autorun();
            
            tournamentDTO tour = soi.GetCurrentTournament();
            if (tour.type != 1)
            {
                // không phải free tour, không cho vào tự do, đuổi luôn
                APICommandStatus cs = APICommandStatus.Get_NOT_ALLOW_CommandStatus();
                cs.Info += string.Format("Tournament {0}:{1} không cho phép vào sới tự do", tour.id, tour.name);
                Cmd.Add(cs);
                base.ProcessRequest(context);
                return;
            }


            string pu = context.Request["pu"].ToStringSafetyNormalize();
            if (pu.IsNullOrEmpty())
            {
                #region // AU tự add mình vào, tự join Sới

                int sResult = soi.AddPlayer(security.CurrentAU.Username);
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
                        case -4:
                            cs.ID = "NOT_ALLOW";
                            cs.Info = "sới này đang chơi rồi, bạn chỉ được vào xem, không gia nhập được";
                            break;
                    }
                    Cmd.Add(cs);
                }

                #endregion
            }
            else
            {
                #region // player mời người khác vào chơi

                TalaUser usertoadd = Song.Instance.GetUserByUsername(pu);
                if (usertoadd == null)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "JOIN_SOI", "Người bạn mời đã rời mạng");
                    Cmd.Add(cs);
                }
                else
                {
                    int nResult = soi.AddPlayer(usertoadd.Username);
                    if (nResult >= 0)
                    {
                        APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Mời gia nhập sới thành công");
                        Cmd.Add(cs);
                    }
                    else
                    {
                        APICommandStatus cs = new APICommandStatus(false);

                        switch (nResult)
                        {
                            case -1:
                                cs.ID = "SOI_FULL_PLAYER";
                                cs.Info = "Sới đầy rồi nhé";
                                break;
                            case -3:
                                cs.ID = "GUEST_PLAYER_IS_PLAYING";
                                cs.Info = "Người bạn mời đã ngồi ở sới khác rồi";
                                break;
                            case -4:
                                cs.ID = "NOT_ALLOW";
                                cs.Info = "sới này đang chơi rồi, bạn chỉ được vào xem, không gia nhập được";
                                break;
                        }

                        Cmd.Add(cs);
                    }
                }

                #endregion
            }


            base.ProcessRequest(context);
        }
    }
}
