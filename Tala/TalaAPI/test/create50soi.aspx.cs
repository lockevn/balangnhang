using System;
using System.Collections;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Xml.Linq;

using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Exception;
using Quantum.Tala.Service.Authentication;

namespace TalaAPI.test
{
    public partial class create50soi : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;
            
            TalaUser u1 = song.LoginVaoSongChoi("v101", "vtc");
            pln(u1.Username + " login rồi");

            for (int i = 0; i < 50; i++)
            {
                Soi soi = song.CreatNewSoiOfTour("test soi paging " + i, 1);
                pln("đã tạo sới [" + soi.ID + "] " + soi.Name);
            }

            pln("========================================================================================");
            pln("all ok");
        }        
    }
}
