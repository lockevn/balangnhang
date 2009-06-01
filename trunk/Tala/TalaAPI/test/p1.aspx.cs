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

using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Exception;

namespace TalaAPI.test
{
    public partial class p1 : TalaPage
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;

            User dan = song.LoginVaoSongChoi("danhut", "quantum");
            User thach = song.LoginVaoSongChoi("lockevn", "quantum");
            User lam = song.LoginVaoSongChoi("lamlt", "quantum");
            User dung = song.LoginVaoSongChoi("dung", "quantum");

            pln("4 người login rồi");

            Soi soi = song.CreatSoiMoi("test soi", "lamlt");
            pln("lamlt tạo sới");

            soi.AddPlayer("lockevn");
            pln("lockevn vào");
            //soi.AddPlayer("lamlt");
            soi.AddPlayer("dung");
            pln("dung vào");
            soi.AddPlayer("danhut");
            pln("danhut vào");

            soi.SetReady(dan);
            soi.SetReady(thach);
            soi.SetReady(dung);
            pln("dan lockevn dung ready");

            //soi.SetReady(lam);

            pln("all ok");   
        }        
    }
}
