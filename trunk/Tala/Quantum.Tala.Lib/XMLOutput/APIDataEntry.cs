using System.Collections;
using System.Reflection;
using System.Xml.Linq;

namespace Quantum.Tala.Lib.XMLOutput
{
    /// <summary>
    /// Mô tả một đối tượng dữ liệu có thể render thành XML
    /// </summary>
    public class APIDataEntry : IAPIData
    {
        protected ArrayList Data = new ArrayList();
        
        #region IAPIData Members

        public virtual string ToXMLString()
        {
            return this.ToXElement().ToString(SaveOptions.DisableFormatting);
        }

        #endregion
    }
}
