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

namespace TalaAPI.play.van
{    
    public class an : XMLHttpHandler
    {
        public override void ProcessRequest(HttpContext context)
        {
            Soi soi = new Soi(1, "danhut");                        

            User u = new User();
            u.Username = "danhut";
            soi.JoinSoi(u);

            u = new User();
            u.Username = "lockevn";
            soi.JoinSoi(u);

            u = new User();
            u.Username = "lamlt";
            soi.JoinSoi(u);

            u = new User();
            u.Username = "minh.phan";
            soi.JoinSoi(u);

            Van newVan = soi.CreateVan(false);
                        
            base.ProcessRequest(context);

        }    
    }
}
