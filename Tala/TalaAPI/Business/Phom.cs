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
    public class Phom
    {
        int _Id;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int Id
        {
            get { return _Id; }
            set { _Id = value; }
        }

        Card[] _CardArr;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.Array, false, true)]
        public Card[] CardArray
        {
            get { return _CardArr; }
            set { _CardArr = value; }
        }
        
        internal Seat OfSeat; /*seat ma phom thuoc ve*/

        int _Pos = -1;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int Pos
        {
            get { return _Pos; }
            set { _Pos = value; }
        }

        public Phom(Card[] cardArr)
        {
            _CardArr = cardArr;
        }

    }
}
