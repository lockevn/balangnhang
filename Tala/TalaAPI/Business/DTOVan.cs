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

using System.Collections.Generic;

using TalaAPI.XMLRenderOutput;
using TalaAPI.Lib;

namespace TalaAPI.Business
{
    public class DTOVan : APIDataEntry
    {
        Van _VanInfo;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public Van VanInfo
        {
            get { return _VanInfo; }
            set { _VanInfo = value; }
        }

        List<Card> _BaiDaAn = new List<Card>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Card> BaiDaAn
        {
            get { return _BaiDaAn; }
            set { _BaiDaAn = value; }
        }

        List<Card> _BaiDaDanh = new List<Card>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Card> BaiDaDanh
        {
            get { return _BaiDaDanh; }
            set { _BaiDaDanh = value; }
        }


        List<Phom> _PhomDaHa = new List<Phom>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Phom> PhomDaHa
        {
            get { return _PhomDaHa; }
            set { _PhomDaHa = value; }
        }
        
    }
}
