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
        public Card[] phom;
        public Seat seat; /*seat ma phom thuoc ve*/

        public Phom()
        {
        }

        /// <summary>
        /// Check 1 tập cards có tạo thành phỏm không
        /// </summary>
        /// <param name="cardArr">mảng Card</param>
        /// <returns>true/false</returns>
        public static bool Check(Card[] cardArr)
        {
            if (cardArr == null || cardArr.Count < 3)
            {
                return false;
            }
            for (int i = 1; i < cardArr.Count; i++)
            {
                /*kiem tra phom ngang*/
                if (cardArr[i].So == cardArr[i - 1].So)
                {
                    if (i == cardArr.Count - 1)
                    {
                        /*neu da kiem tra den het cardArr*/
                        return true;
                    }
                    continue;
                }
            }
            /*kiem tra phom doc*/
            /*sap xep lai cac cay trong mang tang dan*/
            cardArr = Phom.SapXepCardTangDan(cardArr);
            for (int i = 1; i < cardArr.Count; i++)
            {
                if (cardArr[i].Chat == cardArr[i - 1].Chat &&
                    cardArr[i].So == (cardArr[i - 1].So + 1))
                {
                    if (i == cardArr.Count - 1)
                    {
                        /*neu da kiem tra den het cardArr*/
                        return true;
                    }
                    continue;
                }
            }
            return false;                        
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
            for (int i = 0; i < cardArr.Count - 1; i++)
            {
                for (int j = i + 1; j < cardArr.Count; j++)
                {
                    if (cardArr[i].So > cardArr[j].So)
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
