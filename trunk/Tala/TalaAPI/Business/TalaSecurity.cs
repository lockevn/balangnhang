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

using TalaAPI.Lib;
using TalaAPI.Business;

namespace TalaAPI.Business
{
    public class TalaSecurity
    {
        HttpContext _context;
        string _authkey;
        public string CurrentAuthkey
        {
            get
            {                
                return _authkey;
            }
        }
        User _user;
        public User CurrentAU
        {
            get { return _user; }
        }

        public TalaSecurity(HttpContext context)
        {
            _context = context;
            _authkey = context.Request["authkey"].ToStringSafetyNormalize();
            _user = Song.Instance.GetUserByUsername(Song.Instance.GetUsernameByAuthkey(_authkey));
        }

    }
}
