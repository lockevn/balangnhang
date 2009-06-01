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
            
            context.Response.Write("4 người login rồi");

            Soi soi = song.CreatSoiMoi("test soi", "lamlt");
            context.Response.Write("lamlt tạo sới");

            soi.AddPlayer("lockevn");
            context.Response.Write("lockevn vào");
            //soi.AddPlayer("lamlt");
            soi.AddPlayer("dung");
            context.Response.Write("dung vào");
            soi.AddPlayer("danhut");
            context.Response.Write("danhut vào");

            soi.SetReady(dan);
            soi.SetReady(thach);            
            soi.SetReady(dung);
            context.Response.Write("dan lockevn dung ready");

            //soi.SetReady(lam);

            context.Response.Write("all ok");                   
        }    
    }
}
