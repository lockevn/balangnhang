using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;

using System.Collections.Generic;


using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Biểu diễn Một thông báo phát sinh khi chơi bài
    /// </summary>
    public class Message : APIDataEntry
    {
        int _id = 0;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public int ID
        {
            get { return _id; }
            set { _id = value; }
        }

        string _code;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string Code
        {
            get { return _code.ToLower(); }
            set { _code = value; }
        }

        string _msg;
        [ElementXMLExportAttribute("", DataOutputXMLType.Attribute)]
        public string Msg
        {
            get { return _msg.ToLower(); }
            set { _msg = value; }
        }

        public Message(string code, string msg)
        {
            _code= code;
            _msg = msg;
        }
    }    
}
