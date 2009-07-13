using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Xml.Linq;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using System.Text;
using Quantum.Tala.Service.Exception;

namespace Quantum.Tala.Service
{
    public class AutorunService
    {
        HttpContext _context;        
        TalaUser _CurrentAU;
        
        /// <summary>
        /// Autorun hoạt động dựa trên context hiện tại (để truy xuất Cache) và currentAU (để detect ván đang chơi, xử lý trên ván đang chơi)
        /// </summary>
        /// <param name="context"></param>
        public AutorunService(HttpContext context, TalaUser CurrentAU)
        {            
            _context = context;
            _CurrentAU = CurrentAU;

            if (_CurrentAU == null)
            {
                throw new BusinessException("Autorun","không có authkey, không xác định đc các thông số để autorun");
            }
        }


        

        public string Autorun_InVan()
        {
            // TODO: mỗi hành động tự làm, đều append vào sRet
            StringBuilder sRet = new StringBuilder();
                        
            // tìm seat đang đến lượt hiện tại
            Seat currentInTurnSeat = _CurrentAU.CurrentSoi.GetSeatOfCurrentInTurn();
            TalaUser currentInTurnPlayer = currentInTurnSeat.Player;
            /// kiểm tra với Volatine Repository (ở đây dùng luôn ASP.NET Cache)
            string sCacheKey = AutorunService.GetCacheKey_Autorun_InVan(currentInTurnPlayer);
            object oInCache = _context.Cache[sCacheKey];
            /// oInCache mà null là user đang có turn đã hết vị, timeout
                        
            /// if inturn player, on time fire
            if (oInCache == null)
            {
                sRet.Append(currentInTurnPlayer.Username + " hết thời gian nghĩ. Hệ thống tự chơi");

                //xem bài có mấy cây                
                if (currentInTurnSeat.GetTotalCardOnSeat() == 9)
                {
                    // TODO: 9 cây, chưa bốc, tự bốc
                    Autorun_Boc();
                }
                else if (currentInTurnSeat.GetTotalCardOnSeat() == 10)
                {
                    // 10 cây, bốc nhưng chưa đánh, tự đánh
   
                    // bài đã đánh có 3 cây, phải hạ hộ, gửi hộ
                    if(currentInTurnSeat.BaiDaDanh.Count == 3)
                    {                        
                        Autorun_Ha();
                        Autorun_Gui();
                    }
                    
                    Autorun_Danh();                    
                }
                else
                {
                    throw new NotImplementedException("Developer chưa xử lý trường hợp này, vào code sửa ngay");
                }                
            }
            
            /// trả lại chuỗi kết quả ra ngoài
            /// có thể để còn log

            return sRet.ToString();
        }

        private string Autorun_Boc()
        {
            throw new NotImplementedException();
        }

        private string Autorun_Danh()
        {
            // TODO: đánh tránh những cây trong phỏm đã ăn, nếu có thể tránh
            // TODO: //tránh cạ, nếu có thể tránh
            // TODO: //đánh cây to nhất ok, nếu ko tránh đc, vẫn đánh cây to nhất                    
            throw new NotImplementedException();
        }

        private string Autorun_Gui()
        {
            throw new NotImplementedException();
        }

        private string Autorun_Ha()
        {
            throw new NotImplementedException();
        }


        
        public string Autorun_InStartingVan()
        {
            // TODO: mỗi hành động tự làm, đều append vào sRet
            StringBuilder sRet = new StringBuilder();

            // chơi rồi thì thôi, ko chạy hàm này nữa
            if(_CurrentAU.CurrentSoi.IsPlaying == true)
            {
                return sRet.ToString();
            }


            // cập nhật cache cho currentAU (vì họ có hoạt động thật, họ đang chờ chơi thật)
            string sCacheKey = AutorunService.GetCacheKey_Autorun_InStartingVan(_CurrentAU);
            object oTemp = _context.Cache[sCacheKey];

            foreach (Seat seat in _CurrentAU.CurrentSoi.SeatList)
            {
                if (seat.Player.Username == _CurrentAU.Username)
                {
                    // bỏ qua user hiện đang tạo request, vì họ đang tham gia vào sới thật
                    continue;
                }

                string sAutorunRet = Autorun_DuoiKhoiSoi();
                sRet.Append(sAutorunRet);
            }

            return sRet.ToString();
        }


        private string Autorun_DuoiKhoiSoi()
        {
            throw new NotImplementedException();
        }




        const string AUTORUN_IN_VAN_KEY_PREFIX = "AUTORUN_VAN_";
        const string AUTORUN_IN_STARTING_VAN_KEY_PREFIX = "AUTORUN_VAN_STARTING_";

        public static string GetCacheKey_Autorun_InVan(TalaUser currentInTurnPlayer)
        {
            return AUTORUN_IN_VAN_KEY_PREFIX + "#" + currentInTurnPlayer.Username + "#" + currentInTurnPlayer.CurrentSoi.ID;
        }

        public static string GetCacheKey_Autorun_InStartingVan(TalaUser currentInTurnPlayer)
        {
            return AUTORUN_IN_STARTING_VAN_KEY_PREFIX + "#" + currentInTurnPlayer.Username + "#" + currentInTurnPlayer.CurrentSoi.ID;
        }

    }
}
