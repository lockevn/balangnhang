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
    public partial class login2nguoi_tuchoi : APIPageASPX
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            Song song = Song.Instance;
            
            pln("<pre>");
            
            TalaUser v3 = song.LoginVaoSongChoi("v0", "vtc");
            TalaUser v4 = song.LoginVaoSongChoi("v1", "vtc");
            pln(v3.Username + " login rồi");
            pln(v4.Username + " login rồi");
            
            Soi soi = song.CreatNewFreeSoi("test chơi 2 người" + FunctionExtension.GetRandomGUID(), v3.Username);
            pln(v3.Username + " tạo sới " + soi.ID + " -    " + soi.Name);
                        
            soi.AddPlayer(v3.Username);
            pln(v3.Username + " vào");
            soi.AddPlayer(v4.Username);
            pln(v4.Username + " vào");
            
            soi.SetReady(v3);
            soi.SetReady(v4);
            pln("2 user ready");
            pln("all ok");
            soi.StartPlaying(); 
            pln("Ván start");
            pln("================================================================================");
                        
            
            for (int i = 0; i < (soi.SeatList.Count * 4) ; i++)
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
