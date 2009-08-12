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
using System.Collections.Generic;

namespace Quantum.Tala.Service
{
    /// <summary>    
    /// Autorun hoạt động dựa trên context hiện tại (để truy xuất Cache) và currentAU (để detect ván đang chơi, xử lý trên ván đang chơi)       
    /// </summary>
    public class AutorunService
    { 
        const string AUTORUN_IN_VAN_KEY_PREFIX = "AUTORUN_VAN_";
        const string AUTORUN_IN_STARTING_VAN_KEY_PREFIX = "AUTORUN_VAN_STARTING_";

        const int AUTORUN_IN_STARTING_VAN_TIMEOUT = 89;
        const int AUTORUN_IN_VAN_TIMEOUT = 68;
        


        public AutorunService()
        { }


        public static string GetCacheKey_Autorun_InVan(TalaUser currentInTurnPlayer)
        {
            return AUTORUN_IN_VAN_KEY_PREFIX + "#" + currentInTurnPlayer.Username + "#" + currentInTurnPlayer.CurrentSoi.ID;
        }

        public static string GetCacheKey_Autorun_InStartingVan(TalaUser currentInTurnPlayer)
        {
            return AUTORUN_IN_STARTING_VAN_KEY_PREFIX + "#" + currentInTurnPlayer.Username + "#" + currentInTurnPlayer.CurrentSoi.ID;
        }


        /// <summary>
        /// hàm này nên chạy sau khi đã add user thành công vào sới
        /// </summary>
        /// <param name="player"></param>
        public static void Create_Autorun_InStartingVan(TalaUser player)
        {            
            string sCacheKey = AutorunService.GetCacheKey_Autorun_InStartingVan(player);
            // cache.Insert là replace key cũ, nếu key cũ tồn tại rồi

            // create cache dạng timeout tính từ last recent use
            HttpContext.Current.Cache.Insert(
                sCacheKey, player, 
                null, 
                DateTime.MaxValue, TimeSpan.FromSeconds(AutorunService.AUTORUN_IN_STARTING_VAN_TIMEOUT)
                );
        }
        
        /// <summary>
        /// ghi một key vào cache, khởi tạo Đồng hồ đếm ngược khi user có lượt trong khi ván đang chơi. Hàm này  gọi khi chuyển lượt.
        /// </summary>
        /// <param name="soi"></param>
        public static void Create_Autorun_InVan(TalaUser player)
        {   
            int nTimeout = player.CurrentSoi.SoiOption.TurnTimeout;
            // timeout do người chơi có thể set lại trong sới, tuy nhiên không được nhanh quá, ko được nhỏ hơn giá trị default, cũng như không lâu quá 2 lần giá trị default
            nTimeout = (AutorunService.AUTORUN_IN_VAN_TIMEOUT < nTimeout) && (nTimeout < AutorunService.AUTORUN_IN_VAN_TIMEOUT * 3)
                ? nTimeout : AutorunService.AUTORUN_IN_VAN_TIMEOUT;

            string sCacheKey = AutorunService.GetCacheKey_Autorun_InVan(player);
            // cache.Insert là replace key cũ, nếu key cũ tồn tại rồi
            // create dạng absolute expiration, hết hạn tại thời điểm cố định trong tương lai
            HttpContext.Current.Cache.Insert(
                sCacheKey, player,
                null,
                DateTime.Now.AddSeconds(nTimeout), TimeSpan.Zero
                );
        }

        
        
        public static string Check_Autorun_InStartingVan(Soi soi)
        {
            if(soi.IsPlaying == true)
            {
                return string.Empty;
            }

            HttpContext context = HttpContext.Current;
            string _CurrentAuthkey = context.Request["authkey"].ToStringSafetyNormalize();

            List<string> arrPlayerTimeoutNeedToRemove = new List<string>();
            foreach (Seat seat in soi.SeatList)
            {
                TalaUser player = seat.Player;
                string sCacheKey = GetCacheKey_Autorun_InStartingVan(player);                

                if (player.Authkey == _CurrentAuthkey)
                {
                    // nếu ai vào rồi thì gia hạn cache, create lại timer cho nó thôi
                    Create_Autorun_InStartingVan(player);
                }
                else
                {
                    // kiểm tra, chơi luôn mấy thằng khác hộ cho Quantum
                    if (seat.IsReady == false && context.Cache[sCacheKey] == null)
                    {
                        // đã vào sới rồi, mà ko chịu ready, cachekey kô tồn tại nghĩa là đã timeout
                        
                        // đánh dấu ghi tên lại để đuổi
                        arrPlayerTimeoutNeedToRemove.Add(player.Username);
                    }
                }
            }   // end foreach


            foreach (string sToRemove in arrPlayerTimeoutNeedToRemove)
            {
                soi.RemovePlayer(sToRemove);
            }

            // TODO: add progress info of autorun process here
            return string.Empty;
        }
                
        public static string Check_Autorun_InVan(Soi soi)
        {
            StringBuilder sRet = new StringBuilder();

            if(soi.IsPlaying == false)
            {
                return sRet.ToString();
            }

            HttpContext context = HttpContext.Current;

            string _CurrentAuthkey = context.Request["authkey"].ToStringSafetyNormalize();            
            Seat seatInTurn = soi.GetSeatOfCurrentInTurn();
            // kiểm tra thằng đang có lượt                
            if (seatInTurn.Player.Authkey == _CurrentAuthkey)
            {
                // currentAU đang có lượt (ko cập nhật lại timeout, ko đánh nhanh thì thiệt), do Bizz
                // cho qua, ko đụng đậy tay chân gì, ghi nhận là đồng chí có đang connected thôi
                seatInTurn.IsDisconnected = false;
            }                        
                       
            // Kiểm tra timeout
            string sCacheKey = GetCacheKey_Autorun_InVan(seatInTurn.Player);
            if (context.Cache[sCacheKey] == null)
            {
                // TIMEOUT
                seatInTurn.IsDisconnected = true;

                /* auto play */
                sRet.Append(seatInTurn.Player.Username + " hết thời gian nghĩ. Hệ thống tự chơi");                
                
                //xem bài có mấy cây
                if (seatInTurn.GetTotalCardOnSeat() == 9)
                {
                    // 9 cây mà lại đang có lượt,  tức là chưa bốc, tự bốc
                    Autorun_Boc(soi);
                }
                else if (seatInTurn.GetTotalCardOnSeat() == 10)
                {
                    // 10 cây, có lượt, tức là bốc nhưng chưa đánh, tự đánh

                    // bài đã đánh có 3 cây, phải hạ hộ, gửi hộ
                    if (seatInTurn.BaiDaDanh.Count == 3)
                    {
                        Autorun_Ha(soi);
                        Autorun_Gui(soi);
                    }

                    Autorun_Danh(soi);
                }
                else
                {
                    throw new NotImplementedException("Developer chưa xử lý trường hợp này, vào code sửa ngay");
                }
            }
            else
            {
                // nếu không timeout, bỏ qua luôn, ở ngoài sẽ báo lỗi Not In Turn cho currentAU
            }                        
            
            // trả lại chuỗi kết quả ra ngoài
            // có thể để còn log
            return sRet.ToString();
        }



        

        private static string Autorun_Boc(Soi soi)
        {
            string sRet = string.Empty;
            if (null != soi.CurrentVan)
            {
                Card cardBocDuoc = soi.CurrentVan.Boc(soi.GetSeatOfCurrentInTurn());
                if(null != cardBocDuoc)
                {
                    sRet = cardBocDuoc.ToString();
                }
            }
            return sRet;
        }

        private static string Autorun_Danh(Soi soi)
        {            
            string sRet = string.Empty;
            if (null != soi.CurrentVan)
            {
                Seat seat = soi.GetSeatOfCurrentInTurn();
                
                // sắp xếp bài trên tay theo thứ tự to đến bé
                // duyệt từng cây to, nếu không dính phỏm thì đánh luôn
                                           
                // duyệt đến cuối mà không tìm được con nào để đánh vì dính phỏm cả
                /// đánh cây to nhất nếu cuối cùng tìm ko được            
                Card cardCanDanh = null;
                seat.BaiTrenTay.Sort();
                for (int i = 0; i < seat.BaiTrenTay.Count; i++)
                {
                    // pick ra cây to nhất
                    cardCanDanh = seat.BaiTrenTay[i];
                    if (InspectPhomOfCard(cardCanDanh, seat.BaiTrenTay.Union(seat.BaiDaAn).ToList()) == null)
                    {
                        // cây này có dính với phỏm, bỏ qua
                        if (i == seat.BaiTrenTay.Count - 1)
                        {
                            // tìm tới cuối bài mà con nào cũng dính phỏm cả, (vô lý, thực ra ko sẽ ít có case này do đây là ù)
                            // thì ù thử phát, không được thì
                            // thôi thì rút cây to nhất ra phang
                            if (soi.CurrentVan.U(seat))
                            {
                                return sRet;
                            }
                            else
                            {
                                cardCanDanh = seat.BaiTrenTay.First();
                            }
                        }                        
                    }
                    else
                    {
                        // cây này không dính phỏm, oánh lun
                        break;
                    }                    
                }

                soi.CurrentVan.Danh(seat, cardCanDanh);
            }
            return sRet;
        }

        private static string Autorun_Gui(Soi soi)
        {
            string sRet = string.Empty;
            if (null != soi.CurrentVan)
            {
                Seat seat = soi.GetSeatOfCurrentInTurn();
                // TODO: Gửi hộ: soi.CurrentVan.Gui();
            }
            return sRet;
        }

        private static string Autorun_Ha(Soi soi)
        {
            string sRet = string.Empty;
            if (null != soi.CurrentVan)
            {
                Seat seat = soi.GetSeatOfCurrentInTurn();
                
                // Hạ hộ                
                if (seat.BaiDaAn.Count > 0)
                {
                    // ăn dăm ba cây rồi, cố hạ nốt giúp nó phát
                    List<Card[]> arrPhomListForHa = new List<Card[]>();
                    
                    // TODO: lan trong đám bài, tìm phỏm, ấn vào arPhomList.
                    // tạm thời lan ngu dốt đã, chưa thông minh vội
                    foreach (Card cardDaAn in seat.BaiDaAn)
                    {
                        List<Card> phomPotential = InspectPhomOfCard(cardDaAn, seat.BaiTrenTay);
                        if (null != phomPotential && phomPotential.Count >= 3)
                        {
                            arrPhomListForHa.Add(phomPotential.ToArray());
                        }
                    }

                    if(arrPhomListForHa.Count > 0)
                    {
                        soi.CurrentVan.Ha(seat, arrPhomListForHa);
                    }
                }
            }
            return sRet;
        }

        private static bool Autorun_U(Soi soi)
        {
            bool bRet = false;

            if (null != soi.CurrentVan)
            {
                Seat seat = soi.GetSeatOfCurrentInTurn();
                return soi.CurrentVan.U(seat);                
            }

            return bRet;
        }


        /// <summary>
        /// tìm trong bộ bài cardListToLookup, xem cây truyền vào có tạo phỏm được với các cây khác không
        /// </summary>
        /// <param name="cardAnchor"></param>
        /// <param name="cardListToLookup"></param>
        /// <returns></returns>
        public static List<Card> InspectPhomOfCard(Card cardAnchor, List<Card> cardListToLookup)
        {            
            List<Card> phomPotential = new List<Card>();
            phomPotential.Add(cardAnchor);

            // thử tìm phỏm ngang
            foreach (Card card in cardListToLookup)
            {
                if (cardAnchor == card)
                {
                    continue;   // trong trường hợp card cần theo dõi 
                }

                if (cardAnchor.So == card.So)
                {
                    phomPotential.Add(card);
                }
            }
            if (phomPotential.Count >= 3)
            {                
            }
            else
            {
                phomPotential.Clear();
                phomPotential.Add(cardAnchor);
            }
            
            // reset, tìm lại với phỏm dọc
            foreach (Card card in cardListToLookup)
            {
                if (cardAnchor.Chat == card.Chat)
                {
                    int nSoCardDaAn = int.Parse(cardAnchor.So);
                    int nSoCard = int.Parse(card.So);

                    if (nSoCardDaAn - 1 == nSoCard || nSoCardDaAn + 1 == nSoCard ||
                        nSoCardDaAn - 2 == nSoCard || nSoCardDaAn + 2 == nSoCard
                        )
                    {
                        phomPotential.Add(card);
                    }
                }
            }

            if (phomPotential.Count >= 3)
            {
                return phomPotential;
            }
            else
            {
                return null;
            }            
        }
        
    }
}
