using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using System.Collections.Generic;


using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Authentication;


namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// biểu diễn một chỗ ngồi trong một sới. Chỗ ngồi sẽ có thông tin về người ngồi chơi, các cây bài thuộc về chỗ ngồi (khi đang đánh mới có)
    /// </summary>
    public class Seat : APIDataEntry
    {
        int _Pos = 0;
        /// <summary>
        /// Thứ tự chỗ ngồi 0 1 2 3
        /// </summary>
        [ElementXMLExportAttribute("pos", DataOutputXMLType.Attribute)]
        public int Pos
        {
            get { return _Pos; }
            set { _Pos = value; }
        }
        
        bool _IsReady;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsReady
        {
            get { return _IsReady; }
            set { _IsReady = value; }
        }
        
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int HaIndex { get; set; }




        TalaUser _Player;
        /// <summary>
        /// User nào đang ngồi ở chỗ này
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public TalaUser Player
        {
            get { return _Player; }
            set { _Player = value; }
        }

        public List<Card> BaiTrenTay;
        public List<Card> BaiDaDanh;
        public List<Card> BaiDaAn;
        public List<Card> BaiDaGui;
        public List<Phom> PhomList;

        /// <summary>
        /// số cây gửi vào các phỏm của seat này, dùng để tính toán tổng số cây thực sự của 1 seat
        /// </summary>
        public int SoCayGuiToiSeat { get; set; }

        /// <summary>
        /// nếu đang chơi mà một user bị timeout, ghi biến này = true. Nếu người đó lại vào chơi, ghi lại biến này bằng false
        /// </summary>
        public bool IsDisconnected { get; set; }

        /// <summary>
        /// nếu đang chơi mà một người chơi nhấn nút rời sới, gán cờ này = true, nếu người đó lại vào chơi, ghi lại = false, cho vào lại
        /// </summary>
        public bool IsQuitted { get; set; }


        /// <summary>
        /// tạo một chỗ ngồi cho player tại vị trí xác định
        /// </summary>
        /// <param name="pos">nếu tạo mới seat thì truyền -1 vào, coi như chưa có pos trong sới</param>
        /// <param name="player"></param>
        public Seat(int pos, TalaUser player)
        {
            this._Pos = pos;
            this.Player = player;

            this.HaIndex = pos;
            this.BaiTrenTay = new List<Card>();
            this.BaiDaDanh = new List<Card>();
            this.BaiDaAn = new List<Card>();
            this.BaiDaGui = new List<Card>();
            this.PhomList = new List<Phom>();
            this.SoCayGuiToiSeat = 0;
            IsDisconnected = false;
            IsQuitted = false;
        }


      
        

        /// <summary>
        /// Lấy tổng số card trên tay + card đã ăn + Các Card trong các phỏm + các cây đã gửi đi bài khác. Số này luôn = 9 hoặc 10
        /// </summary>
        /// <returns></returns>
        public int GetTotalCardOnSeat()
        {
            if (this.BaiTrenTay == null || this.BaiDaAn == null || this.PhomList == null || this.BaiDaGui == null)
            {
                return 0;
            }
            return this.BaiTrenTay.Count + this.BaiDaAn.Count + this.GetTotalCardsInAllPhom() + this.BaiDaGui.Count;
        }

        /// <summary>
        /// lấy số lượng các cây trong các phỏm của người chơi ngồi ở Seat này (không tính các cây người khác gửi vào phỏm)
        /// </summary>
        /// <returns></returns>
        private int GetTotalCardsInAllPhom()
        {
            if (this.PhomList == null || this.PhomList.Count == 0)
            {
                return 0;
            }

            int count = 0;
            foreach (Phom phom in this.PhomList)
            {
                if (phom.CardArray != null)
                {
                    count += phom.CardArray.Length;
                }
            }
            
            /// bỏ qua các cây của người khác gửi tới seat này
            return count - this.SoCayGuiToiSeat;
        }


    }
}
