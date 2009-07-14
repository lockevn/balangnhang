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
using GURUCORE.Framework.Business;

namespace TalaAPI
{
    /// <summary>
    /// implement a application with GURUCORE Design pattern
    /// </summary>
    public class TalaProgramApplication : ServiceOrientedApplication
    {
        public override void Start(object p_oParam)
        {  
            base.Start(p_oParam);
        }      
    }
}
