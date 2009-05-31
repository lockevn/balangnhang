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
using TalaAPI.Business;
using TalaAPI.Lib;


namespace TalaAPI.Business
{
    public class User : APIDataEntry
    {   
        string _sUsername;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string Username
        {
            get
            {
                return _sUsername.ToStringSafetyNormalize();
            }
            set
            {
                _sUsername = value;
            }
        }

        string _sAuthkey;        
        public string Authkey
        {
            get
            {
                return _sAuthkey;
            }
            set
            {
                _sAuthkey = value;
            }
        }

        int _Money;
        /// <summary>
        /// Read only. Thuộc tính này là chỉ đọc, đọc ra số tiền hiện có của User
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int Money
        {
            get { return _Money; }
            set { _Money = value; }
        }

        /// <summary>
        /// For displaying only
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]        
        public string CurrentPlayingPos
        {
            get 
            {
                if (_CurrentSoi != null)
                {
                    return _CurrentSoi.GetSeatOfUserInSoi(_sUsername).Index.ToString();
                }
                else 
                {
                    return null; 
                }
            }
        }

        Soi _CurrentSoi;
        public Soi CurrentSoi
        {
            get { return _CurrentSoi; }
            set { _CurrentSoi = value; }
        }
        

        public User()
        {}        
        public User(string p_sUsername, string p_sAuthkey)
        {
            _sUsername = p_sUsername;
            _sAuthkey = p_sAuthkey;
        }





        /// <summary>
        /// cộng tiền cho của user, ghi persist xuống DB
        /// </summary>
        /// <param name="value"></param>
        /// <returns>trả về số tiền được cập nhật xuống DB</returns>
        internal int AddMoney(int value)
        {
            _Money += value;

            // update xuống DB
            value = DBUtil.AddMoneyOfUser(this.Username, value);

            return value;
        }

        /// <summary>
        /// trừ tiền của user, ghi persist xuống DB
        /// </summary>
        /// <param name="value"></param>
        /// <returns>trả về số tiền được cập nhật xuống DB</returns>
        internal int SubtractMoney(int value)
        {
            _Money -= value;

            // update xuống DB
            value = DBUtil.SubtractMoneyOfUser(this.Username, value);

            return value;
        }

    }
}
