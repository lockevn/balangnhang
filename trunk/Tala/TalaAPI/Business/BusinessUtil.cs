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
using TalaAPI.Exception;
using System.Collections.Generic;

namespace TalaAPI.Business
{
    public static class BusinessUtil
    {
        public static Card ToCard(this string s)
        {
            return Card.ParseString(s);
        }


        /// <summary>
        /// Check 1 tập cards có tạo thành phỏm không, nếu đúng, trả về một Phom
        /// </summary>
        /// <param name="cardArr">mảng Card</param>
        /// <returns>Phom nếu thỏa mãn, null nếu 0 đủ điều kiện tạo phỏm</returns>
        public static Phom IsValidPhom(this Card[] cardArr)
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
                /*khong phai phom ngang*/
                break;
            }
            /*kiem tra phom doc*/
            /*sap xep lai cac cay trong mang tang dan*/
            cardArr = cardArr.SapXepCardTangDan();
            for (int i = 1; i < cardArr.Length; i++)
            {
                if (cardArr[i].Chat == cardArr[i - 1].Chat &&
                    int.Parse(cardArr[i].So) == (int.Parse(cardArr[i - 1].So) + 1))
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
        public static Card[] SapXepCardTangDan(this Card[] cardArr)
        {
            if (cardArr == null)
            {
                return cardArr;
            }
            
            for (int i = 0; i < cardArr.Length - 1; i++)
            {
                for (int j = i + 1; j < cardArr.Length; j++)
                {
                    if (cardArr[i] > cardArr[j])
                    {
                        Card tmpCard = cardArr[i];
                        cardArr[i] = cardArr[j];
                        cardArr[j] = tmpCard;
                    }
                }
            }
            return cardArr;
        }

        
        public static string ToTalaString(this Card[] cardArr)
        {
            string sRet = string.Empty;
            foreach(Card c in cardArr)
            {
                sRet += c.ToString() + CONST.CARD_SEPERATOR_SYMBOL;
            }
            sRet = sRet.TrimEnd(',');
            return sRet;
        }


        public static Card[] StringArrayToCardArray(this string[] cardStrArr) 
        {
            Card[] cardArr = new Card[cardStrArr.Length];
            int i = 0;
            foreach (string cardStr in cardStrArr)
            {                 
                /*if CardException occurs, throw it to higher level*/
                Card card = Card.ParseString(cardStr);                
                cardArr[i] = card;
                i++;
            }
            return cardArr;
        }


        /// <summary>
        /// parse string of format "{string,string,…^string,string,…} into List<Card[]>"
        /// </summary>
        /// <param name="value"></param>
        /// <returns></returns>
        public static List<Card[]> StringToCardList(string value)
        {
            string[] stringArr = value.Split('^');
            List<Card[]> cardArrList = new List<Card[]>();
            /*tạo List<Card[]> tu stringArr*/
            foreach (string tmpStr in stringArr)
            {
                string[] cardStrArr = tmpStr.Split(',');
                Card[] cardArr = BusinessUtil.StringArrayToCardArray(cardStrArr);
                cardArrList.Add(cardArr);                
            }
            return cardArrList;
        }
    }
}
