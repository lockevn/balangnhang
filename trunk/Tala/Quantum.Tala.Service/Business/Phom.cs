using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;

using Quantum.Tala.Lib;

using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Business
{
    public class Phom : APIDataEntry
    {
        int _Id;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int Id
        {
            get { return _Id; }
            set { _Id = value; }
        }

        Card[] _CardArr;
        /// <summary>
        /// Các cây chứa trong phỏm
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.Array, false, true)]
        public Card[] CardArray
        {
            get { return _CardArr; }
            set { _CardArr = value; }
        }
        
        /// <summary>
        /// Phỏm này thuộc Seat nào
        /// </summary>
        internal Seat OfSeat { get; set; }
        
        int _Pos = -1;
        /// <summary>
        /// Nếu Pos có giá trị -1 nghĩa là chưa đc gán, Pos vô nghĩa
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int Pos
        {
            get { return _Pos; }
            set { _Pos = value; }
        }

        /// <summary>
        /// Tạo phỏm từ một mảng card
        /// </summary>
        /// <param name="cardArr"></param>
        public Phom(Card[] cardArr)
        {
            _CardArr = cardArr;
        }

    }
}
