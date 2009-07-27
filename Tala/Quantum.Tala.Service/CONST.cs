using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service
{
    public class CONST
    {
        /// <summary>
        /// Số sới tối đa có thể tạo cho Server này
        /// </summary>
        public const int MAX_SOI_ALLOW = 300;

        /// <summary>
        /// ,   Dấu phân cách các quân bài trong một chuỗi string biểu diễn CardList (VD 1 phỏm gồm 3 cây, phân cách nhau bởi dấu này
        /// </summary>
        public const char CARD_SEPERATOR_SYMBOL = ',';
        /// <summary>
        /// ^   Dấu phân cách các CardList(VD 2 phỏm liên tiếp, cách nhau bởi dấu này)
        /// </summary>
        public const char CARDLLIST_SEPERATOR_SYMBOL = '^';


        public const int MOM_POINTVALUE = 1000;
        public const int HALAO_POINTVALUE = 2000;

        /// <summary>
        /// 4   , Số Seat tối đa cho phép trong 1 sới
        /// </summary>
        public const int MAX_SEAT_IN_SOI_ALLOW = 4;
    }
}
