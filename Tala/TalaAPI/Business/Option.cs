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
    public class Option
    {
        int _TiGiaChip;
        public int TiGiaChip
        {
            get { return _TiGiaChip;  }
            set { _TiGiaChip = value;  }
        }

        bool _IsUKhan;
        public bool IsUKhan
        {
            get { return _IsUKhan;  }
            set { _IsUKhan = value; }
        }

        bool _IsGuiNoi;
        public bool IsGuiNoi
        {
            get { return _IsGuiNoi; }
            set { _IsGuiNoi = value; }
        }

        bool _IsGa;
        public bool IsGa
        {
            get { return _IsGa; }
            set { _IsGa = value; }
        }

        bool _IsChot;
        public bool IsChot
        {
            get { return _IsChot; }
            set { _IsChot = value; }
        }

        bool _DoiChoSauKhiU;
        public bool DoiChoSauKhiU
        {
            get {return _DoiChoSauKhiU;}
            set {_DoiChoSauKhiU = value;}
        }

        int _TurnTimeout;
        public int TurnTimeout
        {
            get { return _TurnTimeout; }
            set { _TurnTimeout = value; }
        }
    }
}
