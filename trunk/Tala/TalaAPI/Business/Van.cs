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
using TalaAPI.Exception;

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
        internal void ChiaBai()
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

            /*chuyển turn sang next seat*/
            this.CurrentTurnSeatIndex = Seat.GetNextSeatIndex(seat.Index, this.Soi.SeatList.Count);
            
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
            int previousSeatIndex = Seat.GetPreviousSeatIndex(seat.Index, this.Soi.SeatList.Count);
            Seat previousSeat = this.Soi.SeatList.ElementAt(previousSeatIndex);
            
            /*kiểm tra previousSeat có bài đã đánh không*/
            if (previousSeat.BaiDaDanh == null || previousSeat.BaiDaDanh.Count == 0)
            {
                return false;
            }

            /*kiểm tra cây chốt*/
            if (previousSeat.BaiDaDanh.Count == 3 && this.Soi.SoiOption.IsChot)
            {
                ///TODO: chuyen tien cho cay chot
            }

            /*lấy cây vừa đánh của seat trước chuyển sang BaiDaAn của seat */
            Card anCard = previousSeat.BaiDaDanh.Last();
            seat.BaiDaAn.Add(anCard);
            previousSeat.BaiDaDanh.Remove(anCard);

                        
            /*nếu seat ăn có haIndex trước khi ăn != 1 thì sẽ phải xếp lại các BaiDaDanh trên sới*/
            if (seat.HaIndex != 1)
            {
                /*nếu haIndex = 2 ăn thì chuyển bài đã đánh từ haIndex 0 sang haIndex 1*/
                /*nếu haIndex = 3 ăn thì chuyển bài đã đánh từ haIndex 0 sang haIndex 2*/
                /*nếu haIndex = 0 ăn thì chuyển bài đã đánh từ haIndex 0 sang haIndex 3*/            
                int indexChuyenSang = Seat.GetPreviousSeatIndex(seat.HaIndex, this.Soi.SeatList.Count);
                Seat seat0 = this.Soi.SeatList.ElementAt(0);
                Seat seatI = this.Soi.SeatList.ElementAt(indexChuyenSang);
                Card chuyenCard = seat0.BaiDaDanh.Last();
                seatI.BaiDaDanh.Add(chuyenCard);
                seat0.BaiDaDanh.Remove(chuyenCard);                
            }

            /*cập nhật lại thứ tự hạ cho tất cả các seat*/
            foreach (Seat tmpSeat in this.Soi.SeatList)
            {
                tmpSeat.HaIndex = Seat.GetPreviousSeatIndex(tmpSeat.HaIndex, this.Soi.SeatList.Count);
            }

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
            if (!this.IsSeatInTurn(seat) || phomArr == null || phomArr.Count == 0)
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

        /// <summary>
        /// Hạ một mảng các mảng Card 
        /// </summary>
        /// <param name="seat">seat ra lệnh hạ phỏm</param>
        /// <param name="phomArr">một mảng các mảng Card để trở thành tập các Phom</param>
        /// <returns>true/false</returns>
        /// 
        public bool Ha(Seat seat, List<Card[]> phomArr)
        {
            //TODO can thay the dieu kien seat.BaiDaDanh.Count < 3
            if (!this.IsSeatInTurn(seat) || phomArr == null || phomArr.Count == 0 || seat.BaiDaDanh.Count < 3)
            {
                return false;
            }
            
            List<Phom> phomList = new List<Phom>();            
            List<Card> cardList = new List<Card>();
            int i = 0;
            /*kiem tra tinh chinh xac cua phom*/
            foreach (Card[] cardArr in phomArr)
            {
                /*kiểm tra card trong cardArr có thuộc BaiTrenTay hoặc BaiDaAn của seat không*/
                foreach (Card card in cardArr)
                {
                    cardList.Add(card);
                    if (!seat.BaiTrenTay.Contains(card) && !seat.BaiDaAn.Contains(card))
                    {
                        return false;
                    }
                }
                i++;
                Phom phom = cardArr.IsValidPhom();
                if (phom == null)
                {
                    return false;
                }
                /*set thêm các property cho phom*/
                phom.OfSeat = seat;
                /*một seat có tối đa 3 phỏm, set id cho phỏm để phỏm id là duy nhất trong 1 sới*/
                phom.Id = seat.Index * 3 + i; 
                phomList.Add(phom);
            }
            /*set phom cho seat*/
            seat.PhomList = phomList;

            /*remove Card ở BaiTrenTay và BaiDaAn của seat*/            
            foreach (Card card in cardList)
            {
                if (seat.BaiTrenTay.Contains(card))
                {
                    seat.BaiTrenTay.Remove(card);
                }
                if (seat.BaiDaAn.Contains(card))
                {
                    seat.BaiDaAn.Remove(card);
                }
            }           

            return true;
        }

        /// <summary>
        /// Gửi 1 mảng card vào phỏm có id = phomID
        /// </summary>
        /// <param name="seat">seat ra lệnh gửi</param>
        /// <param name="phomID">id của phỏm đc gửi</param>
        /// <param name="cardArr">các cây gửi vào phỏm</param>
        /// <returns>true/false</returns>
        public bool Gui(Seat seat, int phomID, Card[] cardArr)
        {
            /*kiểm tra phomID có tồn tại không*/
            Phom phom = this.GetPhomByID(phomID);
            if (phom == null)
            {
                return false;
            }

            /*kiểmm tra cardArr có nằm trên BaiTrenTay của seat không*/
            foreach (Card card in cardArr)
            {
                if (!this.IsCardInBaiTrenTay(seat, card))
                {
                    return false;
                }
            }            

            /**chỉ được gửi khi BaiDaDanh của seat có từ 3 cây trở lên             
             * seat có haIndex lớn hơn mới đc gửi vào phỏm của seat có haIndex nhỏ hơn             
             */
            if (seat.BaiDaDanh.Count < 3 || seat.HaIndex <= phom.OfSeat.HaIndex)
            {
                return false;
            }

            /*kiểm tra cardArr có tạo phỏm với phom không*/
            Card[] tmpCardArr = new Card[cardArr.Length + phom.CardArr.Length];
            phom.CardArr.CopyTo(tmpCardArr, 0);
            cardArr.CopyTo(tmpCardArr, phom.CardArr.Length);
            Phom tmpPhom = tmpCardArr.IsValidPhom();
            if (tmpPhom == null)
            {
                return false;
            }
            /*cập nhật phỏm sau khi đc gửi*/
            phom = tmpPhom;
            /*remove các cây đã gửi khỏi BaiTrenTay của seat*/
            foreach (Card card in cardArr)
            {
                seat.BaiTrenTay.Remove(card);
            } 
            
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
                throw new NotInTurnException("seat " + seat.Index + " is not in turn");
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

        /// <summary>
        /// Get phỏm trong Ván theo phomID
        /// </summary>
        /// <param name="phomID"></param>
        /// <returns>phom hoặc null nếu phomID không tồn tại</returns>
        private Phom GetPhomByID(int phomID)
        {            
            foreach (Seat seat in this.Soi.SeatList)
            {
                List<Phom> phomList = seat.PhomList;
                foreach (Phom phom in phomList)
                {
                    if (phom.Id == phomID)
                    {
                        return phom;
                    }
                }
            }
            return null;
        }


        


    }
}
