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
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.Business
{
    public class Van : APIDataEntry
    {
        int _Index;
        [ElementXMLExportAttribute("id", DataOutputXMLType.NestedTag)]
        public int Index
        {
            get { return _Index; }
            set { _Index = value; }
        }
        
        int _CurrentTurnSeatIndex;
        public int CurrentTurnSeatIndex
        {
            get { return _CurrentTurnSeatIndex; }
            set { _CurrentTurnSeatIndex = value; }
        }
        
        public bool IsFinished { get; set; }
        public int CurrentRound { get; set; }
        public Soi SoiDangChoi { get; set; }
        internal List<Card> Noc { get; set; }

        public string WinnerUsername { get; set; }

        List<Message> _MessageList = new List<Message>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Message> MessageList
        {
            get { return _MessageList; }
            set { _MessageList = value; }
        }



        public Van(int index, Soi soi)
        {
            this.Index = index;
            this.SoiDangChoi = soi;
            this.CurrentRound = 1;
            this.CurrentTurnSeatIndex = 0;
            
            this.InitializeNoc();
            this.IsFinished = false;

            /*xóa bài của tất cả các seat trong seatList*/
            List<Seat> seatList = this.SoiDangChoi.SeatList;
            if (seatList != null)
            {
                foreach (Seat seat in seatList)
                {
                    seat.PhomList = new List<Phom>();
                    seat.BaiTrenTay = new List<Card>();
                    seat.BaiDaAn = new List<Card>();
                    seat.BaiDaDanh = new List<Card>();
                    seat.BaiDaGui = new List<Card>();                                        
                                        
                    /*reset SoCayGui*/
                    seat.SoCayGuiToiSeat = 0;
                }
            }
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
        /// for testing only
        /// </summary>
        //private void InitializeNoc()
        //{
        //    this.Noc = new List<Card>();

        //    for (int i = 0; i < 52; i++)
        //    {
        //        this.Noc.Add(Card.CARD_SET[i]);
        //    }
        //}


        /// <summary>
        /// Chia bài từ Nọc cho các seat, chia cho Winner của ván trước đầu tiên
        /// </summary>
        internal void ChiaBai(string p_sOldWinnerUsername)
        {
            if (this.Noc == null || this.Noc.Count != 52)
            {
                return;
            }            
            
            int nSeatCount = this.SoiDangChoi.SeatList.Count;
            for (int i = 0; i < 9; i++)
            {
                for (int j = 0; j < nSeatCount; j++)
                {
                    /*chia bai i+j cho seat[j]*/
                    this.SoiDangChoi.SeatList[j].BaiTrenTay.Add(this.Noc[nSeatCount * i + j]);
                }
            }

            Seat seatDanhDauTien = SoiDangChoi.GetSeatByUsername(p_sOldWinnerUsername);
            if (seatDanhDauTien == null)
            {
                seatDanhDauTien = SoiDangChoi.SeatList[0];
            }
            /*chia them cho seat đánh đầu tiên 1 cay */
            seatDanhDauTien.BaiTrenTay.Add(this.Noc[9 * nSeatCount]);

            /*xoa cac cay da chia ra khoi Noc*/
            for (int i = 0; i < 9*nSeatCount + 1; i++)
            {
                this.Noc.RemoveAt(0);
            }

            this.CurrentTurnSeatIndex = seatDanhDauTien.Pos;
        }        


        /// <summary>
        /// Đánh một cây trên BaiTrenTay của seat
        /// </summary>
        /// <param name="seat">seat ra lệnh đánh</param>
        /// <param name="card">cây được đánh</param>
        /// <returns>true/false</returns>
        public bool Danh(Seat seat, Card card)
        {
            if (!this.IsSeatInTurn(seat) || !this.IsCardInBaiTrenTay(seat, card) || seat.GetTotalCardOnSeat() < 10)                                
            {
                return false;
            }

            /*kiểm tra nếu đến lượt phải hạ phỏm, thằng này có ăn cây nào mà không hạ phỏm k*/
            if (seat.BaiDaDanh.Count == 3 && seat.BaiDaAn.Count > 0)
            {
                this.EndVan(seat);
                return true;
            }

            /*chuyen card tu BaiTrenTay cua seat[i] sang BaiDaDanh cua seat[i]*/
            seat.BaiTrenTay.Remove(card);
            seat.BaiDaDanh.Add(card);

            /*nếu sau khi đánh mà bài trên tay của seat count=0 --> ù thuong
             --> giải quyết trường hợp gửi xong còn 1 cây trên tay đánh nốt.
             */
            if (seat.BaiTrenTay.Count == 0)
            {
                /*cho seat U va endVan*/
                this.EndVan(seat, null, false);
                return true;
            }


            /*chuyển turn sang next seat*/
            this.CurrentTurnSeatIndex = Seat.GetNextSeatIndex(seat.Pos, this.SoiDangChoi.SeatList.Count);
            
            /*nếu là seat đánh cuối cùng ở vòng cuối cùng thì end game (kiểm tra qua Nọc)*/
            if (this.Noc.Count == 0)
            {
                this.EndVan();
            }
            return true;

        }

        /// <summary>
        /// Bốc 1 cây ở dưới nọc
        /// </summary>
        /// <param name="seat">seat ra lệnh bốc</param>
        /// <returns>true/false</returns>
        public Card Boc(Seat seat)
        {
            if (!this.IsSeatInTurn(seat) 
                || this.Noc.Count == 0 
                || seat.GetTotalCardOnSeat() > 9)
            {
                return null;
            }
            /*chuyển 1 cây ở Nọc lên BaiTrenTay của seat*/
            Card cardBoc = this.Noc.ElementAt(0);
            seat.BaiTrenTay.Add(cardBoc);
            this.Noc.RemoveAt(0);
            return cardBoc;
            
        }

        /// <summary>
        /// Ăn một cây đã đánh của seat ngồi trước
        /// </summary>
        /// <param name="seat">seat ra lệnh ăn</param>
        /// <returns>true/false</returns>
        public Card An(Seat seat)
        {
            if (!this.IsSeatInTurn(seat) || seat.GetTotalCardOnSeat() > 9)
            {
                return null;
            }

            /*chuyen card cuoi cung tu BaiDaDanh cua seat truoc sang seat BaiDaAn cua seat sau*/
            int previousSeatIndex = Seat.GetPreviousSeatIndex(seat.Pos, this.SoiDangChoi.SeatList.Count);
            Seat previousSeat = this.SoiDangChoi.SeatList.ElementAt(previousSeatIndex) as Seat;
            
            /*kiểm tra previousSeat có bài đã đánh không*/
            if (previousSeat.BaiDaDanh == null || previousSeat.BaiDaDanh.Count == 0)
            {
                return null;
            }

            /*kiểm tra cây chốt*/
            if (seat.BaiDaDanh.Count == 3 && this.SoiDangChoi.SoiOption.IsChot)
            {
                if (this.SoiDangChoi.SoiOption.IsChot)
                {
                    /* tinh tien cho cay chot */
                    int money = Cashier.CHIP_AN_CHOT * this.SoiDangChoi.SoiOption.TiGiaChip;
                    this.AddMessage("Ăn cây chốt", previousSeat.Player.Username + " nộp " + money + " cho " + seat.Player.Username);
                    /*tru tien vao tai khoan cua nguoi bi an chot*/
                    previousSeat.Player.SubtractMoney(money);
                    /*cong tien vao tai khoan cua nguoi an chot*/
                    seat.Player.AddMoney(money);
                }
            }

            /*nộp gà*/
            if (this.SoiDangChoi.SoiOption.IsGa)
            {
                int nTienPhatVaoGa = Cashier.NopGa(this.SoiDangChoi, previousSeat.Player);
                this.AddMessage("Nộp gà", previousSeat.Player.Username + " nộp " + nTienPhatVaoGa + " vào gà");                
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
                int indexChuyenSang = Seat.GetPreviousSeatIndex(seat.HaIndex, this.SoiDangChoi.SeatList.Count);
                Seat seat0 = this.SoiDangChoi.GetSeatByHaIndex(0) as Seat;
                Seat seatI = this.SoiDangChoi.GetSeatByHaIndex(indexChuyenSang) as Seat;
                Card chuyenCard = seat0.BaiDaDanh.Last();
                seatI.BaiDaDanh.Add(chuyenCard);
                seat0.BaiDaDanh.Remove(chuyenCard);                
            }

            /*cập nhật lại thứ tự hạ cho tất cả các seat*/
            foreach (Seat tmpSeat in this.SoiDangChoi.SeatList)
            {
                tmpSeat.HaIndex = Seat.GetPreviousSeatIndex(tmpSeat.HaIndex, this.SoiDangChoi.SeatList.Count);
            }

            

            return anCard;
            
        }               

        /// <summary>
        /// Ù
        /// </summary>
        /// <param name="seat">seat ra lệnh ù</param>
        /// <param name="phomArr">tập các phỏm để ù</param>
        /// <returns>true/false</returns>
        public bool U(Seat seat, List<Card[]> phomArr)
        {
            if (!this.IsSeatInTurn(seat) || phomArr == null || phomArr.Count == 0 || seat.GetTotalCardOnSeat() < 10)
            {
                return false;
            }
            int count = 0; /*dem tong so cay cua tat ca cac phom*/
            List<Phom> tmpPhomList = new List<Phom>();
            /*kiem tra tinh chinh xac cua phom*/
            foreach(Card[] cardArr in phomArr)
            {
                /*kiểm tra các cây ù có thuộc bài trên tay và bài đã ăn của seat không*/                
                foreach (Card card in cardArr)
                {
                    if (!seat.BaiTrenTay.Contains(card) && !seat.BaiDaAn.Contains(card))
                    {
                        return false;
                    }
                    count++;
                }

                Phom tmpPhom = cardArr.IsValidPhom();
                if ( tmpPhom == null)
                {
                    return false;
                }
                else
                {
                    tmpPhomList.Add(tmpPhom);
                }

            }

            /*nếu tổng tất cả các phỏm hạ không đủ 9 cây*/
            if (count < 9)
            {
                return false;
            }

            /*cap nhat thong tin phom cua seat vua u*/
            seat.PhomList = tmpPhomList;

            /*neu bai da an cua seat == 3, previous seat phai den*/
            if (seat.BaiDaAn.Count == 3)
            {
                int previousIndex = Seat.GetPreviousSeatIndex(seat.Pos, this.SoiDangChoi.SeatList.Count);
                Seat previousSeat = this.SoiDangChoi.SeatList.ElementAt(previousIndex) as Seat;
                /*kết thúc ván khi có thằng ù và có thằng phải đền
                 kiểm tra count để xác định ù tròn hay ù thường
                 */                
                this.EndVan(seat, previousSeat, count == 10);
                
            }
            else
            {
                /*nếu không ai phải đền, mỗi người nộp chip cho người ù
                 kiểm tra count để xác định ù tròn hay ù thường
                 */
                this.EndVan(seat, null, count == 10);

            }                        
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
            if (!this.IsSeatInTurn(seat) || phomArr == null || phomArr.Count == 0 || seat.BaiDaDanh.Count < 3 || seat.GetTotalCardOnSeat() < 10)
            {
                return false;
            }
                        
            List<Card> cardList = new List<Card>();
            List<Phom> phomList = new List<Phom>();
            int i = 0;
            /*kiem tra tinh chinh xac cua phom*/
            foreach (Card[] cardArr in phomArr)
            {
                
                foreach (Card card in cardArr)
                {
                    /*kiểm tra client hạ láo: trong phomArr có card trùng nhau*/
                    if(cardList.Contains(card))
                    {
                        return false;
                    }
                    /*kiểm tra client hạ láo: card trong cardArr có thuộc BaiTrenTay hoặc BaiDaAn của seat không*/
                    if (!seat.BaiTrenTay.Contains(card) && !seat.BaiDaAn.Contains(card))
                    {
                        return false;
                    }
                    cardList.Add(card);
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
                phom.Id = seat.Pos * 3 + i;
                phomList.Add(phom);
                
            }

            /*kiểm tra hạ láo --> đền, end van*/
            if (this.checkHaLao(seat, phomList))
            {                
                /*end van và đền*/
                this.EndVan(seat);
                return true;
            }
            
            /*cap nhat phomList vao seat.phomList*/
            foreach (Phom phom in phomList)
            {
                seat.PhomList.Add(phom);
            }

            /*remove Card ở BaiTrenTay và BaiDaAn của seat*/            
            foreach (Card card in cardList)
            {
                if (seat.BaiTrenTay.Contains(card))
                {
                    seat.BaiTrenTay.Remove(card);
                }
                /*Remove card khỏi bài đã ăn*/
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
            #region Exceptional flow, các trường hợp gửi không hợp lệ
                        
            /*kiểm tra phomID có tồn tại không*/
            Phom phom = this.GetPhomByID(phomID);
            if (phom == null)
            {
                return false;
            }

            /*móm không được gửi, do vậy chỉ được gửi nếu seat có phỏm (do đó phải hạ phỏm trước khi gửi)*/
            if (seat.PhomList == null || seat.PhomList.Count == 0)
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

            /// chỉ được gửi khi BaiDaDanh của seat có từ 3 cây trở lên (gửi xong mới đánh)
            if (seat.BaiDaDanh.Count < 3)
            {
                return false;
            }

            /// kiểm tra cardArr có tạo phỏm với phỏm cũ không
            /// Sau khi gửi vào, phỏm vẫn phải giữ nguyên tính chất là Phỏm
            Card[] tmpCardArr = new Card[cardArr.Length + phom.CardArray.Length];
            phom.CardArray.CopyTo(tmpCardArr, 0);
            cardArr.CopyTo(tmpCardArr, phom.CardArray.Length);
            Phom tmpPhom = tmpCardArr.IsValidPhom();
            if (tmpPhom == null)
            {
                return false;
            }

            #endregion


            /*cập nhật phỏm sau khi đc gửi*/
            phom.CardArray = new Card[cardArr.Length + phom.CardArray.Length];
            /*cập nhật số cây gửi của seat có phỏm đc gửi đến*/
            phom.OfSeat.SoCayGuiToiSeat += cardArr.Length;

            tmpCardArr.CopyTo(phom.CardArray, 0);
            /*remove các cây đã gửi khỏi BaiTrenTay của seat
             đồng thời thêm các cây này vào bài đã gửi của seat
             */
            foreach (Card card in cardArr)
            {
                seat.BaiTrenTay.Remove(card);
                seat.BaiDaGui.Add(card);
            } 

            /*nếu sau khi gửi mà bài trên tay count == 0 --> ù tròn*/
            if (seat.BaiTrenTay.Count == 0)
            {
                this.EndVan(seat, null, true);
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
            if (seat.Pos != this.CurrentTurnSeatIndex)
            {
                throw new NotInTurnException("seat " + seat.Pos + " is not in turn");
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
            if (seat == null 
                || seat.BaiTrenTay == null 
                || seat.BaiTrenTay.Count == 0 
                || card == null 
                || !seat.BaiTrenTay.Contains<Card>(card))
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
            foreach (Seat seat in this.SoiDangChoi.SeatList)
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



        /// <summary>
        /// End van trong truong hop co thang ha lao
        /// </summary>
        /// <param name="haLaoSeat"></param>
        private void EndVan(Seat haLaoSeat)
        {
            this.IsFinished = true;
            this.SoiDangChoi.IsPlaying = false;

            /*tru tien thằng hạ láo*/
            int chipHaLao = Cashier.CHIP_DEN * (this.SoiDangChoi.SeatList.Count - 1);
            haLaoSeat.Player.SubtractMoney(chipHaLao * this.SoiDangChoi.SoiOption.TiGiaChip);
            /*thong bao*/
            this.AddMessage("Phạt", haLaoSeat.Player.Username + " " + chipHaLao + " chip");

            /*cong tien cho cac player con lai*/
            foreach (Seat seat in this.SoiDangChoi.SeatList)
            {
                if (seat.Pos != haLaoSeat.Pos)
                {
                    seat.Player.AddMoney(Cashier.CHIP_DEN * this.SoiDangChoi.SoiOption.TiGiaChip);
                    this.AddMessage("Thưởng", seat.Player.Username + " " + Cashier.CHIP_DEN + " chip");
                }
            }

            WinnerUsername = haLaoSeat.Player.Username;
        }


        /// <summary>
        /// kết thúc ván bình thường và tính điểm, xác định thắng thua, sang tiền
        /// </summary>
        private void EndVan()
        {
            this.IsFinished = true;
            this.SoiDangChoi.IsPlaying = false;

            /*tính điểm*/
            int[] pointArr = this.TinhDiemBaiTrenTay();

            /*xác định danh sách seat tăng dần theo điểm*/
            Seat[] resultSeatArr = this.SapXep(ref pointArr);
            int totalWinnerChip = 0;
            for (int i = 1; i < pointArr.Length; i++ )
            {
                Seat seat = resultSeatArr[i];
                int chip = 0; /*so chip phai nop*/
                if (pointArr[i] < CONST.MOM_POINTVALUE)
                {
                    /*vị trí i sẽ phải trả i chip cho thằng nhất*/
                    chip = i;                    
                    this.AddMessage("Về thứ " + (i + 1), seat.Player.Username + " Điểm: " + pointArr[i] + "     Số chip: -" + chip);
                }
                else
                {
                    /*nộp móm*/
                    chip = Cashier.CHIP_MOM;   
                    this.AddMessage("Về thứ " + (i + 1), seat.Player.Username + " Điểm: Móm     Số chip: -" + chip );
                }
                

                /*trừ tiền*/
                seat.Player.SubtractMoney(chip * this.SoiDangChoi.SoiOption.TiGiaChip);                    
                totalWinnerChip += chip;
            }

            /*sang tiền cho thằng nhất*/
            Seat winner = resultSeatArr[0];
            winner.Player.AddMoney(totalWinnerChip * this.SoiDangChoi.SoiOption.TiGiaChip);
            this.AddMessage("Thắng cuộc", winner.Player.Username + " Điểm: " + pointArr[0] + "    Số chip: +" + totalWinnerChip);

            WinnerUsername = winner.Player.Username;
        }


        /// <summary>
        /// Kết thúc ván khi có người Ù
        /// </summary>
        /// <param name="uSeat"></param>
        /// <param name="denSeat"></param>
        /// <param name="uTron"></param>
        private void EndVan(Seat uSeat, Seat denSeat, bool uTron)
        {
            this.IsFinished = true;
            this.SoiDangChoi.IsPlaying = false;

            int chipAnU = Cashier.CHIP_U;
            
            if (uTron)
            {
                chipAnU = Cashier.CHIP_U_TRON;                
            }

            /*nếu có thằng phải đền*/
            if (denSeat != null)
            {
                /*trừ tiền tài khỏan thằng phải đền*/
                int chipDen = chipAnU * (this.SoiDangChoi.SeatList.Count - 1);
                denSeat.Player.SubtractMoney(chipDen * this.SoiDangChoi.SoiOption.TiGiaChip);
                /*thong bao*/
                this.AddMessage("Đền ù", denSeat.Player.Username + "    -" + chipDen + " chip");
            }
            else
            {
                /*mỗi thằng nộp 5 chip cho thằng ù*/
                foreach (Seat seat in this.SoiDangChoi.SeatList)
                {
                    if (seat != uSeat)
                    {
                        seat.Player.SubtractMoney(chipAnU * this.SoiDangChoi.SoiOption.TiGiaChip);
                        /*thong bao*/
                        this.AddMessage("Nộp ù", seat.Player.Username + "   -" + chipAnU + " chip");
                    }
                }
            }
            /*cộng tiền cho thằng ù*/
            int uVal = chipAnU * (this.SoiDangChoi.SeatList.Count - 1) + this.SoiDangChoi.GaValue;
            uSeat.Player.AddMoney(uVal * this.SoiDangChoi.SoiOption.TiGiaChip);
            /*thong bao*/
            this.AddMessage("Ăn ù", uSeat.Player.Username + " " + uVal + " chip, bao gồm gà: " + this.SoiDangChoi.GaValue + " chip");

            /*reset gà*/
            this.SoiDangChoi.GaValue = 0;

            WinnerUsername = uSeat.Player.Username;
        }


        /// <summary>
        /// xác định danh sách seat xếp theo điểm tăng dần
        /// </summary>
        /// <param name="pointArr"></param>
        /// <returns></returns>
        private Seat[] SapXep(ref int[] pointArr)
        {
            
            Seat[] tmpSeatArr = this.SoiDangChoi.SeatList.ToArray();
            for (int i = 0; i < pointArr.Length - 1; i++)
            {
                for (int j = i + 1; j < pointArr.Length; j++)
                {
                    if (pointArr[i] > pointArr[j])
                    {
                        /*sắp xếp lại tmpSeatArr va pointArr*/
                        Seat tmpSeat = tmpSeatArr[j];
                        int tmpInt = pointArr[j];
                        tmpSeatArr[j] = tmpSeatArr[i];
                        pointArr[j] = pointArr[i];
                        tmpSeatArr[i] = tmpSeat;
                        pointArr[i] = tmpInt;
                    }
                }
            }            
            return tmpSeatArr;
        }



       /// <summary>
        /// check seat Hạ láo --> đền
       /// </summary>
       /// <param name="seat">seat ha</param>
       /// <param name="phomList">danh sach phom ma seat da ha</param>
       /// <returns>true/false</returns>        
        private bool checkHaLao(Seat seat, List<Phom> phomList)
        {
            if (phomList == null || phomList.Count == 0)
            {
                return true;
            }
           
            /*có 1 phỏm chứa > 1 cây đã ăn --> hạ láo*/
            foreach (Phom phom in phomList)
            {
                bool found = false; /*chua tim thay card nao trong bai da an co mat trong phom*/             
                foreach (Card card in seat.BaiDaAn)
                {
                    /*nếu đã tìm thấy 1 card trong bài đã ăn có mặt trong phom, mà lại tìm thấy card nữa
                     cũng có mặt trong phỏm thì là hạ láo*/
                    if (phom.CardArray.Contains(card) && found)
                    {
                        return true;
                    }
                    if (phom.CardArray.Contains(card))
                    {
                        found = true;
                    }                    
                }                
            }

            /*kiểm tra cây đã ăn phải thuộc 1 và chỉ 1 phỏm*/
            foreach (Card card in seat.BaiDaAn)
            {
                bool found = false; /*card chua nam trong phom nao*/
                int index = 0;
                foreach(Phom phom in phomList)
                {
                    /*nếu bài đã ăn đã nằm trong 1 phỏm mà lại nằm tiếp trong phỏm khác --> hạ láo*/
                    if (phom.CardArray.Contains(card) && found)
                    {
                        return true;
                    }
                    if(phom.CardArray.Contains(card))
                    {
                        found = true;
                    }
                    index++;
                }
                /*bài đã ăn không thuộc phỏm nào --> hạ láo*/
                if (!found && index == phomList.Count - 1)
                {
                    return true;
                }
            }

            return false;

        }

        /// <summary>
        /// Tính điểm bài trên tay còn lại của tất cả các seat
        /// 
        /// </summary>
        /// <returns>mảng các giá trị int là điểm của seat với index tương ứng</returns>
        public int[] TinhDiemBaiTrenTay()
        {
            int[] tmpArr = new int[this.SoiDangChoi.SeatList.Count];

            foreach (Seat seat in this.SoiDangChoi.SeatList)
            {
                /*neu seat bi mom, set diem = vo cung + hạ Index*/
                if (seat.PhomList.Count == 0)
                {
                    tmpArr[seat.Pos] = CONST.MOM_POINTVALUE + seat.HaIndex;
                }
                else
                {
                    tmpArr[seat.Pos] = this.TinhDiemCards(seat.BaiTrenTay);
                }
            }
            return tmpArr;
        }

        private int TinhDiemCards(List<Card> cardList)
        {
            int tmpVal = 0;
            foreach (Card card in cardList)
            {
                tmpVal += card.PointValue;
            }
            return tmpVal;
        }

        /// <summary>
        /// thêm mới một thông điệp của ván (sự kiện của ván). Sự kiện mới sẽ có ID mới (tự động, số tự tăng)
        /// </summary>
        /// <param name="code"></param>
        /// <param name="msg"></param>
        /// <returns>thông điệp đã được thêm</returns>
        Message AddMessage(string code, string msg)
        {
            Message message = new Message(code, msg);
            if (_MessageList.Count > 0)
            {
                message.ID = _MessageList.Last().ID + 1;
            }
            
            _MessageList.Add(message);
            return message;
        }


    }
}
