using System;

namespace Quantum.Tala.Lib.XMLOutput
{
    public class APICommandStatusState
    {
        public const string OK = "ok";
        public const string FAIL = "fail";
    }

    /// <summary>
    /// Biểu diễn kết quả của một lệnh được API thực hiện
    /// </summary>
    public class APICommandStatus : IAPIData
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

        public APICommandStatus(bool p_bStat)
        {
            _sStat = p_bStat ? APICommandStatusState.OK : APICommandStatusState.FAIL;
        }

        public APICommandStatus(bool p_sStat, string p_sID, string p_sInfo) : this(p_sStat)
        {            
            _sID = p_sID;
            _sInfo = p_sInfo;
        }

        public APICommandStatus(string p_sStat, string p_sID, string p_sInfo)
        {
            Stat = p_sStat;
            _sID = p_sID;
            _sInfo = p_sInfo;            
        }
        


        public string ToXMLString()
        {
            return string.Format("<e stat='{0}'><id>{1}</id><info>{2}</info></e>", _sStat, _sID, _sInfo);
        }



        #region Một số CommandStatus thường gặp, có thể tạo nhanh bằng các hàm tĩnh này        
        
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

        #endregion


        #region IAPIData Members

        string IAPIData.ToXMLString()
        {
            throw new NotImplementedException();
        }

        #endregion
    }
}
