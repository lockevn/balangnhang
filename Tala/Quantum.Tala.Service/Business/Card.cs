using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;





using System.Xml.Linq;
using System.Collections.Generic;


using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Biểu diễn một quân bài
    /// </summary>
    [ElementXMLExportAttribute("c", DataOutputXMLType.NestedTag)]
    public class Card : APIDataEntry, IComparable<Card>
    {
        #region Các khai báo tĩnh, ràng buộc nghiệp vụ về một Card

        /// <summary>
        /// Tập hợp các số có thể của bộ bài, từ 01 đến 13
        /// </summary>
        public static string[] SO_SET = new string[13] {"01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13"};
        //public static string[] SO_SET = new string[5] { "01", "03", "05", "07", "08"};
        
        /// <summary>
        /// Cơ Dô Pích Tép (theo thứ tự  C D P T)
        /// </summary>        
        public static string[] CHAT_SET = new string[4] { "c", "d", "p", "t" };        
        
        /// <summary>
        /// Khởi tạo và trả về một bộ bài mới nguyên, chưa tráo, từ nhỏ đến to
        /// </summary>
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
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string So
        {
            get { return _So.ToLower(); }
            set { _So = value; }
        }
        string _Chat;
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string Chat
        {
            get { return _Chat.ToLower(); }
            set { _Chat = value; }
        }

        int _Pos = -1;
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Pos
        {
            get { return _Pos; }
            set { _Pos = value;  }
        }

        /// <summary>
        /// get Row index of this card in the 2-dimension array model
        /// </summary>
        public int SoIndex
        {
            get
            {
                int so = -1;
                int.TryParse(this._So, out so);
                if(so > 0)
                {
                    return so - 1;
                }
                return so;
            }
        }

        /// <summary>
        /// get Column index of this card in the 2-dimension array model
        /// </summary>
        public int ChatIndex
        {
            get
            {
                for(int i=0; i < Card.CHAT_SET.Length; i++)

                if (this._Chat == Card.CHAT_SET[i])
                {
                    return i;
                }
                return -1;
            }            
        }


        
        /// <summary>
        /// giá trị của cây bài, dùng để tính điểm khi cuối ván kết thúc bằng tính điểm
        /// </summary>
        public int PointValue
        {
            get
            {
                int tmpVal = 0;
                int.TryParse(this.So, out tmpVal);
                return tmpVal;
            }
        }


        public Card(string so, string chat)
        {
            this.So = so;
            this.Chat = chat;
        }



        /// <summary>
        /// Viết Card thành dạng 3 ký tự, ssc (số số chất)
        /// </summary>
        /// <returns></returns>
        public override string ToString()
        {
            return So + Chat;
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

                      
        private static bool Compare(Card c1, Card c2)
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
        

        #region IComparable<Card> Members

        public int CompareTo(Card other)
        {
            if (Compare(this,other))
            {
                return 0;
            }
            else
            {
                if (this > other)
                {
                    return 1;
                }
                else
                {
                    return -1;
                }
            }
        }

        #endregion
    }    
}
