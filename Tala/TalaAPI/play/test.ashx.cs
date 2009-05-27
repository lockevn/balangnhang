using System;
using System.Collections;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Exception;
using TalaAPI.Business;
using TalaAPI.Lib;
using TalaAPI.XMLRenderOutput;


namespace TalaAPI.play
{    
    public class test : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            Song song = Song.Instance;            

            User dan = song.LoginVaoSongChoi("danhut", "quantum");
            User thach = song.LoginVaoSongChoi("lockevn", "quantum");
            User lam = song.LoginVaoSongChoi("lamlt", "quantum");
            User dung = song.LoginVaoSongChoi("dung", "quantum");


            Soi soi = song.CreatSoiMoi("test soi", "danhut");
                                    
            soi.AddPlayer("lockevn");
            soi.AddPlayer("lamlt");
            soi.AddPlayer("dung");

            soi.SetReady(dan);
            soi.SetReady(thach);
            soi.SetReady(lam);
            soi.SetReady(dung);


            context.Response.Write("ok");
            
                                                            
        }    
    }
}
