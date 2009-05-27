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
using TalaAPI.XMLRenderOutput;


namespace TalaAPI.Lib
{
    public class APIParamHelper
    {
        public static string CheckEmptyParam(string paramName, HttpContext context)
        {
            
            string value = context.Request[paramName].ToStringSafetyNormalize();
            if (value.Length == 0)
            {
                APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "EMPTY_PARAM", "empty parameter: " + paramName);
                XMLHttpHandler httphandler = new XMLHttpHandler();
                httphandler.Cmd.Add(cs);
                httphandler.ProcessRequest(context);
                context.Response.End();                
            }
            return value;            
        }
                
    }
}
