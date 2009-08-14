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
    public partial class login4nguoi_tuchoiden15luot : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;
                       

            TalaUser v1 = song.LoginVaoSongChoi("v1", "vtc");
            TalaUser v2 = song.LoginVaoSongChoi("v2", "vtc");
            TalaUser v3 = song.LoginVaoSongChoi("vtc23", "111111");
            TalaUser v4 = song.LoginVaoSongChoi("vtc24", "111111");

            pln("4 người login rồi");
            Soi soi = v1.CurrentSoi;

            if (soi == null)
            {
                soi = song.CreatNewSoiOfTour("test soi " + FunctionExtension.GetRandomGUID(), 1);
                pln(v1.Username + " tạo sới");

                soi.AddPlayer(v1.Username);
                pln(v1.Username + " vào");
                soi.AddPlayer(v2.Username);
                pln(v2.Username + " vào");
                soi.AddPlayer(v3.Username);
                pln(v3.Username + " vào");
                soi.AddPlayer(v4.Username);
                pln(v4.Username + " vào");
            }

            if (soi != null)
            {
                soi.SetReady(v1);
                soi.SetReady(v2);
                soi.SetReady(v3);
                soi.SetReady(v4);
                pln("4 user ready");
                pln("all ok");
                pln("Ván start");
                pln("======================================================================");

                soi.StartPlaying();


                int nRandomTurnToEat = 1111;//new Random(DateTime.Now.Millisecond).Next(0, 15);

                for (int i = 0; i < 12; i++)
                {
                    if(soi.IsPlaying == false)
                    {
                        break;
                    }

                    Seat seat = soi.GetSeatOfCurrentInTurn();
                    if (i == nRandomTurnToEat)
                    {
                        Card cayanduoc = soi.CurrentVan.An(seat);
                        pln("<b>" +                             
                            seat.Player.Username + " ăn " + cayanduoc +
                            "</b>");
                    }
                    else
                    {
                        Card caybocduoc = soi.CurrentVan.Boc(seat);
                        pln(seat.Player.Username + " bốc  " + caybocduoc);
                    }

                    pln(seat.Player.Username + " đánh <b>" + seat.BaiTrenTay.Last() + "</b>");
                    soi.CurrentVan.Danh(seat, seat.BaiTrenTay.Last());

                    if (i == 3 || i == 7 ||i == 11)
                    {
                        pln("---------");
                    }
                }

                if (soi.IsPlaying == false && soi.CurrentVan.IsFinished == true)
                {                    
                    pln("**************************************************************");
                    foreach (Message msg in soi.CurrentVan.MessageList)
                    {
                        pln(msg.Code + ": " + msg.Msg);
                    }
                }
            }

        }
    }
}
