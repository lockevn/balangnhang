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
using TalaAPI.Lib;
namespace TalaAPI.Exception
{
    public class BusinessException : System.Exception
    {
        public String ErrorMessage;
        public BusinessException(string source, string message)
        {
            this.Source = source;            
            this.ErrorMessage = message;
        }
        public BusinessException(string message)
        {
            this.Source = "NOT_VALID";
            this.ErrorMessage = message;
        }

        public void SendErrorAPICommand(HttpContext context)
        {
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, this.Source, this.ErrorMessage);            
            XMLHttpHandler httphandler = new XMLHttpHandler();
            httphandler.Cmd.Add(cs);
            httphandler.ProcessRequest(context);
            context.Response.End();
        }
    }
}
