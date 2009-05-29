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
using System.Collections.Generic;
using System.Collections;

using System.Reflection;
using TalaAPI.Lib;
using System.Text;

namespace TalaAPI.XMLRenderOutput
{
    public class APIDataEntry : IAPIData
    {
        protected ArrayList Data = new ArrayList();

        #region IAPIData Members

        public virtual XElement ToXElement()
        {
            /// XML Element, đại diện cho chính DataEntry đang được xử lý này
            XElement thisx = new XElement(this.GetType().Name.ToStringSafetyNormalize());            
            
            // lấy tất cả các property Public, Instance (không phải tĩnh) của DataEntry
            PropertyInfo[] arrPro = this.GetType().GetProperties(BindingFlags.Public | BindingFlags.Instance);


            foreach (PropertyInfo pro in arrPro)
            {
                // tìm các Property có đánh dấu
                object[] attribs = pro.GetCustomAttributes(typeof(ElementXMLExportAttribute), true);
                if (attribs.Length <= 0)
                {
                    // ko mark attribute, ko xử lý gì hết
                    continue;
                }
                 
                ElementXMLExportAttribute at = (ElementXMLExportAttribute)attribs[0];
                string sName = at.TagName.IsNullOrEmpty() ? pro.Name.ToStringSafetyNormalize() : at.TagName;
                
                object xProElement = null;
                object oProValue = pro.GetValue(this, null);

                // nếu không muốn render khi giá trị rỗng, bỏ qua vòng foreach với property này luôn
                if ((oProValue == null || oProValue.ToString().IsNullOrEmpty()) && !at.RenderWhileEmpty)
                {
                    continue;
                }

                /// kiểm tra xem có phải dạng danh sách không (Array  hoặc List)
                ICollection list = oProValue as ICollection;
                if (list != null)
                {
                    #region  Xử lý render List các APIDataEntry
                    
                    foreach (APIDataEntry entry in list)
                    {
                        // muốn ghi các phần tử con của list (Property là list) là con trực tiếp của DataEntry
                        if (at.AddChildListEntryToParentEntry)
                        {
                            thisx.Add(entry.ToXElement());
                        }
                        else    // ghi dưới Element có name = PropertyName
                        {
                            xProElement = new XElement(sName, entry.ToXElement());
                        }
                    }

                    #endregion
                }
                else    // đây là trường dữ liệu đơn
                {
                    #region Xử lý trường dữ liệu đơn                                       
                    

                    // tuỳ kiểu mà render thành nested tag hay attrib                    
                    if (at.OutputXMLType == DataOutputXMLType.NestedTag)
                    {
                        // nếu là APIDataEntry, render nó tiếp
                        if (pro.PropertyType.IsSubclassOf(typeof(APIDataEntry)))
                        {
                            oProValue = (oProValue as APIDataEntry).ToXElement();
                        }
                        else
                        {
                            // nếu là đối tượng kiểu thường, ấn thẳng vào, render ra bằng ToString                            
                        }
                        xProElement = new XElement(sName, oProValue);
                    }
                    else
                    {
                        // nếu là render dạng attrib
                        xProElement = new XAttribute(sName, oProValue);
                    }

                    #endregion
                }

                thisx.Add(xProElement);                
            }  // end foreach Property

            return thisx;            
        }

        public virtual string ToXMLString()
        {  
            return ToXElement().ToString();
        }



        //XDocument bookStoreXml = new XDocument(
        //       new XDeclaration("1.0", "utf-8", "yes"),
        //       new XComment("Bookstore XML Example"),
        //       new XElement("bookstore",
        //        new XElement("genre",
        //         new XAttribute("name", "Fiction"),
        //         new XElement("book",
        //             new XAttribute("ISBN", "10-861003-324"),
        //             new XAttribute("Title", "A Tale of Two Cities"),
        //             new XAttribute("Price", "19.99"),
        //             new XElement("chapter", "Abstract...",
        //                 new XAttribute("num", "1"),
        //                 new XAttribute("name", "Introduction")
        //                 ),
        //              new XElement("chapter", "Abstract...",
        //                  new XAttribute("num", "2"),
        //                  new XAttribute("name", "Body")
        //                  )
        //                )
        //            )
        //        )
        //      );

        #endregion
    }
}
