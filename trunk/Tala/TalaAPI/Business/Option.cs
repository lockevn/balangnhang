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
    }
}
