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
using Quantum.Tala.Lib.XMLOutput;


using TalaAPI.Lib;using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Exception;
using Quantum.Tala.Service.Authentication;

namespace TalaAPI.test
{
    public partial class p2 : TalaPage
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;


            pln("<pre>");

            TalaUser v1 = song.LoginVaoSongChoi("v1", "quantum");
            TalaUser v2 = song.LoginVaoSongChoi("v2", "quantum");
            TalaUser v3 = song.LoginVaoSongChoi("v3", "quantum");
            TalaUser v4 = song.LoginVaoSongChoi("v4", "quantum");

            pln("4 người login rồi");

            Soi soi = song.CreatSoiMoi("test soi", v1.Username);
            pln(v1.Username + " tạo sới");

            soi.AddPlayer(v2.Username);
            pln(v2.Username + " vào");
            soi.AddPlayer(v3.Username);
            pln(v3.Username + " vào");
            soi.AddPlayer(v4.Username);
            pln(v4.Username + " vào");
            
            

            soi.SetReady(v1);
            soi.SetReady(v2);
            soi.SetReady(v3);
            soi.SetReady(v4);
            pln("4 user ready");
            pln("all ok");
            pln("Ván start");
            pln("================================================================================");

            soi.StartPlaying();
            
            for (int i = 0; i < 16; i++)
            {
                Seat seat = soi.GetSeatOfCurrentInTurn();
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

            pln("---------");
            pln("---------");
            foreach (Message msg in soi.CurrentVan.MessageList)
            {
                pln(msg.Msg);                                
            }

        }
    }
}
