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
    public partial class loginnhieunguoi : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;

            int n = 7;
            for (int i = 0; i < n; i++)
            {
                TalaUser u = song.LoginVaoSongChoi("v" + i, "vtc");
            }
            pln(n + " người login rồi");

            //Soi soi = song.CreatNewFreeSoi("test soi", "v1");
            //pln("v1 tạo sới " + soi.ID);

            //soi.AddPlayer("v2");
            //pln("v2 vào");            
            //soi.AddPlayer("v3");
            //pln("v3 vào");
            
            ////soi.AddPlayer("v4");
            ////pln("v4 vào");

            //soi.SetReady(dan);
            //soi.SetReady(thach);
            //soi.SetReady(dung);
            //pln("dan lockevn dung ready");
            //soi.SetReady(lam);

            pln("all ok");   
        }        
    }
}
