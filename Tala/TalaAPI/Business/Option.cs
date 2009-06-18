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

namespace TalaAPI.Business
{
    public class Option
    {
        int _TiGiaChip;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int TiGiaChip
        {
            get { return _TiGiaChip;  }
            set { _TiGiaChip = value;  }
        }

        bool _IsUKhan;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsUKhan
        {
            get { return _IsUKhan;  }
            set { _IsUKhan = value; }
        }

        bool _IsGuiNoi;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsGuiNoi
        {
            get { return _IsGuiNoi; }
            set { _IsGuiNoi = value; }
        }

        bool _IsGa;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsGa
        {
            get { return _IsGa; }
            set { _IsGa = value; }
        }

        bool _IsChot;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsChot
        {
            get { return _IsChot; }
            set { _IsChot = value; }
        }

        bool _DoiChoSauKhiU;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool DoiChoSauKhiU
        {
            get {return _DoiChoSauKhiU;}
            set {_DoiChoSauKhiU = value;}
        }

        int _TurnTimeout;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int TurnTimeout
        {
            get { return _TurnTimeout; }
            set { _TurnTimeout = value; }
        }

        public Option()
        {
            this._IsChot = true;
            this._IsGa = true;
            this._IsGuiNoi = true;
            this._IsUKhan = true;
            this._TiGiaChip = 1;
            
            
        }


        public static int CHIP_AN_CHOT = 4; /*số chip người bị ăn chốt phải trả cho người ăn chốt*/
        public static int CHIP_MOM     = 4; /*số chip người móm phải trả cho người nhất*/
        public static int CHIP_U       = 5; /*số chip người ù ăn được của mỗi người chơi*/
        public static int CHIP_U_TRON  = 10; /*số chip người ù tròn ăn được của mỗi người chơi*/
        
        public static int CHIP_DEN     = 5; /*số chip phải đền cho mỗi người chơi*/
        public static int CHIP_DEN_U_TRON = 10; /*số chip phải đền cho mỗi người chơi khi bi thang u trong an 3 cay*/

        public static int CHIP_NOP_GA   = 1; /*số chip phải nộp vào gà với mỗi lần bị ăn*/
        public static int CHIP_BET      = 3; /*số chip người bét phải nộp cho người nhất*/
        public static int CHIP_BA       = 2; /*số chip người ba phải nộp cho người nhất*/
        public static int CHIP_NHI      = 1; /*số chip người nhì phải nộp cho người nhất*/



    }
}
