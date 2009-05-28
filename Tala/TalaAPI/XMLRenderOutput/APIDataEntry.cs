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

namespace TalaAPI.XMLRenderOutput
{
    public class APIDataEntry : IAPIData
    {
        protected ArrayList Data = new ArrayList();

        #region IAPIData Members

        public virtual XElement ToXElement()
        {
            XElement thisx = new XElement(this.GetType().Name.ToStringSafetyNormalize());

            //string sRenderedData = string.Empty;
            //if (Data.Count > 0)
            //{
            //    foreach (APIDataEntry data in Data)
            //    {
            //        sRenderedData += data.ToString();
            //    }
            //    x.Add(new XElement("data", sRenderedData));
            //}

            PropertyInfo[] arrPro = this.GetType().GetProperties(BindingFlags.Public | BindingFlags.Instance);
            foreach (PropertyInfo pro in arrPro)
            {
                object[] attribs = pro.GetCustomAttributes(typeof(ElementXMLExportAttribute), true);
                if (attribs.Length < 0)
                {
                    // ko mark attribute, ko sinh ra gì hết
                    continue;
                }
                 
                ElementXMLExportAttribute at = (ElementXMLExportAttribute)attribs[0];
                string sName = at.TagName.IsNullOrEmpty() ? pro.Name.ToStringSafetyNormalize() : at.TagName;


                object child = null;
                if (at.ListizeType == DataListizeType.Array)
                {
                    child = new XElement(sName);
                    Array list = pro.GetValue(this, null) as Array;
                    if (list != null)
                    {
                        foreach (APIDataEntry entry in list)
                        {
                            XElement xEntryInList = entry.ToXElement();
                            (child as XElement).Add(xEntryInList);
                        }
                    }
                }
                else if (at.ListizeType == DataListizeType.ListGeneric)
                {
                    child = new XElement(sName);
                    List<APIDataEntry> list = pro.GetValue(this, null) as List<APIDataEntry>;
                    if (list != null)
                    {
                        foreach (APIDataEntry entry in list)
                        {
                            XElement xEntryInList = entry.ToXElement();
                            (child as XElement).Add(xEntryInList);
                        }
                    }
                }
                else
                {
                    // tuỳ kiểu mà render thành nested tag hay attrib                    
                    if (at.OutputXMLType == DataOutputXMLType.NestedTag)
                    {
                        // nếu là APIDataEntry, render nó tiếp
                        object oValue = null;
                        if (pro.PropertyType.IsSubclassOf(typeof(APIDataEntry)))
                        {
                            oValue = (pro.GetValue(this, null) as APIDataEntry).ToXElement();
                        }
                        else
                        {
                            // nếu là đối tượng kiểu thường, ấn thẳng vào, render ra bằng ToString
                            oValue = pro.GetValue(this, null);
                        }
                        child = new XElement(sName, oValue);
                    }
                    else
                    {
                        // nếu là render dạng attrib
                        object oValue = pro.GetValue(this, null);
                        child = new XAttribute(sName, oValue);
                    }
                }

                thisx.Add(child);

            }
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
