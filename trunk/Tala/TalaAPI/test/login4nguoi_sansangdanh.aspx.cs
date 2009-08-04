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
    public partial class login4nguoi_sansangdanh : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;
                       

            TalaUser v1 = song.LoginVaoSongChoi("vtc21", "111111");
            TalaUser v2 = song.LoginVaoSongChoi("vtc22", "111111");
            TalaUser v3 = song.LoginVaoSongChoi("vtc23", "111111");
            TalaUser v4 = song.LoginVaoSongChoi("vtc24", "111111");

            v1.IP = "127.0.0.1";
            v2.IP = "127.0.0.1";
            v3.IP = "127.0.0.1";
            v4.IP = "127.0.0.1";


            pln("4 người login rồi");
            Soi soi = v1.CurrentSoi;

            if (soi == null)
            {
                soi = song.CreatNewSoiOfTour("test soi " + FunctionExtension.GetRandomGUID(), 2);
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
                if (soi.IsPlaying == false)
                {
                    soi.SetReady(v1);
                    soi.SetReady(v2);
                    soi.SetReady(v3);
                    soi.SetReady(v4);
                    pln("4 user ready");
                    pln("all ok");
                    pln("Ván start" + soi.StartPlaying());
                    pln("======================================================================");
                    
                }
                else
                {
                    pln("Người đang đến lượt là: " + soi.GetSeatOfCurrentInTurn().Player.UsernameInGame);
                    foreach (Card c in soi.CurrentVan._Noc)
                    {
                        p(c.ToString() + "-");
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
