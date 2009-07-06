using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;


namespace Quantum.Tala.Service.Business
{
    public class CardMatrix
    {
        private int[,] _IntMatrix;
        public int[,] IntMatrix
        {
            get
            {
                return this._IntMatrix;
            }
        }

        private int _RowLength;

        /// <summary>
        /// row count of matrix
        /// </summary>
        public int RowLength
        {
            get
            {
                return this._RowLength;
            }

            set
            {
                this._RowLength = value;
            }
        }

        public static List<int[]>[] SCAN_DIRECTION 
        {
            get
            {
                List<int[]>[] tmpArr = new List<int[]>[4];
                /**
                 * 1: quét top-down (quét phỏm ngang)
                 * 2: quét left-right (quét phỏm dọc)                                  
                 */

                /*nếu số lượng phỏm ngang = 0, chỉ quét top-down*/
                tmpArr[0] = new List<int[]>();
                tmpArr[0].Add(new int[] { 2, 2, 2 });

                /*nếu số lượng phỏm ngang = 1, có 3 tổ hợp quét*/
                tmpArr[1] = new List<int[]>();
                tmpArr[1].Add(new int[] { 1, 2, 2 });
                tmpArr[1].Add(new int[] { 1, 2, 2 });
                tmpArr[1].Add(new int[] { 2, 1, 2 });

                /*nếu số lượng phỏm ngang = 2, có 3 tổ hợp quét*/
                tmpArr[2] = new List<int[]>();
                tmpArr[2].Add(new int[] { 1, 1, 2 });
                tmpArr[2].Add(new int[] { 1, 2, 1 });
                tmpArr[2].Add(new int[] { 2, 1, 1 });

                /*nếu số lượng phỏm ngang = 3, có 1 tổ hợp quét*/
                tmpArr[3] = new List<int[]>();
                tmpArr[3].Add(new int[] { 1, 1, 1 });
                return tmpArr;
            }
            
        }
        
        public CardMatrix( int[,] arrArr, int rowLength)
        {
            this._IntMatrix = new int[rowLength, 4];

            for (int i = 0; i < rowLength; i++)
            {
                for (int j = 0; j < 4; j++)
                {
                    this._IntMatrix[i,j] = arrArr[i,j];
                }
            }            
            this._RowLength = rowLength;
        }

        /// <summary>
        /// remove all upper-bound and lower-bound empty rows (containing 4 zeros)
        /// không làm thay đổi input matrix
        /// </summary>        
        /// <returns></returns>
        public CardMatrix Compress()
        {            
            int notEmptyRowStartIndex = 0;            
            /*mark not empty row start index*/
            for (int i = 0; i < this.RowLength; i++)
            {
                bool isAllZero = true;
                /*check if row i contains all zero*/
                for (int j = 0; j < 4; j++)
                {                   
                    if (this.IntMatrix[i, j] != 0)
                    {
                        isAllZero = false;
                        notEmptyRowStartIndex = i;
                        break;                        
                    }
                }
                /*stop if one notEmptyRow is found*/
                if (!isAllZero)
                {
                    break;
                }                
            }
            
            /*mark not empty row end index*/
            int notEmptyRowEndIndex = this.RowLength - 1;
            for (int i = this.RowLength - 1; i >= 0; i--)
            {
                bool isAllZero = true;
                /*check if row i contains all zero*/
                for (int j = 0; j < 4; j++)
                {
                    if (this.IntMatrix[i, j] != 0)
                    {
                        isAllZero = false;
                        notEmptyRowEndIndex = i;
                        break;
                    }
                }
                /*stop if one notEmptyRow is found*/
                if (!isAllZero)
                {
                    break;
                }
            }

            /*create the copressed array by copying non-empty rows of the input array*/
            int newRowLength = (notEmptyRowEndIndex == 0) ? 0 : (notEmptyRowEndIndex - notEmptyRowStartIndex + 1);
            int[,] newIntArrArr = new int[newRowLength, 4];
            for (int i = 0; i < newRowLength; i++)
            {
                for (int j = 0; j < 4; j++)
                {
                    newIntArrArr[i, j] = this.IntMatrix[i + notEmptyRowStartIndex, j];
                }
            }
            return new CardMatrix(newIntArrArr, newRowLength);
        }

        /// <summary>
        /// lật các phần tử của ma trận theo trục đối xứng dọc
        /// không làm thay đổi this
        /// a[i,j] = a[i,3-j] (j=0,1)
        /// </summary>        
        /// <returns>new matrix</returns>
        public CardMatrix CreateVerticalSymmetricMatrix()
        {
            int[,] tmpIntMatrix = new int[this.RowLength, 4];
            for (int i = 0; i < this.RowLength; i++)
            {
                for (int j = 0; j < 2; j++)
                {                    
                    tmpIntMatrix[i, j] = this.IntMatrix[i, 3 - j];
                    tmpIntMatrix[i, 3 - j] = this.IntMatrix[i, j];
                }
            }
            return new CardMatrix(tmpIntMatrix, this.RowLength);
        }

        /// <summary>
        /// lật các phần tử của ma trận theo trục đối xứng ngang
        /// không làm thay đổi this
        /// a[i,j] = a[rowLength - 1 - i, j] (i=0, rowLength / 2)
        /// </summary>        
        /// <returns>new matrix</returns>
        public CardMatrix CreateHorizontalSymmetricMatrix()
        {
            int[,] tmpIntMatrix = new int[this.RowLength, 4];
            for (int i = 0; i < this.RowLength / 2; i++)
            {
                for (int j = 0; j < 4; j++)
                {

                    tmpIntMatrix[i, j] = this.IntMatrix[this.RowLength - 1 - i, j];
                    tmpIntMatrix[this.RowLength - 1 - i, j] = this.IntMatrix[i, j];
                    
                }
            }
            /*neu so row la le copy row o giua*/
            if (this.RowLength % 2 != 0)
            {
                for (int j = 0; j < 4; j++)
                {
                    tmpIntMatrix[this.RowLength / 2, j] = this.IntMatrix[this.RowLength / 2, j];                    
                }
            }

            return new CardMatrix(tmpIntMatrix, this.RowLength);
        }

        /// <summary>
        /// create card matrix [13,4] from a list of card
        /// </summary>
        /// <param name="cardList"></param>
        /// <returns></returns>
        public static CardMatrix ParseCardListToCardMatrix(List<Card> baiTrenTay, List<Card> baiDaAn)
        {
            int[,] intArrArr = new int[13, 4];

            /*đánh dấu các cây ở bài trên tay với giá trị = 1*/
            foreach (Card card in baiTrenTay)
            {
                intArrArr[card.SoIndex, card.ChatIndex] = 1;
            }

            /*đánh dấu các cây đã ăn với giá trị = 10
             lấy giá trị = 10 để kiểm tra phỏm có chứa 2 cây đã ăn không (tổng các phần tử trên row hoặc col >= 20)
             */
            foreach (Card card in baiDaAn)
            {
                intArrArr[card.SoIndex, card.ChatIndex] = 10;
            }
            CardMatrix tmpCardArrArr = new CardMatrix(intArrArr, 13);
            return tmpCardArrArr;
        }

        /// <summary>
        /// print out to screen for testing
        /// </summary>
        public void PrintArrArr()
        {
            for (int i = 0; i < this.RowLength; i++)
            {
                for (int j = 0; j < 4; j++)
                {
                    System.Console.Out.WriteLine("arrArr[" + i + "," + j + "] = " + this.IntMatrix[i, j]);
                }
            }
        }

        /// <summary>
        /// quét một row trong matrix. Nếu row có chứa phỏm -> trả về số lượng card trong phỏm, đồng thời set các phần tử tạo phỏm đó về 0.
        /// </summary>        
        /// <param name="rowIndex"></param>        
        /// <param name="resetRequired">có cần set các phần tử 1 về 0 không</param>        
        /// <returns>số card tạo phỏm hợp lệ, nếu không tìm đc phỏm return 0</returns>
        private int ScanSingleRow(int rowIndex, bool resetRequired, bool check2Cay1PhomRequired)
        {
            if (rowIndex >= this.RowLength || rowIndex < 0)
            {
                return 0;
            }
            List<int> indexList = new List<int>();
            
            int found = 0;
            int rowValue = 0;
            for (int i = 0; i < 4; i++)
            {
                if (this.IntMatrix[rowIndex, i] != 0)
                {
                    found++;
                    rowValue += this.IntMatrix[rowIndex, i];
                    /*đánh dấu lại vị trí phần tử để sau này reset nếu tạo phỏm*/
                    if (resetRequired)
                    {
                        indexList.Add(i);
                    }
                }
            }
            /*nếu tìm thấy phỏm và phỏm không chứa > 1 cây trên bài đã ăn, 
             * reset các phần tử đã duyệt và trả về số cây trong phỏm*/
            if (check2Cay1PhomRequired && found >= 3 && rowValue < 20 
                || !check2Cay1PhomRequired && found >=3)
            {
                /*reset matrix elements đã tạo phỏm*/
                if (resetRequired)
                {
                    foreach (int colIndex in indexList)
                    {
                        this.IntMatrix[rowIndex, colIndex] = 0;
                    }
                }
                return found;
            }
            return 0;
        }

        /// <summary>
        /// Quét 1 cột trong matrận, nếu gặp 1 phỏm -> trả về tổng số cây trong phỏm đó,đồng thời reset các phần tử tạo phỏm của matrận về 0
        /// </summary>
        /// <param name="matrix"></param>
        /// <param name="colIndex"></param>
        /// <returns>tổng số cây tạo phỏm, nếu 0 tồn tại phỏm trả về 0</returns>
        private int ScanSingleCol(int colIndex, bool resetRequired)
        {
            if (colIndex >= 4 || colIndex < 0)
            {
                return 0;
            }
            /*duyet cot colIndex*/
            int startIndex = -1; /*đánh dấu rowindex bắt đầu 1 phỏm*/            
            bool found = false;
            int foundCount = 0;
            List<int> indexList = new List<int>();
            int colValue = 0; /*tính giá trị của column*/
            for (int i = 0; i < this.RowLength; i++)
            {
                if (this.IntMatrix[i, colIndex] != 0)
                {
                    colValue += this.IntMatrix[i, colIndex];
                    int count = i - startIndex;
                    /*lưu vị trí của phần tử này lại để reset sau này nếu tạo đc phỏm*/
                    if (resetRequired)
                    {
                        indexList.Add(i);
                    }
                    /*nếu đủ điều kiện tạo phỏm, ghi nhận số cây trọng phỏm*/
                    if (count > 2)
                    {
                        found = true;
                        foundCount = count;
                    }
                }
                else
                {
                    /*nếu đã tìm đc phỏm thì reset các phần tử đã đánh dấu, và return số cây tạo phỏm*/
                    if (found)
                    {
                        break;
                    }
                    startIndex = i;
                    /*xóa các vị trí đã đánh dấu trong indexList để check 1 phỏm mới*/
                    if (resetRequired)
                    {
                        indexList.Clear();
                    }
                    /*reset colValue về 0 để check 1 phỏm mới*/
                    colValue = 0;
                }
            }
            /*nếu phỏm chứa 2 cây đã ăn trở lên -> return 0*/
            if (colValue >= 20)
            {
                return 0;
            }
            /*nếu đã tìm đc phỏm thì reset các phần tử đã đánh dấu, và return số cây tạo phỏm*/
            if (found)
            {
                if (resetRequired)
                {
                    foreach (int rowIndex in indexList)
                    {
                        this.IntMatrix[rowIndex, colIndex] = 0;
                    }
                }
            }
            return foundCount;
        }

        #region tmp

        //private int ScanSingleCol(CardMatrix matrix, int colIndex)
        //{
        //    if (colIndex >= 4 || colIndex < 0)
        //    {
        //        return 0;
        //    }
        //    /*duyet cot colIndex*/
        //    int startIndex = 0; /*đánh dấu rowindex bắt đầu 1 phỏm*/
        //    int endIndex = 0; /*đánh dấu rowindex kết thúc 1 phỏm*/
        //    List<int> foundList = new List<int>(); /*đếm số cây bài trong từng phỏm của cột*/
        //    int foundIndex = -1; /*chỉ số phỏm đã tìm thấy*/
        //    bool newPhomFound = false;
        //    for (int i = 0; i < matrix.RowLength; i++)
        //    {
        //        if (matrix.IntMatrix[i, colIndex] == 1)
        //        {
        //            endIndex = i;
        //            int count = endIndex - startIndex;
        //            /*nếu đủ điều kiện tạo phỏm, ghi nhận số cây trọng phỏm vào found*/
        //            if (count >= 2)
        //            {
        //                /*nếu tìm thấy phỏm mới -> tăng foundIndex*/
        //                if (!newPhomFound)
        //                {
        //                    newPhomFound = true;
        //                    foundIndex++;
        //                }
        //                /*cập nhật số lượng cây trong phỏm vào phỏm thứ foundIndex*/
        //                foundList.Insert(foundIndex, count + 1);
        //            }
        //        }
        //        else
        //        {
        //            newPhomFound = false;
        //            startIndex = i;
        //        }
        //    }
        //    /*tính tổng số cây bài trong tất cả các phỏm*/
        //    int totalCard = 0;
        //    foreach (int count in foundList)
        //    {
        //        totalCard += count;
        //    }
        //    return totalCard;
        //}
        #endregion

        /// <summary>
        /// quét 1 matrận theo một tập hợp hướng quét và trả về tổng số cây trong các phỏm tồn tại trong ma trận đó
        /// </summary>
        /// <param name="matrix"></param>
        /// <param name="direction">tổ hợp hướng quét</param>
        /// <returns></returns>
        public int ScanMatrix(int[] direction)
        {            
            int totalCount = 0; /*total card in all form found*/
            for (int i = 0; i < 3; i++)
            {
                int tmpCount = 0;
                /*quét top-down*/
                if (direction[i] == 1)
                {
                    for (int rowIndex = 0; rowIndex < this.RowLength; rowIndex++)
                    {
                        tmpCount = this.ScanSingleRow(rowIndex, true, true);
                        /*nếu tìm thấy phỏm thì dừng lại để đọc direction tiếp theo*/
                        if (tmpCount >= 3)
                        {                           
                            break;
                        }
                    }
                }
                /*quét left-right*/
                else 
                {
                    for (int colIndex = 0; colIndex < 4; colIndex++)
                    {
                        tmpCount = this.ScanSingleCol(colIndex, true);
                        /*nếu tìm thấy phỏm thì dừng lại để đọc direction tiếp theo*/
                        if (tmpCount >= 3)
                        {                            
                            break;
                        }
                    }
                }
                totalCount += tmpCount;
                /*nếu tìm được 10 cây -> ù tròn thoát luôn*/
                if (totalCount == 10)
                {
                    break;
                }
            }
            return totalCount;
        }

        public int CountPhomNgang()
        {
            int phomCount = 0;
            for (int i = 0; i < this.RowLength; i++)
            {
                int tmp = this.ScanSingleRow(i, false, false);
                if (tmp > 0)
                {
                    phomCount++;
                }
            }
            return phomCount;
        }
    }
}
