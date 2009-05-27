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
        string _sStat;
        public string Stat
        {
            get { return _sStat; }
            set {
                _sStat = (value == APICommandStatusState.OK ? APICommandStatusState.OK : APICommandStatusState.FAIL);
            }
        }
        string _sID;
        public string ID
        {
            get { return _sID; }
            set { _sID = value; }
        }
        string _sInfo;
        public string Info
        {
            get { return _sInfo; }
            set { _sInfo = value; }
        }

        public APICommandStatus(string p_sStat, string p_sID, string p_sInfo)
        {
            Stat = p_sStat;
            _sID = p_sID;
            _sInfo = p_sInfo;            
        }

        public APICommandStatus(bool p_bStat)
        {
            _sStat = p_bStat ? APICommandStatusState.OK : APICommandStatusState.FAIL;
        }

        public override string ToString()
        {
            return string.Format("<e stat='{0}'><id>{1}</id><info>{2}</info></e>", _sStat, _sID, _sInfo);
        }

        public static APICommandStatus Get_NOT_ALLOW_CommandStatus()
        {
            return new APICommandStatus(APICommandStatusState.FAIL, "NOT_ALLOW", "");
        }

        public static APICommandStatus Get_NOT_VALID_CommandStatus()
        {
            return new APICommandStatus(APICommandStatusState.FAIL, "NOT_VALID", "");
        }

        public static APICommandStatus Get_WRONG_AUTHKEY_CommandStatus()
        {
            return new APICommandStatus(APICommandStatusState.FAIL, "WRONG_AUTHKEY", " Bạn chưa đăng nhập");
        }

        public static APICommandStatus Get_NOT_JOINED_SOI_CommandStatus()
        {
            return new APICommandStatus(APICommandStatusState.FAIL, "NOT_JOINED_SOI", " Bạn chưa vào sới");
        }

        public static APICommandStatus Get_NOT_JOINED_SEAT_CommandStatus()
        {
            return new APICommandStatus(APICommandStatusState.FAIL, "NOT_JOINED_SEAT", " Bạn chưa có chỗ trong sới");
        }
    }
}
