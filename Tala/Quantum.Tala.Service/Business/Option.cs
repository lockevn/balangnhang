using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;

using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Lớp này chỉ dùng để chứa dữ liệu, không đưa method vào đây
    /// </summary>
    [ElementXMLExportAttribute("o", DataOutputXMLType.NestedTag)]
    public class Option : APIDataEntry
    {
        int _TiGiaChip;
        /// <summary>
        /// Trong ván sẽ trừ điểm cộng điểm bằng chip. Vậy các bạn chơi muốn đổi 1 chip thành mấy VCoin?
        /// </summary>
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
        /// <summary>
        /// Số giây mà hệ thống chờ user thực hiện thao tác khi đến lượt, nếu timeout hệ thống sẽ xử lý
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]        
        public int TurnTimeout
        {
            get { return _TurnTimeout; }
            set { _TurnTimeout = value; }
        }

        /// <summary>
        /// có cho người khác vào xem không?
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsAllowToViewer { get; set; }


        /// <summary>
        /// Khởi tạo option mặc định cho Sới. 
        /// Tính chốt, tính gà, cho gửi nối, cho ù khan, tỉ giá chíp = 1, không đổi chỗ sau gửi, timeout = 60s
        /// </summary>
        public Option()
        {
            this._TiGiaChip = 1;
            this._IsUKhan = true;
            this._IsGuiNoi = true;
            this._IsGa = true; 
            this._IsChot = true;
            this._DoiChoSauKhiU = false;
            this._TurnTimeout = 60;
            IsAllowToViewer = true;
        }


    }
}
