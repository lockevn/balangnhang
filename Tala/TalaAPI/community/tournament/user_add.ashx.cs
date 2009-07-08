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

namespace TalaAPI.community.tournament
{
    public class user_add : XMLHttpHandler
    {

        public override void ProcessRequest(HttpContext context)
        {
            TalaSecurity security = new TalaSecurity(context);

            // TODO: nghiệp vụ một user đóng tiền tham gia tournament
            /// Kiểm tra lệ phí tối thiểu, thông tin của giải, đã kết thúc chưa ...
            /// Kiểm tra tiền User
            /// Nếu đủ tiền tham gia
            /// Trừ tiền user, gọi sang nghiệp vụ VTC VCoin
            /// Ghi vào bản danh sách đăng ký tham gia giải
            /// Cấp point khởi động cho user
            /// log các hành vi giao dịch tiền, tham gia, ...

            //string pu = context.Request["pu"].ToStringSafetyNormalize();
            //string soiid = context.Request["soiid"].ToStringSafetyNormalize();

            //Soi soi = Song.Instance.GetSoiByID(soiid);
            //if (soi == null)
            //{
            //    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "SOI_NOT_FOUND", "không tìm thấy sới");
            //    Cmd.Add(cs);
            //}
            //else
            //{
            //    if (pu.IsNullOrEmpty())
            //    {
            //        // AU tự add mình vào, tự join Sới
            //        int sResult = soi.AddPlayer(security.CurrentAU.Username);
            //        if (sResult >= 0)
            //        {
            //            APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Gia nhập sới thành công");
            //            Cmd.Add(cs);
            //        }
            //        else
            //        {
            //            APICommandStatus cs = new APICommandStatus(false);
            //            switch (sResult)
            //            {
            //                case -1:
            //                    cs.ID = "SOI_FULL_PLAYER";
            //                    cs.Info = "Sới đầy rồi nhé";
            //                    break;
            //                case -2:
            //                    cs.ID = "NOT_VALID";
            //                    cs.Info = "User này chưa login";
            //                    break;
            //                case -3:
            //                    cs.ID = "NOT_ALLOW";
            //                    cs.Info = "Bạn đã vào sới khác rồi, không vào đây được nữa";
            //                    break;
            //            }
            //            Cmd.Add(cs);
            //        }
            //    }
            //    else
            //    {
            //        // player mời người khác vào chơi
            //        TalaUser usertoadd = Song.Instance.GetUserByUsername(pu);
            //        if (usertoadd == null)
            //        {
            //            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "JOIN_SOI", "Người bạn mời đã rời mạng");
            //            Cmd.Add(cs);
            //        }
            //        else
            //        {
            //            int nResult = soi.AddPlayer(usertoadd.Username);
            //            if (nResult >= 0)
            //            {
            //                APICommandStatus cs = new APICommandStatus(APICommandStatusState.OK, "JOIN_SOI", "Mời gia nhập sới thành công");
            //                Cmd.Add(cs);                            
            //            }
            //            else
            //            {
            //                APICommandStatus cs = new APICommandStatus(false);

            //                switch (nResult)
            //                {
            //                    case -1:
            //                        cs.ID = "SOI_FULL_PLAYER";
            //                        cs.Info = "Sới đầy rồi nhé";
            //                        break;
            //                    case -3:
            //                        cs.ID = "GUEST_PLAYER_IS_PLAYING";
            //                        cs.Info = "Người bạn mời đã ngồi ở sới khác rồi";
            //                        break;
            //                }

            //                Cmd.Add(cs);
            //            }
            //        }
            //    }
            //}

            base.ProcessRequest(context);
        }
    }
}
