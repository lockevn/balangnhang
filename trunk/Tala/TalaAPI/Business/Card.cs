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
using TalaAPI.Exception;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Lib;

namespace TalaAPI.Business
{
    
    public class Card : APIDataEntry
    {
        #region Các ràng buộc nghiệp vụ về một Card

        public static string[] SO_SET = new string[13] {"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13"};
        /// <summary>
        /// Co Do Pich Tep
        /// </summary>
        public static string[] CHAT_SET = new string[4] { "c", "d", "p", "t" };
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

        #endregion
        

        string _So;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string So
        {
            get { return _So.ToLower(); }
            set { _So = value; }
        }
        string _Chat;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string Chat
        {
            get { return _Chat.ToLower(); }
            set { _Chat = value; }
        }


        #region Thuộc tính tạm, chỉ dùng cho việc render XML output
        
        int _Pos;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int Pos
        {
            get { return _Pos; }
            set { _Pos = value;  }
        }

        #endregion



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




        /// <summary>
        /// for use in Compare, Contain ...
        /// </summary>
        /// <param name="obj"></param>
        /// <returns></returns>
        public override bool Equals(object obj)
        {
            return Card.Compare(this, obj as Card);
        }
        
        /// <summary>
        /// use in HashTable
        /// </summary>
        /// <returns></returns>
        public override int GetHashCode()
        {
            return base.GetHashCode();
        }

        /// <summary>
        /// Viết Card thành dạng 3 ký tự, ssc (số số chất)
        /// </summary>
        /// <returns></returns>
        public override string ToString()
        {
            return So + Chat;
        }

               
        public static bool Compare(Card c1, Card c2)
        {
            string sc1 = string.Empty;
            string sc2 = string.Empty;

            try
            {
                sc1 = c1.ToString();
            }
            catch 
            { }

            try
            {
                sc2 = c2.ToString();
            }
            catch 
            { }          

            if (sc1 == sc2)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        public static bool operator ==(Card c1, Card c2)
        {
            return Compare(c1,c2);                    
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


        /// <summary>
        /// create a Card object from string with format SoSoChat: vd 01c (át cơ)
        /// </summary>
        /// <param name="value"></param>
        /// <returns>card object if valid otherwise null</returns>
        public static Card ParseString(string value)
        {
            Card cardRet = null;

            if (value == null || value.Length != 3)
            {
                throw new CardException("invalid card string format: " + value);
            }
            string so = value.Substring(0, 2);
            string chat = value.Substring(2, 1);

            // hợp lệ mới tạo obj Card
            if (Card.SO_SET.Contains(so) && Card.CHAT_SET.Contains(chat))
            {
                cardRet = new Card(so, chat);
                return cardRet;
            }

            throw new CardException("invalid card string format: " + value);
                        
        }
        
    }

    
}
