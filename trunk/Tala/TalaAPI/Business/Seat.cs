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
        public int Index;
        public User Player;
        public int HaIndex;

        public List<Card> BaiTrenTay;
        public List<Card> BaiDaDanh;
        public List<Card> BaiDaAn;
        public List<Phom> PhomList;

        public Seat(int index, User player)
        {
            this.Index = index;
            this.Player = player;

            this.HaIndex = index;
            this.BaiTrenTay = new List<Card>();
            this.BaiDaDanh = new List<Card>();
            this.BaiDaAn = new List<Card>();
            this.PhomList = new List<Phom>();
        }

        public int GetPreviousSeatIndex(int seatCount)
        {
            if (this.Index == 0)
            {
                return seatCount;
            }
            return this.Index - 1;
        }

        public int GetNextSeatIndex(int seatCount)
        {
            if (this.Index == seatCount)
            {
                return 0;
            }
            return this.Index + 1;
        }


    }
}
