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

namespace TalaAPI.Exception
{
    public class NotInTurnException : BusinessException
    {
        public NotInTurnException(string message)
            : base(message)
        {
            this.Source = "NOT_ALLOW";
        }
       
    }
}
