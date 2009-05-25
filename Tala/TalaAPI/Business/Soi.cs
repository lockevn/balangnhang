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
using TalaAPI.Lib;
using System.Collections.Generic;



namespace TalaAPI.Business
{
    public class Soi
    {
        public string Name;
        public int Id;
        public DateTime StartTime;
        public DateTime EndTime;
        public string Description;
        public List<Seat> SeatList;
        public string OwnerUsername;
        public Option SoiOption;
        public int GaValue;
        public Van CurrVan;
        public bool IsLocked;
        


        public Soi(int id, string owner)
        {
            this.Id = id;
            this.OwnerUsername = owner;
            this.StartTime = System.DateTime.Now;
            this.SeatList = new List<Seat>();
        }

        
        public Van CreateVan(bool isXepChoRequired)
        {

            int newVanIndex = 1;
            /*index van moi = index van cu + 1*/
            if (this.CurrVan != null)
            {
                newVanIndex = this.CurrVan.Index++;
            }
            Van newVan = new Van(newVanIndex, this);
            this.CurrVan = newVan;

            /*xep cho randomly*/
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /*chia bai*/
            newVan.ChiaBai();
            return newVan;

        }

        public int NopGa(int value)
        {
            if (value <= 0)
            {
                return -1;
            }
            this.GaValue += value;
            return this.GaValue;
        }

        public int JoinSoi(User player)
        {
            if (player == null)
            {
                return -1;
            }
            /*neu soi da het cho thi khong join dc*/
            int count = this.SeatList.Count;
            if (count >= 4)
            {
                return -1;
            }

            /*neu player da join soi thi 0 join dc lan 2 nua*/
            foreach (Seat seat in this.SeatList)
            {
                if (seat.Player.Username == player.Username)
                {
                    return -1;
                }
            }

            Seat newSeat = new Seat(count, player);
            this.SeatList.Add(newSeat);
            /*return new seat index*/
            return count;

        }

        public void XepChoRandom()
        {
            int max = this.SeatList.Count;
            if (this.SeatList == null || max == 0)
            {
                return;
            }

            /*generate a temp Arr of max elements*/
            int[] tmpArr = new int[max];
            for (int i = 0; i < max; i++)
            {
                tmpArr[i] = i;
            }

            /*randomly reindexing tmpArr*/
            int[] randomArr = TextUtil.ReindexArrayRandomly(tmpArr);


            /*create new player list*/
            User[] newPlayerArr = new User[max];
            /*pick player from SeatList according to randomArr and add to newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                newPlayerArr[i] = this.SeatList.ElementAt(randomArr[i]).Player;
            }

            /*xep lai SeatList theo newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                Seat seat = new Seat(i, newPlayerArr[i]);
                this.SeatList.Insert(i, seat);
            }
        }

        
        












    }
}
