using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// lớp  chứa dữ liệu, box các giá trị truyền ra, phòng khi không dùng biến out được
    /// </summary>
    public class OutputResultObject
    {
        /// <summary>
        /// danh sách các output đã được sửa trong hàm
        /// </summary>
        public List<object> ValueList { get; set; }        

        public OutputResultObject()
        {
            ValueList = new List<object>();
        }
        
    }
}
