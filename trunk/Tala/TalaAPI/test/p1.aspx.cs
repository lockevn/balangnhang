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
    public partial class p1 : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;

            TalaUser dan = song.LoginVaoSongChoi("v1", "vtc");
            TalaUser thach = song.LoginVaoSongChoi("v2", "vtc");
            TalaUser lam = song.LoginVaoSongChoi("v3", "vtc");
            TalaUser dung = song.LoginVaoSongChoi("v4", "vtc");

            pln("4 người login rồi");

            Soi soi = song.CreatNewFreeSoi("test soi", "v1");
            pln("lamlt tạo sới");

            soi.AddPlayer("v2");
            pln("v2 vào");
            //soi.AddPlayer("lamlt");
            soi.AddPlayer("v3");
            pln("v3 vào");
            soi.AddPlayer("v4");
            pln("v4 vào");

            soi.SetReady(dan);
            soi.SetReady(thach);
            soi.SetReady(dung);
            pln("dan lockevn dung ready");

            //soi.SetReady(lam);

            pln("all ok");   
        }        
    }
}
