using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;
using System.Collections.Generic;
using TalaAPI.Lib;

namespace TalaAPI.Business
{
    public class Van
    {
        public int Index;
        public int CurrentTurnSeatIndex;
        public bool IsFinished;
        public int CurrentRound;
        private Soi Soi;
        public List<Card> Noc;

        public Van(int index, Soi soi)
        {
            this.Index = index;
            this.Soi = soi;
            this.CurrentRound = 1;
            this.CurrentTurnSeatIndex = 0;
            
            this.InitializeNoc();
            this.IsFinished = false;
        }

        /// <summary>
        /// Khởi tạo Nọc randomly
        /// </summary>
        private void InitializeNoc()
        {
            this.Noc = new List<Card>();

            /*generate a temporary array of 52 elements*/
            int[] tmpArr = new int[52];
            for (int i = 0; i < 52; i++)
            {
                tmpArr[i] = i;
            }
            /*randomly reindexing the tmpArr*/
            int[] randomArr = TextUtil.ReindexArrayRandomly(tmpArr);

            /*randomly pick a Card in CARD_SET and add to Noc*/
            for (int i = 0; i < 52; i++)
            {
                this.Noc.Add(Card.CARD_SET[randomArr[i]]);
            }
        }

        /// <summary>
        /// Chia bài từ Nọc cho các seat
        /// </summary>
        public void ChiaBai()
        {
            if(this.Noc == null || this.Noc.Count != 52)
            {
                return;
            }
            int seatCount = this.Soi.SeatList.Count;
            for (int i = 0; i < 9; i++)
            {
                for (int j = 0; j < seatCount; j++)
                {
                    /*chia bai i+j cho seat[j]*/
                    this.Soi.SeatList[j].BaiTrenTay.Add(this.Noc.ElementAt(seatCount * i + j));
                }
            }
            /*chia them cho seat[0] 1 cay */
            this.Soi.SeatList[0].BaiTrenTay.Add(this.Noc.ElementAt(9 * seatCount));

            /*xoa cac cay da chia ra khoi Noc*/
            for (int i = 0; i < 9*seatCount + 1; i++)
            {
                this.Noc.RemoveAt(0);
            }
        }


        /// <summary>
        /// Đánh một cây trên BaiTrenTay của seat
        /// </summary>
        /// <param name="seat">seat ra lệnh đánh</param>
        /// <param name="card">cây được đánh</param>
        /// <returns>true/false</returns>
        public bool Danh(Seat seat, Card card)
        {
            if (!this.IsSeatInTurn(seat) || !this.IsCardInBaiTrenTay(seat, card))                                
            {
                return false;
            }

            /*chuyen card tu BaiTrenTay cua seat[i] sang BaiDaDanh cua seat[i]*/
            seat.BaiTrenTay.Remove(card);
            seat.BaiDaDanh.Add(card);
            
            return true;

        }

        /// <summary>
        /// Bốc 1 cây ở dưới nọc
        /// </summary>
        /// <param name="seat">seat ra lệnh bốc</param>
        /// <returns>true/false</returns>
        public bool Boc(Seat seat)
        {
            if (!this.IsSeatInTurn(seat) 
                || this.Noc.Count == 0 
                || (seat.BaiTrenTay.Count + seat.BaiDaAn.Count) > 9)
            {
                return false;
            }
            /*chuyển 1 cây ở Nọc lên BaiTrenTay của seat*/
            Card cardBoc = this.Noc.ElementAt(0);
            seat.BaiTrenTay.Add(cardBoc);
            this.Noc.RemoveAt(0);
            return true;
            
        }

        /// <summary>
        /// Ăn một cây đã đánh của seat ngồi trước
        /// </summary>
        /// <param name="seat">seat ra lệnh ăn</param>
        /// <returns>true/false</returns>
        public bool An(Seat seat)
        {
            if (!this.IsSeatInTurn(seat))
            {
                return false;
            }
            
            ///TODO kiem tra an 1 con tren cung 1 phom

            /*chuyen card cuoi cung tu BaiDaDanh cua seat truoc sang seat BaiDaAn cua seat sau*/
            int previousSeatIndex = seat.GetPreviousSeatIndex(this.Soi.SeatList.Count);
            Seat previousSeat = this.Soi.SeatList.ElementAt(previousSeatIndex);
            
            /*kiểm tra previousSeat có bài đã đánh không*/
            if (previousSeat.BaiDaDanh == null || previousSeat.BaiDaDanh.Count == 0)
            {
                return false;
            }

            Card anCard = previousSeat.BaiDaDanh.Last();
            seat.BaiDaAn.Add(anCard);
            previousSeat.BaiDaDanh.Remove(anCard);

            ///TODO: kiem tra xem co phai xoay bai tren soi khong, neu co thi xoay

            return true;
            
        }

        /// <summary>
        /// Ù
        /// </summary>
        /// <param name="seat">seat ra lệnh ù</param>
        /// <param name="phomArr">tập các phỏm để ù</param>
        /// <returns>true/false</returns>
        public bool U(Seat seat, List<Card[]> phomArr)
        {
            if (!this.IsSeatInTurn(seat))
            {
                return false;
            }
            /*kiem tra tinh chinh xac cua phom*/
            foreach(Card[] cardArr in phomArr)
            {
                if(cardArr.IsValidPhom() == null)
                {
                    return false;
                }
            }

            /*ket thuc van, set winner*/
            this.IsFinished = true;
            ///TODO:  set winner, money
            return true;
        }

        public bool Ha(Seat seat, Card[][] phomArr)
        {
            return true;
        }

        public bool Gui(Seat seat, Phom phom, Card[] cards)
        {
            return true;
        }

        
        /// <summary>
        /// Kiểm tra seat có đang giữ quyền đánh hay không
        /// </summary>
        /// <param name="seat"></param>
        /// <returns>true/false</returns>
        private bool IsSeatInTurn(Seat seat)
        {
            if (seat.Index != this.CurrentTurnSeatIndex)
            {
                return false;
            }
            return true;
        }

        /// <summary>
        /// Kiểm tra card có nằm trên BaiTrentay của seat hay không
        /// </summary>
        /// <param name="seat"></param>
        /// <param name="card"></param>
        /// <returns>true/false</returns>
        private bool IsCardInBaiTrenTay(Seat seat, Card card)
        {
            if (seat == null || seat.BaiTrenTay == null || seat.BaiTrenTay.Count == 0 || card == null || !seat.BaiTrenTay.Contains(card))
            {
                return false;
            }
            return true;
        }

        


    }
}
