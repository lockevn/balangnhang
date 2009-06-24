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

namespace TalaAPI.Lib
{
    public class CONST
    {
        /// <summary>
        /// Số sới tối đa có thể tạo cho Server này
        /// </summary>
        public const int MAX_SOI_ALLOW = 300;
        
        /// <summary>
        /// Dấu phân cách các quân bài trong một chuỗi string biểu diễn CardList (VD 1 phỏm gồm 3 cây, phân cách nhau bởi dấu này
        /// </summary>
        public const char CARD_SEPERATOR_SYMBOL = ',';
        /// <summary>
        /// Dấu phân cách các CardList(VD 2 phỏm liên tiếp, cách nhau bởi dấu này)
        /// </summary>
        public const char CARDLLIST_SEPERATOR_SYMBOL = '^';


        public static int MOM_POINTVALUE = 1000;
    }
}
