using System;

namespace Quantum.Tala.Lib.XMLOutput
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


    /// <summary>
    /// Attrib này dùng để mark trên các Property của APIDataEntry. Nếu được mark, bộ XMLHttpHandler mới expose Property ấy để render XML
    /// </summary>
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

        bool _RenderWhileEmpty;
        public bool RenderWhileEmpty
        {
            get { return _RenderWhileEmpty; }
        }

        bool _AddChildListEntryToParentEntry;
        public bool AddChildListEntryToParentEntry
        {
            get { return _AddChildListEntryToParentEntry; }
        }



                
        /// <summary>
        /// 
        /// </summary>
        /// <param name="tagname">bỏ trống sẽ dùng chính PropertyName.toLower làm tagname</param>
        /// <param name="type">thích render XML dạng attrib hay dạng nested tag</param>
        /// <param name="listizetype"></param>
        /// <param name="renderWhileEmpty">Property được đánh dấu là dạng một giá trị đơn, dạng Array, hay dạng List</param>
        /// <param name="p_AddChildListEntryToParentEntry">Đưa các phần tử của list lên làm con của Entry cha (bỏ một mức tag là Property Name)</param>
        public ElementXMLExportAttribute(string tagname, DataOutputXMLType type, DataListizeType listizetype, bool renderWhileEmpty, bool p_AddChildListEntryToParentEntry)
        {
            _TagName = tagname;
            _OutputXMLType = type;
            _ListizeType = listizetype;
            _RenderWhileEmpty = renderWhileEmpty;
            _AddChildListEntryToParentEntry = p_AddChildListEntryToParentEntry;
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="tagname">bỏ trống sẽ dùng chính PropertyName.toLower làm tagname</param>
        /// <param name="type">thích render dạng attrib hay dạng nested tag</param>
        public ElementXMLExportAttribute(string tagname, DataOutputXMLType type) 
            : this(tagname, type, DataListizeType.Single, false, false)
        {
        }

    }
    
}
