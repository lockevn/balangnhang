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

namespace TalaAPI.Business
{
    public class Seat
    {
        int _Index;
        /// <summary>
        /// Thứ tự chỗ ngồi 0 1 2 3
        /// </summary>
        public int Index
        {
            get { return _Index; }
        }

        User _Player;
        public User Player
        {
            get { return _Player; }
            set { _Player = value; }
        }

        bool _IsReady;
        public bool IsReady
        {
            get { return _IsReady; }
            set { _IsReady = value; }
        }



        public int HaIndex;

        public List<Card> BaiTrenTay;
        public List<Card> BaiDaDanh;
        public List<Card> BaiDaAn;
        public List<Phom> PhomList;

        public Seat(int index, User player)
        {
            this._Index = index;
            this.Player = player;

            this.HaIndex = index;
            this.BaiTrenTay = new List<Card>();
            this.BaiDaDanh = new List<Card>();
            this.BaiDaAn = new List<Card>();
            this.PhomList = new List<Phom>();
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
                return seatCount;
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
            if (currIndex == seatCount)
            {
                return 0;
            }
            return currIndex + 1;
        }


    }
}
