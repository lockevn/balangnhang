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
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Lib;

namespace TalaAPI.Lib
{
    public class APIParamHelper
    {
        /// <summary>
        /// Kiểm tra (trong context) xem paramName có được truyền lên API không. kết quả được trả về.
        /// </summary>
        /// <param name="paramName">tên param cần lấy</param>
        /// <param name="context"></param>
        /// <param name="NeedToEndRequest">nếu true, khi param ko có, sẽ phun lỗi và kết thúc request</param>
        /// <returns></returns>
        public static string GetParam(string paramName, HttpContext context, bool NeedToEndRequest)
        {            
            string value = context.Request[paramName].ToStringSafetyNormalize();
            if (value.Length == 0)
            {
                if (NeedToEndRequest)
                {
                    APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, "EMPTY_PARAM", "missing parameter: " + paramName);
                    XMLHttpHandler httphandler = new XMLHttpHandler();
                    httphandler.Cmd.Add(cs);
                    httphandler.ProcessRequest(context);
                    context.Response.End();
                }
            }
            
            return value;            
        }

        /// <summary>
        /// kết thúc request nếu param ko có. Dùng hàm này khi cần param nào mandatory
        /// </summary>
        /// <param name="paramName"></param>
        /// <param name="context"></param>
        /// <returns></returns>
        public static string GetParam(string paramName, HttpContext context)
        {
            return GetParam(paramName, context, true);
        }
       
    }
}
