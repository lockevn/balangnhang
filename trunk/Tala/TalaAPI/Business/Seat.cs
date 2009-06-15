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
using TalaAPI.XMLRenderOutput;
using TalaAPI.Lib;


namespace TalaAPI.Business
{
    public class Seat : APIDataEntry
    {
        int _Index;
        /// <summary>
        /// Thứ tự chỗ ngồi 0 1 2 3
        /// </summary>
        [ElementXMLExportAttribute("pos", DataOutputXMLType.Attribute)]
        public int Index
        {
            get { return _Index; }
            set { _Index = value; }
        }

        User _Player;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public User Player
        {
            get { return _Player; }
            set { _Player = value; }
        }

        bool _IsReady;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsReady
        {
            get { return _IsReady; }
            set { _IsReady = value; }
        }
        

        public int HaIndex;

        public List<Card> BaiTrenTay;
        public List<Card> BaiDaDanh;
        public List<Card> BaiDaAn;

        public List<Card> BaiDaGui;

        public List<Phom> PhomList;

        public int SoCayGuiToiSeat; /*số cây gửi vào các phỏm của seat, dùng để tính toán tổng số cây thực sự của 1 seat*/
        


        public Seat(int index, User player)
        {
            this._Index = index;
            this.Player = player;

            this.HaIndex = index;
            this.BaiTrenTay = new List<Card>();
            this.BaiDaDanh = new List<Card>();
            this.BaiDaAn = new List<Card>();
            this.BaiDaGui = new List<Card>();
            this.PhomList = new List<Phom>();
            this.SoCayGuiToiSeat = 0;
        }

        /// <summary>
        /// Lấy chỉ số của seat trước so với chỉ số hiện tại của seat. Chỉ số có thể là index hoặc haIndex của Seat
        /// </summary>
        /// <param name="currIndex">index hoặc haIndex của Seat</param>
        /// <param name="seatCount">tổng số seat trong sới</param>
        /// <returns>chỉ số của seat trước</returns>
        public static int GetPreviousSeatIndex(int currIndex, int seatCount)
        {
            if (currIndex == 0)
            {
                return seatCount - 1;
            }
            return currIndex - 1;
        }

        /// <summary>
        /// Lấy chỉ số của seat sau so với chỉ số hiện tại của seat. Chỉ số có thể là index hoặc haIndex của Seat
        /// </summary>
        /// <param name="currIndex">index hoặc haIndex của Seat</param>
        /// <param name="seatCount">tổng số seat trong sới</param>
        /// <returns>chỉ số của seat sau</returns>
        public static int GetNextSeatIndex(int currIndex, int seatCount)
        {
            if (currIndex == seatCount - 1)
            {
                return 0;
            }
            return currIndex + 1;
        }

        /// <summary>
        /// Lấy tổng số card trên tay lẫn card đã ăn của seat
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
            return count - this.SoCayGuiToiSeat;
        }

        




    }
}
