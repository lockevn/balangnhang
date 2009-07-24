using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Lib;
using System.Xml.Linq;

namespace Quantum.Tala.Service.DTO
{
    [ElementXMLExportAttribute("u", DataOutputXMLType.NestedTag)]
    public sealed partial class userDTO : IAPIData
    {
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string Username { get; set;}
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Win { get; set; }
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Lose { get; set; }
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Point { get; set; }       


        #region IAPIData Members

        public string ToXMLString()
        {
            return this.ToXElement().ToString(SaveOptions.DisableFormatting);
        }

        #endregion
    }
}
