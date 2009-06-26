using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Lib.XMLOutput
{
    /// <summary>
    /// Lớp nào cài đặt Interface này sẽ có khả năng render ra XML bằng hàm ToXMLString()
    /// </summary>
    interface IAPIData
    {
        string ToXMLString();
    }
}
