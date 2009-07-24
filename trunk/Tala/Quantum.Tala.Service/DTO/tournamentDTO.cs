using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Lib;
using System.Xml.Linq;

namespace Quantum.Tala.Service.DTO
{
    [ElementXMLExportAttribute("t", DataOutputXMLType.NestedTag)]
    public sealed partial class tournamentDTO : IAPIData
    {
        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int ID
        {
            get 
            {
                return m_nid;
            }
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string Name
        {
            get
            {
                return m_sname;
            }
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string Desc
        {
            get
            {
                return m_sdesc;
            }
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string StartTime
        {
            get
            {
                return m_dtstarttime.ToUTCString();
            }            
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public string EndTime
        {
            get
            {
                return m_dtendtime.ToUTCString();
            }            
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int MinRequiredPlayer
        {
            get
            {
                return m_nminrequiredplayer;
            }            
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public bool IsStart
        {
            get
            {
                return m_bisstart;
            }            
        }        

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int StartupPoint
        {
            get
            {
                return m_nstartuppoint;
            }            
        }

        [ElementXMLExport("", DataOutputXMLType.Attribute)]
        public int EnrollFee
        {
            get
            {
                return m_nenrollfee;
            }            
        }


        #region IAPIData Members

        public string ToXMLString()
        {
            return this.ToXElement().ToString(SaveOptions.DisableFormatting);
        }

        #endregion
    }
}
