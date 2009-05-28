using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;

namespace TalaAPI.Lib
{
    public enum DataOutputXMLType
    { 
        NestedTag,
        Attribute
    }
        
    public enum DataListizeType
    { 
        Single,
        Array,
        ListGeneric
    }

    [AttributeUsage(AttributeTargets.Property)]
    public class ElementXMLExportAttribute : System.Attribute
    {
        string _TagName;
        public string TagName
        {
            get { return _TagName; }            
        }

        DataOutputXMLType _OutputXMLType;
        public DataOutputXMLType OutputXMLType
        {
            get { return _OutputXMLType; }
        }

        DataListizeType _ListizeType;
        public DataListizeType ListizeType
        {
            get { return _ListizeType; }
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="tagname">bỏ trống sẽ dùng chính PropertyName.toLower làm tagname</param>
        /// <param name="type">thích render dạng attrib hay dạng nested tag</param>
        /// <param name="listizetype">Property được đánh dấu là dạng đơn, dạng Array, hay dạng List</param>
        public ElementXMLExportAttribute(string tagname, DataOutputXMLType type, DataListizeType listizetype)
        {
            _TagName = tagname;
            _OutputXMLType = type;
            _ListizeType = listizetype;
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="tagname">bỏ trống sẽ dùng chính PropertyName.toLower làm tagname</param>
        /// <param name="type">thích render dạng attrib hay dạng nested tag</param>
        public ElementXMLExportAttribute(string tagname, DataOutputXMLType type) 
            : this(tagname, type, DataListizeType.Single)
        {
        }

    }
    
}
