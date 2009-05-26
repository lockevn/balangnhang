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
    
    public class Card
    {
        public string So;
        public string Chat;

        public static string[] SO_SET = new string[13] {"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13"};
        /// <summary>
        /// Co Do Pich Tep
        /// </summary>
        public static string[] CHAT_SET = new string[4] { "c", "r", "b", "t" };
        public static Card[] CARD_SET
        {
            get
            {
                List<Card> tmpCardList = new List<Card>();
                foreach (string so in SO_SET)
                {
                    foreach (string chat in CHAT_SET)
                    {
                        Card tmpCard = new Card(so, chat);
                        tmpCardList.Add(tmpCard);
                    }
                }
                return tmpCardList.ToArray();
            }
        }
                

        public Card(string so, string chat)
        {
            this.So = so;
            this.Chat = chat;
        }

        public bool IsCungChat(Card card)
        {
            if (this.Chat == card.Chat)
            {
                return true;
            }
            return false;
        }

        public bool IsCungSo(Card card)
        {
            if (this.So == card.So)
            {
                return true;
            }
            return false;
        }

        


        public static bool operator ==(Card c1, Card c2)
        {
            if (c1.So == c2.So && c1.Chat == c2.Chat)
            {
                return true;
            }
            return false;            
        }

        public static bool operator !=(Card c1, Card c2)
        {            
            return (!(c1 == c2));
        }

        public static bool operator >(Card c1, Card c2)
        {
            if (c1.So.CompareTo(c2.So) > 0)
            {
                return true;
            }
            else if(c1.So == c2.So)
            {
                if (c1.Chat.CompareTo(c2.Chat) > 0)
                {
                    return true;
                }
            }
            return false;            
        }

        public static bool operator <(Card c1, Card c2)
        {
            if(c1 == c2)
            {
                return false;
            }
            
            return (!(c1 > c2));
        }


    }

    
}
