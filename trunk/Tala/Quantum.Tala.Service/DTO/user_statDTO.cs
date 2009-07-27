using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;
using Quantum.Tala.Lib.XMLOutput;
using System.Xml.Linq;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for user_statDTO.
	/// </summary>
    [ElementXMLExportAttribute("stat", DataOutputXMLType.NestedTag)]
    public sealed partial class user_statDTO : IAPIData
	{
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string Username { get { return u; } }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Win { get{return win;}  }
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Lose { get{return lose;}  }
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int Point { get{return point;}  }

        #region IAPIData Members

        public string ToXMLString()
        {
            return this.ToXElement().ToString(SaveOptions.DisableFormatting);
        }

        #endregion
    }
}
