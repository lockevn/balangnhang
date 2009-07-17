using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Quantum.Tala.Service.Business;

namespace Quantum.Tala.Service.DTO
{
    /// <summary>
    /// Người xem của một sới, đã được phép xem
    /// Có thể hỗ trợ cho xem bài của một người chơi (mà họ đã đồng ý)
    /// </summary>
    public class SoiViewer
    {
        /// <summary>
        /// Người xem là ai
        /// </summary>
        public TalaUser User { get; set; }
        
        /// <summary>
        /// Đã được cho phép xem sới nào
        /// </summary>
        public Soi ViewingSoi { get; set; }
        
        /// <summary>
        /// Đã được cho phép xem Seat nào
        /// </summary>
        public Seat AllowToViewSeat { get; set; }
    }
}
