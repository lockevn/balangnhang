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

namespace TalaAPI.XMLRenderOutput
{
    public class APICommandStatusState
    {
        public const string OK = "ok";
        public const string FAIL = "fail";
    }

    public class APICommandStatus
    {
        public string _sStat;
        public string _sID;
        public string _sInfo;

        public APICommandStatus(string p_sStat, string p_sID, string p_sInfo)
        {
            if (p_sStat != APICommandStatusState.OK)
            {
                p_sStat = APICommandStatusState.FAIL;
            }

            _sStat = p_sStat;
            _sID = p_sID;
            _sInfo = p_sInfo;            
        }

        public APICommandStatus(bool p_bStat)
        {
            _sStat = p_bStat ? "ok" : "fail";
        }

        public override string ToString()
        {
            return string.Format("<e stat='{0}'><id>{1}</id><info>{2}</info></e>", _sStat, _sID, _sInfo);
        }
    }
}
