using System;
using System.Collections;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;
using Quantum.Tala.Service.Business;
using System.Collections.Generic;
using TalaAPI.Lib;
using System.Reflection;

namespace TalaAPI.test
{
    public partial class viewnoc : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            //Response.ContentType = "text";            
            Van van  = Song.Instance.GetSoiByID("352").CurrentVan;

            if (null != van)
            {
                foreach (PropertyInfo o in van.GetType().GetProperties(BindingFlags.NonPublic | BindingFlags.Instance))
                {
                    pln(o.MemberType + " " + o.Name);
                }

                PropertyInfo _Noc = van.GetType().GetProperty("_Noc", BindingFlags.NonPublic | BindingFlags.Instance);
                if (_Noc != null)
                {
                    List<Card> noc = _Noc.GetValue(van, null) as List<Card>;
                    foreach (Card card in noc)
                    {
                        this.StringDirectUnderRoot += card.ToString() + " ";
                    }
                }
                else
                {
                    pln("--------Nọc null");
                }
            }
            else
            {
                pln("---------Van null");
            }
            
        }
    }
}
