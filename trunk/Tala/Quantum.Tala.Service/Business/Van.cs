﻿using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Xml.Linq;
using System.Collections.Generic;

using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Exception;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    public class Van : APIDataEntry
    {
        int _ID;
        [ElementXMLExportAttribute("id", DataOutputXMLType.NestedTag)]
        public int ID
        {
            get { return _ID; }
            set { _ID = value; }
        }

        int _CurrentTurnSeatIndex;
        /// <summary>
        /// Lượt hiện tại đang ở Seat nào? Seat nào có lượt, thì số này = index của Seat đó trong SeatList
        /// </summary>
        public int CurrentTurnSeatIndex
        {
            get { return _CurrentTurnSeatIndex; }
            // HACK: không cho truy xuất từ bên ngoài trực tiếp vào biến Turn này, tránh sai sót.
            // set { _CurrentTurnSeatIndex = value; }
        }

        public bool IsFinished { get; set; }
        public int CurrentRound { get; set; }
        public Soi SoiDangChoi { get; set; }

        
        internal List<Card> _Noc { get; set; }

        public List<Card> Noc        
        {
            get
            {
                return _Noc;
            }
        }

        

        public string WinnerUsername { get; set; }

        private List<Seat> _AnChotNguyCoDenList; /*list các seat ăn chốt có nguy cơ đền*/

        List<Message> _MessageList = new List<Message>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Message> MessageList
        {
            get { return _MessageList; }
            set { _MessageList = value; }
        }



        public Van(int index, Soi soi)
        {
            this.ID = index;
            this.SoiDangChoi = soi;
            this.CurrentRound = 1;
            this._CurrentTurnSeatIndex = 0;

            this.InitializeNoc();
            this.IsFinished = false;

            this._AnChotNguyCoDenList = new List<Seat>();

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

            /* kiểm tra nếu đến lượt phải hạ phỏm, thằng này có ăn cây nào mà không hạ phỏm k */
            if (seat.BaiDaDanh.Count == 3 && seat.BaiDaAn.Count > 0)
            {
                // TODO: kiểm tra current Tournament Type để quyết định có dừng ván hay không?
                // this.SoiDangChoi.GetCurrentTournament().type
                
                this.EndVan_HaLao(seat);
                return true;
            }

            /*chuyen card tu BaiTrenTay cua seat[i] sang BaiDaDanh cua seat[i]*/
            seat.BaiTrenTay.Remove(card);
            seat.BaiDaDanh.Add(card);

            /*nếu sau khi đánh mà bài trên tay của seat count=0 --> ù thường
             --> giải quyết trường hợp gửi xong còn 1 cây trên tay đánh nốt.
             */
            if (seat.BaiTrenTay.Count == 0)
            {
                /*cho seat U va endVan*/
                this.EndVan_U(seat, null, false);
                return true;
            }

            /*chuyển turn sang next seat*/
            this.AdvanceCurrentTurnIndex();

            /*nếu là seat đánh cuối cùng ở vòng cuối cùng thì end game (kiểm tra qua Nọc)*/
            if (this._Noc.Count == 0)
            {
                this.EndVan_TinhDiem();
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
                || this._Noc.Count == 0
                || seat.GetTotalCardOnSeat() > 9)
            {
                return null;
            }
            /*chuyển 1 cây ở Nọc lên BaiTrenTay của seat*/
            Card cardBoc = this._Noc.ElementAt(0);
            seat.BaiTrenTay.Add(cardBoc);
            this._Noc.RemoveAt(0);
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
                    previousSeat.Player.SubtractMoney(money, EnumPlayingResult.Nothing);
                    /*cong tien vao tai khoan cua nguoi an chot*/
                    seat.Player.AddMoney(money, EnumPlayingResult.Nothing);

                    /*ăn chốt có nguy cơ đền*/
                    if (previousSeat.BaiDaDanh.Count >= 3)
                    {
                        /*luu nhung thang an chot co nguy cơ phải đền*/
                        this._AnChotNguyCoDenList.Add(seat);
                    }
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
        /// <returns>true/false</returns>
        public bool U(Seat seat)
        {
            if (!this.IsSeatInTurn(seat) || seat.GetTotalCardOnSeat() < 10)
            {
                return false;
            }
            //int count = 0; /*dem tong so cay cua tat ca cac phom*/
            //List<Phom> tmpPhomList = new List<Phom>();
            /*kiem tra tinh chinh xac cua phom*/
            //foreach(Card[] cardArr in phomArr)
            //{
            //    /*kiểm tra các cây ù có thuộc bài trên tay và bài đã ăn của seat không*/                
            //    foreach (Card card in cardArr)
            //    {
            //        if (!seat.BaiTrenTay.Contains(card) && !seat.BaiDaAn.Contains(card))
            //        {
            //            return false;
            //        }
            //        count++;
            //    }

            //    Phom tmpPhom = cardArr.IsValidPhom();
            //    if ( tmpPhom == null)
            //    {
            //        return false;
            //    }
            //    else
            //    {
            //        tmpPhomList.Add(tmpPhom);
            //    }

            //}

            int count = UUtil.CheckU(seat.BaiTrenTay, seat.BaiDaAn);
            /*nếu tổng tất cả các phỏm hạ không đủ 9 cây*/
            if (count < 9)
            {
                return false;
            }

            Seat denSeat = null; /*không ai phải đền*/

            /*nếu có thằng ăn chốt ở vòng hạ, thằng ăn chốt cuối cùng (khác thằng ù) phải đền*/
            if(this._AnChotNguyCoDenList.Count != 0)
            {                
                denSeat = this._AnChotNguyCoDenList.Last();
                /*nếu thằng ăn chốt là thằng ù*/
                if (seat.Pos == denSeat.Pos)
                {
                    if(this._AnChotNguyCoDenList.Count > 1)
                    {
                        /*nếu trước đó có thằng ăn chốt thì thằng này đền*/
                        denSeat = this._AnChotNguyCoDenList.ElementAt(this._AnChotNguyCoDenList.Count - 2);
                    }
                    else
                    {
                        /*nếu không, thì thằng ù là thằng ăn chốt duy nhất thì không ai phải đền*/
                        denSeat = null;
                    }
                }                                                
            }

            /*neu bai da an cua seat == 3, previous seat phai den*/
            else if (seat.BaiDaAn.Count == 3)
            {
                int previousIndex = Seat.GetPreviousSeatIndex(seat.Pos, this.SoiDangChoi.SeatList.Count);
                denSeat = this.SoiDangChoi.SeatList.ElementAt(previousIndex) as Seat;                                                
            }                                                      
            this.EndVan_U(seat, denSeat, count == 10);

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
                    if (cardList.Contains(card))
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
            if (this.CheckHaLao(seat, phomList))
            {
                /*end van và đền*/
                this.EndVan_HaLao(seat);
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
                this.EndVan_U(seat, null, true);
            }

            return true;
        }







        /// <summary>
        /// Hàm này sẽ set các cờ,
        /// cộng trừ VCoin hay không, tuỳ theo thể thức của tour
        /// CÓ kết thúc ván và giải tán sới hay không, tuỳ theo thể thức của tours
        /// các thao tác chung khi kết thúc một ván, dù ván kết thúc theo cách nào thì cũng phải gọi hàm này .
        /// Chỉ gọi ở cuối các hàm EndVan
        /// </summary>
        private void FinishVan(string p_sWinnerUsername)
        {
            WinnerUsername = p_sWinnerUsername;
            this.IsFinished = true;
            this.SoiDangChoi.IsPlaying = false;
            
            switch (this.SoiDangChoi.GetCurrentTournament().type)
            {
                case (int)TournamentType.DeadMatch:
                    // TODO: huỷ ván, huỷ sới, đuổi người chơi luôn, cộng tiền thưởng cho người nhất
                    break;
                case (int)TournamentType.ChampionShip:
                    // kệ cho chơ tiếp
                    break;
                case (int)TournamentType.TennisTree:
                    // TODO: thiết lập vòng sau
                    break;
                default:
                    // FREE : kệ cho chơ tiếp
                    break;
            }
            
        }


        /// <summary>
        /// Kết thúc ván trong trường hợp có người hạ láo
        /// </summary>
        /// <param name="haLaoSeat"></param>
        private void EndVan_HaLao(Seat haLaoSeat)
        {
            // TODO: sửa, vì với deathmatch, hạ láo ko dừng trận đấu ngay

            /*tru tien thằng hạ láo*/
            int chipHaLao = Cashier.CHIP_DEN * (this.SoiDangChoi.SeatList.Count - 1);
            haLaoSeat.Player.SubtractMoney(chipHaLao * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Lose);
            /*thong bao*/
            this.AddMessage("Phạt", haLaoSeat.Player.Username + " " + chipHaLao + " chip");

            /*cong tien cho cac player con lai*/
            foreach (Seat seat in this.SoiDangChoi.SeatList)
            {
                if (seat.Pos != haLaoSeat.Pos)
                {
                    seat.Player.AddMoney(Cashier.CHIP_DEN * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Win);
                    this.AddMessage("Thưởng", seat.Player.Username + " " + Cashier.CHIP_DEN + " chip");
                }
            }

            FinishVan(haLaoSeat.Player.Username);
        }

        /// <summary>
        /// kết thúc ván bình thường và tính điểm, xác định thắng thua, sang tiền
        /// </summary>
        private void EndVan_TinhDiem()
        {
            /*tính điểm*/
            int[] pointArr = this.TinhDiemBaiTrenTay();

            /*xác định danh sách seat tăng dần theo điểm*/
            Seat[] resultSeatArr = this.SapXep(ref pointArr);
            int totalWinnerChip = 0;

            /*duyệt lấy từ seat thứ 2 đổ đi */
            for (int i = 1  ; i < pointArr.Length; i++)
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
                    this.AddMessage("Về thứ " + (i + 1), seat.Player.Username + " Điểm: Móm     Số chip: -" + chip);
                }


                /*trừ tiền*/
                seat.Player.SubtractMoney(chip * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Lose);
                totalWinnerChip += chip;
            }

            /*sang tiền cho thằng nhất*/
            Seat winner = resultSeatArr[0];
            winner.Player.AddMoney(totalWinnerChip * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Win);
            this.AddMessage("Thắng cuộc", winner.Player.Username + " Điểm: " + pointArr[0] + "    Số chip: +" + totalWinnerChip);

            FinishVan(winner.Player.Username);
        }

        /// <summary>
        /// Kết thúc ván khi có người Ù
        /// </summary>
        /// <param name="uSeat"></param>
        /// <param name="denSeat"></param>
        /// <param name="uTron"></param>
        private void EndVan_U(Seat uSeat, Seat denSeat, bool uTron)
        {
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
                denSeat.Player.SubtractMoney(chipDen * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Lose);
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
                        seat.Player.SubtractMoney(chipAnU * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Lose);
                        /*thong bao*/
                        this.AddMessage("Nộp ù", seat.Player.Username + "   -" + chipAnU + " chip");
                    }
                }
            }
            /*cộng tiền cho thằng ù*/
            int uVal = chipAnU * (this.SoiDangChoi.SeatList.Count - 1) + this.SoiDangChoi.GaValue;
            uSeat.Player.AddMoney(uVal * this.SoiDangChoi.SoiOption.TiGiaChip, EnumPlayingResult.Win);
            /*thong bao*/
            this.AddMessage("Ăn ù", uSeat.Player.Username + " " + uVal + " chip, bao gồm gà: " + this.SoiDangChoi.GaValue + " chip");

            /*reset gà*/
            this.SoiDangChoi.GaValue = 0;

            FinishVan(uSeat.Player.Username);
        }





        /// <summary>
        /// Khởi tạo Nọc randomly
        /// </summary>
        private void InitializeNoc()
        {
            this._Noc = new List<Card>();

            /*generate a temporary array of 52 elements*/
            int[] tmpArr = new int[52];
            for (int i = 0; i < 52; i++)
            {
                tmpArr[i] = i;
            }
            /*randomly reindexing the tmpArr*/
            int[] randomArr = FunctionExtension.ReindexArrayRandomly(tmpArr);

            /*randomly pick a Card in CARD_SET and add to Noc*/
            for (int i = 0; i < 52; i++)
            {
                this._Noc.Add(Card.CARD_SET[randomArr[i]]);
                System.Diagnostics.Debug.WriteLine("card " + i + ": " + Card.CARD_SET[randomArr[i]].ToString());
            }

        }

        /// <summary>
        /// Chia bài từ Nọc cho các seat, chia cho Winner của ván trước đầu tiên
        /// </summary>
        internal void ChiaBai(string p_sOldWinnerUsername)
        {
            if (this._Noc == null || this._Noc.Count != 52)
            {
                return;
            }

            int nSeatCount = this.SoiDangChoi.SeatList.Count;
            for (int i = 0; i < 9; i++)
            {
                for (int j = 0; j < nSeatCount; j++)
                {
                    /*chia bai i+j cho seat[j]*/
                    this.SoiDangChoi.SeatList[j].BaiTrenTay.Add(this._Noc[nSeatCount * i + j]);
                }
            }

            Seat seatDanhDauTien = SoiDangChoi.GetSeatByUsername(p_sOldWinnerUsername);
            if (seatDanhDauTien == null)
            {
                seatDanhDauTien = SoiDangChoi.SeatList[0];
            }
            /*chia them cho seat đánh đầu tiên 1 cay */
            seatDanhDauTien.BaiTrenTay.Add(this._Noc[9 * nSeatCount]);

            /*xoa cac cay da chia ra khoi Noc*/
            for (int i = 0; i < 9 * nSeatCount + 1; i++)
            {
                this._Noc.RemoveAt(0);
            }

            _CurrentTurnSeatIndex = seatDanhDauTien.Pos;
        }

        /// <summary>
        /// Chuyển lượt đánh cho Seat kế tiếp
        /// </summary>
        private int AdvanceCurrentTurnIndex()
        {
            _CurrentTurnSeatIndex = Seat.GetNextSeatIndex(this.CurrentTurnSeatIndex, this.SoiDangChoi.SeatList.Count);
            return this.CurrentTurnSeatIndex;
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
                throw new NotInTurnException(string.Format("seat [{0}] of player [{1}] is not in turn", seat.Pos, seat.Player.Username));
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
        /// check xem seat này có Hạ láo hay không
        /// </summary>
        /// <param name="seat">seat ha</param>
        /// <param name="phomList">danh sach phom ma seat da ha</param>
        /// <returns>true/false</returns>        
        private bool CheckHaLao(Seat seat, List<Phom> phomList)
        {
            if (phomList == null || phomList.Count == 0)
            {
                return true;
            }

            /*có 1 phỏm chứa > 1 cây đã ăn --> hạ láo*/
            foreach (Phom phom in phomList)
            {
                bool bFound = false; /*chua tim thay card nao trong bai da an co mat trong phom*/
                foreach (Card card in seat.BaiDaAn)
                {
                    /*nếu đã tìm thấy 1 card trong bài đã ăn có mặt trong phom, mà lại tìm thấy card nữa
                     cũng có mặt trong phỏm thì là hạ láo*/
                    if (phom.CardArray.Contains(card) && bFound)
                    {
                        return true;
                    }
                    if (phom.CardArray.Contains(card))
                    {
                        bFound = true;
                    }
                }
            }

            /*kiểm tra cây đã ăn phải thuộc 1 và chỉ 1 phỏm*/
            foreach (Card card in seat.BaiDaAn)
            {
                bool found = false; /*card chua nam trong phom nao*/
                int index = 0;
                foreach (Phom phom in phomList)
                {
                    /*nếu bài đã ăn đã nằm trong 1 phỏm mà lại nằm tiếp trong phỏm khác --> hạ láo*/
                    if (phom.CardArray.Contains(card) && found)
                    {
                        return true;
                    }
                    if (phom.CardArray.Contains(card))
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
        private int[] TinhDiemBaiTrenTay()
        {
            int[] tmpArr = new int[this.SoiDangChoi.SeatList.Count];
            foreach (Seat seat in this.SoiDangChoi.SeatList)
            {
                // TODO: với thể thức DeathMatch, tính điểm ăn láo thành 2000, 2001, 2002, 2003
                if(this.SoiDangChoi.GetCurrentTournament().type == (int)TournamentType.DeadMatch)
                {
                    if(this.CheckHaLao(seat, seat.PhomList))
                    {
                        // TODO: có bài đã ăn, mà cây ăn chưa hạ, tính là hạ láo
                        /// chú ý điều kiện để ràng chặt cả chuyện nó ăn 2 cây, hạ 1 phỏm, còn cây kia vẫn là hạ láo
                        tmpArr[seat.Pos] = CONST.HALAO_POINTVALUE + seat.HaIndex;
                    }
                    continue;
                }
                
                /*nếu seat bị móm, điểm = 1000 + hạ Index*/
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


        #region temporary code for load testing only

        /// <summary>
        /// Constructor for load testing only
        /// Chia bai theo mot mau xac dinh
        /// </summary>
        /// <param name="index"></param>
        /// <param name="soi"></param>
        /// <param name="forTesting"></param>
        public Van(int index, Soi soi, bool forTesting)
        {
            this.ID = index;
            this.SoiDangChoi = soi;
            this.CurrentRound = 1;
            _CurrentTurnSeatIndex = 0;
            this._AnChotNguyCoDenList = new List<Seat>();

            // TEST: về sau phải bỏ đi
            if (forTesting)
            {
                this.InitializeNocForTesting();
            }
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
        /// test, bỏ đi
        /// </summary>
        private void InitializeNocForTesting()
        {
            // TEST: về sau phải bỏ đi
            this._Noc = new List<Card>();
            this._Noc.Add(new Card("08", "c"));
            this._Noc.Add(new Card("05", "t"));
            this._Noc.Add(new Card("11", "c"));
            this._Noc.Add(new Card("08", "d"));
            this._Noc.Add(new Card("01", "t"));
            this._Noc.Add(new Card("10", "d"));
            this._Noc.Add(new Card("07", "p"));
            this._Noc.Add(new Card("08", "p"));
            this._Noc.Add(new Card("05", "c"));
            this._Noc.Add(new Card("05", "p"));
            this._Noc.Add(new Card("09", "c"));
            this._Noc.Add(new Card("02", "t"));
            this._Noc.Add(new Card("03", "c"));
            this._Noc.Add(new Card("06", "d"));
            this._Noc.Add(new Card("11", "p"));
            this._Noc.Add(new Card("05", "d"));
            this._Noc.Add(new Card("01", "d"));
            this._Noc.Add(new Card("01", "c"));
            this._Noc.Add(new Card("04", "p"));
            this._Noc.Add(new Card("08", "t"));
            this._Noc.Add(new Card("11", "d"));
            this._Noc.Add(new Card("06", "c"));
            this._Noc.Add(new Card("13", "p"));
            this._Noc.Add(new Card("02", "c"));
            this._Noc.Add(new Card("02", "d"));
            this._Noc.Add(new Card("03", "p"));
            this._Noc.Add(new Card("12", "t"));
            this._Noc.Add(new Card("09", "t"));
            this._Noc.Add(new Card("03", "t"));
            this._Noc.Add(new Card("02", "p"));
            this._Noc.Add(new Card("04", "d"));
            this._Noc.Add(new Card("12", "p"));
            this._Noc.Add(new Card("10", "p"));
            this._Noc.Add(new Card("13", "c"));
            this._Noc.Add(new Card("12", "d"));
            this._Noc.Add(new Card("06", "t"));
            this._Noc.Add(new Card("09", "p"));
            this._Noc.Add(new Card("11", "t"));
            this._Noc.Add(new Card("13", "d"));
            this._Noc.Add(new Card("09", "d"));
            this._Noc.Add(new Card("07", "d"));
            this._Noc.Add(new Card("04", "t"));
            this._Noc.Add(new Card("10", "t"));
            this._Noc.Add(new Card("03", "d"));
            this._Noc.Add(new Card("01", "p"));
            this._Noc.Add(new Card("07", "t"));
            this._Noc.Add(new Card("10", "c"));
            this._Noc.Add(new Card("06", "p"));
            this._Noc.Add(new Card("13", "t"));
            this._Noc.Add(new Card("12", "c"));
            this._Noc.Add(new Card("07", "c"));
            this._Noc.Add(new Card("04", "c"));
        }
        #endregion


    }
}
