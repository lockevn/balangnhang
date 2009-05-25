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

        public virtual string ToXMLString()
        {
            string sRenderedData = string.Empty;
            if (Data.Count > 0)
            {
                foreach (APIDataEntry data in Data)
                {
                    sRenderedData += data.ToString();
                }
                sRenderedData = "<data>" + sRenderedData + "</data>";
            }

            string sXMLString = string.Empty;
            PropertyInfo[] arrPro =  this.GetType().GetProperties();
            foreach (PropertyInfo pro in arrPro)
            {                
                sXMLString += string.Format("<{0}>{1}</{0}>", pro.Name.ToStringSafetyNormalize(), pro.GetValue(this, null));
            }
            sXMLString = string.Format("<{0}>{1}</{0}>", this.GetType().Name.ToStringSafetyNormalize(), sXMLString);

            return sXMLString += sRenderedData;
        }

        #endregion
    }
}
