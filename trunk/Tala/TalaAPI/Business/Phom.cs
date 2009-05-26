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

namespace TalaAPI.Business
{
    public class Phom
    {
        public int Id;
        public Card[] CardArr;
        public Seat OfSeat; /*seat ma phom thuoc ve*/

        public Phom(Card[] cardArr)
        {
            this.CardArr = cardArr;
        }

        /// <summary>
        /// Check 1 tập cards có tạo thành phỏm không, nếu đúng, trả về một Phom
        /// </summary>
        /// <param name="cardArr">mảng Card</param>
        /// <returns>Phom nếu thỏa mãn, null nếu 0 đủ điều kiện tạo phỏm</returns>
        public static Phom Check(Card[] cardArr)
        {
            if (cardArr == null || cardArr.Length < 3)
            {
                return null;
            }
            for (int i = 1; i < cardArr.Length; i++)
            {
                /*kiem tra phom ngang*/
                if (cardArr[i].So == cardArr[i - 1].So)
                {
                    if (i == cardArr.Length - 1)
                    {
                        /*neu da kiem tra den het cardArr thi cardArr đủ điều kiện tạo Phỏm*/
                        Phom phom = new Phom(cardArr);

                        return phom;
                    }
                    continue;
                }
            }
            /*kiem tra phom doc*/
            /*sap xep lai cac cay trong mang tang dan*/
            cardArr = CardArr.SapXepCardTangDan(cardArr);
            for (int i = 1; i < cardArr.Length; i++)
            {
                if (cardArr[i].Chat == cardArr[i - 1].Chat &&
                    cardArr[i].So == (cardArr[i - 1].So + 1))
                {
                    if (i == cardArr.Length - 1)
                    {
                        /*neu da kiem tra den het cardArr thi cardArr đủ điều kiện tạo Phỏm*/
                        Phom phom = new Phom(cardArr);
                        return phom;
                    }
                    continue;
                }
            }
            return null;                        
        }

        /// <summary>
        /// Sắp xếp các cây theo thứ tự số tăng dần
        /// </summary>
        /// <param name="cardArr"></param>
        /// <returns></returns>
        public static Card[] SapXepCardTangDan(Card[] cardArr)
        {
            if (cardArr == null)
            {
                return cardArr;
            }
            for (int i = 0; i < cardArr.Length - 1; i++)
            {
                for (int j = i + 1; j < cardArr.Length; j++)
                {
                    if (cardArr[i].So.CompareTo(cardArr[j].So) > 0)
                    {
                        Card tmpCard = cardArr[i];
                        cardArr[i] = cardArr[j];
                        cardArr[j] = tmpCard;
                    }
                }
            }
            return cardArr;
        }

    }
}
