using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    public static class UUtil
    {

        public static int CheckU(List<Card> cardList)
        {
            CardMatrix cardMatrix = CardMatrix.ParseCardListToCardMatrix(cardList);
            cardMatrix = cardMatrix.Compress();
            int phomNgangCount = cardMatrix.CountPhomNgang();

            /*lấy về tập hợp các tổ hợp quét ứng với phomNgangCount*/
            List<int[]> directionList = CardMatrix.SCAN_DIRECTION.ElementAt(phomNgangCount);

            int maxCount = 0;

            //System.Console.Out.WriteLine("Phom ngang count: " + phomNgangCount + " Direction list: " + directionList.Count);
            System.Console.Out.WriteLine("======== original matrix ======= ");
            cardMatrix.PrintArrArr();
            /*quét matrix theo các tổ hợp quét*/
            foreach (int[] directionSet in directionList)
            {
                
                /*tạo các ma trận sẽ phải quét*/
                CardMatrix[] matrixArr = new CardMatrix[] 
                {
                    new CardMatrix(cardMatrix.IntMatrix, cardMatrix.RowLength),
                    cardMatrix.CreateHorizontalSymmetricMatrix(),
                    cardMatrix.CreateVerticalSymmetricMatrix()
                };
                
                for (int i = 0; i < 3; i++)
                {
                    CardMatrix tmpMatrix = matrixArr[i];
                    int count = tmpMatrix.ScanMatrix(directionSet);                    

                    System.Console.Out.WriteLine("======== matrix " + i + " count = " + count);
                    if (maxCount < count)
                    {
                        maxCount = count;
                    }
                    /*nếu ù tròn -> return*/
                    if (maxCount == 10)
                    {
                        return maxCount;
                    }
                }                                                                                
            }
            return maxCount;            
        }
    }
}
