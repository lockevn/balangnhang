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
    }
}
