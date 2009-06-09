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
using TalaAPI.XMLRenderOutput;


using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Exception;

namespace TalaAPI.test
{
    public partial class p2 : TalaPage
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;


            pln("<pre>");

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
            soi.SetReady(lam);
            pln("dan lockevn dung lamlt ready");
            pln("all ok");
            pln("Ván start");
            pln("================================================================================");

            
            for (int i = 0; i < 16; i++)
            {
                Seat seat = soi.SeatList[soi.CurrentVan.CurrentTurnSeatIndex];
                try
                {
                    Card caybocduoc = soi.CurrentVan.Boc(seat);
                    pln(seat.Player.Username + " bốc  " + caybocduoc);
                }
                catch { }

                pln(seat.Player.Username + " đánh " + seat.BaiTrenTay[0]);
                soi.CurrentVan.Danh(seat, seat.BaiTrenTay[0]);

                pln("---------");
            }

        }
    }
}
