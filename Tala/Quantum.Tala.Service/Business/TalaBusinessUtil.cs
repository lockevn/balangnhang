using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Xml.Linq;
using System.Collections.Generic;
using Quantum.Tala.Lib;
using Quantum.Tala.Service.Exception;


namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// chứa các hàm mở rộng, hàm tĩnh để thao tác trên các đối tượng dữ liệu của tá lả
    /// </summary>
    public static class TalaBusinessUtil
    {
        
        /// <summary>
        /// Chuyển từ string sang Card, create a Card object from string with format SoSoChat: vd 01c (át cơ)
        /// </summary>
        /// <param name="s"></param>
        /// <returns>card object if valid, otherwise null</returns>
        public static Card ToCard(this string p_s)
        {
            Card cardRet = null;

            if (p_s == null || p_s.Length != 3)
            {
                throw new CardException("invalid card string format: " + p_s);
            }

            string so = p_s.Substring(0, 2);
            string chat = p_s.Substring(2, 1);
            // hợp lệ mới tạo obj Card
            if (Card.SO_SET.Contains(so) && Card.CHAT_SET.Contains(chat))
            {
                cardRet = new Card(so, chat);
                return cardRet;
            }

            throw new CardException("invalid card string format: " + p_s);
        }


        /// <summary>
        /// Check 1 dãy các Cards có tạo thành phỏm không, nếu đúng, trả về một Phom
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
                return null;
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

        /// <summary>
        /// Ghép các chuỗi card (Card.ToString) bằng CONST.CARD_SEPERATOR_SYMBOL
        /// </summary>
        /// <param name="cardArr"></param>
        /// <returns></returns>
        public static string ToTalaString(this Card[] cardArr)
        {
            string sRet = string.Empty;
            foreach(Card c in cardArr)
            {
                sRet += c.ToString() + CONST.CARD_SEPERATOR_SYMBOL;
            }

            sRet = sRet.TrimEnd(CONST.CARD_SEPERATOR_SYMBOL);
            return sRet;
        }

        /// <summary>
        /// Convert một string[] theo kiểu {"00x", "00y", "00z"} thành Card[]
        /// </summary>
        /// <param name="cardStrArr"></param>
        /// <returns></returns>
        public static Card[] StringArrayToCardArray(this string[] cardStrArr) 
        {
            Card[] cardArr = new Card[cardStrArr.Length];
            int i = 0;
            foreach (string cardStr in cardStrArr)
            {                 
                /*if CardException occurs, throw it to higher level*/
                Card card = cardStr.ToCard();                
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
            string[] stringArr = value.Split(CONST.CARDLLIST_SEPERATOR_SYMBOL);
            
            List<Card[]> cardArrList = new List<Card[]>();
            
            /*tạo List<Card[]> tu stringArr*/
            foreach (string s in stringArr)
            {
                string[] cardStrArr = s.Split(CONST.CARD_SEPERATOR_SYMBOL);
                Card[] cardArr = TalaBusinessUtil.StringArrayToCardArray(cardStrArr);
                cardArrList.Add(cardArr);                
            }

            return cardArrList;
        }


        /// <summary>
        /// tìm trong bộ bài cardListToLookup, xem cây truyền vào có tạo phỏm được với các cây khác không
        /// </summary>
        /// <param name="cardAnchor"></param>
        /// <param name="cardListToLookup"></param>
        /// <returns></returns>
        public static List<Card> InspectPhomOfCard(Card cardAnchor, List<Card> cardListToLookup)
        {
            //cardListToLookup.Remove(cardAnchor);

            // TODO: sai, kiểm lại đi LockeVN
            List<Card> phomPotential = new List<Card>();
            phomPotential.Add(cardAnchor);

            // thử tìm phỏm ngang
            foreach (Card card in cardListToLookup)
            {
                if (cardAnchor == card)
                {
                    continue;   // trong trường hợp card cần theo dõi 
                }

                if (cardAnchor.So == card.So)
                {
                    phomPotential.Add(card);
                }
            }
            if (phomPotential.Count >= 3)
            {
                return phomPotential;
            }
            else
            {
                phomPotential.Clear();
                phomPotential.Add(cardAnchor);
            }

            // reset, tìm lại với phỏm dọc
            foreach (Card card in cardListToLookup)
            {
                if (cardAnchor.Chat == card.Chat)
                {
                    int nSoCardDaAn = int.Parse(cardAnchor.So);
                    int nSoCard = int.Parse(card.So);

                    if (nSoCardDaAn - 1 == nSoCard || nSoCardDaAn + 1 == nSoCard ||
                        nSoCardDaAn - 2 == nSoCard || nSoCardDaAn + 2 == nSoCard
                        )
                    {
                        phomPotential.Add(card);
                    }
                }
            }

            if (phomPotential.Count >= 3)
            {
                return phomPotential;
            }
            else
            {
                return null;
            }
        }
        
    }
}
