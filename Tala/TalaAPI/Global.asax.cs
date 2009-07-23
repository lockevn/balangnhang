using System;
using System.Collections;
using System.Configuration;
using System.Data;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.SessionState;
using System.Xml.Linq;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Lib;

// Load the configuration from the 'WebApp.dll.log4net' file
[assembly: log4net.Config.XmlConfigurator(ConfigFileExtension = "log4net", Watch = true)]

namespace TalaAPI
{
    public class Global : System.Web.HttpApplication
    {

        protected void Application_Start(object sender, EventArgs e)
        {
            //mount point for GURUCORE GApplication in ASP .NET
            string sRoot = HttpRuntime.AppDomainAppPath;
            GURUCORE.Framework.Core.GApplication.GetInstance().Start(sRoot);
            
            string sWebRootPhysicalPath = Server.MapPath("/Config");
            //System.Diagnostics.Debug.Print(sWebRootPhysicalPath);
            DBHelper.Instance.Init(sWebRootPhysicalPath);

            Song value = Song.Instance;
            this.Application.Add("song", value);
            
        }

        protected void Session_Start(object sender, EventArgs e)
        {

        }

        protected void Application_BeginRequest(object sender, EventArgs e)
        {

        }

        protected void Application_AuthenticateRequest(object sender, EventArgs e)
        {

        }

        protected void Application_Error(object sender, EventArgs e)
        {
            // Nếu bung lên exception không catch, tới mức này là sẽ phun ra Response
        }

        protected void Session_End(object sender, EventArgs e)
        {

        }

        protected void Application_End(object sender, EventArgs e)
        {

        }
    }
}