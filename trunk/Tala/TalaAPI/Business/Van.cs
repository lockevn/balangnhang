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
            this.Noc = new List<Card>();
            this.IsFinished = false;
        }

        /*random set Van.Noc*/
        public void InitializeNoc()
        {
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
                    this.Soi.SeatList[j].BaiTrenTay.Add(this.Noc.ElementAt(i + j));
                }
            }
            /*chia them cho seat[0] 1 cay */
            this.Soi.SeatList[0].BaiTrenTay.Add(this.Noc.ElementAt(9 * seatCount));
        }




        public bool Danh(Seat seat, Card card)
        {
            return true;

        }

        public bool Boc(Seat seat)
        {
            return true;
        }

        public bool An(Seat seat)
        {
            return true;
        }

        public bool U(Seat seat, Card[] cards)
        {
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

        public bool IsSeatInTurn(Seat seat)
        {
            return true;
        }


    }
}
