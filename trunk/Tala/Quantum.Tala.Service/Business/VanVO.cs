using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Xml.Linq;
using System.Collections.Generic;

using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// DTO dành cho hiển thị thông tin về ván. ViewObject
    /// </summary>
    [ElementXMLExportAttribute("van", DataOutputXMLType.NestedTag)]
    public class VanVO : APIDataEntry
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

        List<Card> _BaiTrenTay = new List<Card>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Card> BaiTrenTay
        {
            get { return _BaiTrenTay; }
            set { _BaiTrenTay = value; }
        }


        List<Phom> _PhomDaHa = new List<Phom>();
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Phom> PhomDaHa
        {
            get { return _PhomDaHa; }
            set { _PhomDaHa = value; }
        }
        

        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int IsCurrentPlayerDaBoc
        {
            get 
            {
                Seat seatCurrentInTurn = _VanInfo.SoiDangChoi.GetSeatOfCurrentInTurn();
                if (seatCurrentInTurn.GetTotalCardOnSeat() > 9)
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
            }
        }

    }
}
