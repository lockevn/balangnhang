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
using log4net;

// Load the configuration from the 'WebApp.dll.log4net' file
[assembly: log4net.Config.XmlConfigurator(ConfigFileExtension = "log4net", Watch = true)]

namespace TalaAPI
{
    public class Global : System.Web.HttpApplication
    {
        private static readonly ILog log = LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

        protected void Application_Start(object sender, EventArgs e)
        {
            //mount point for GURUCORE GApplication in ASP.NET
            // load config, database, ORM ...
            string sRoot = HttpRuntime.AppDomainAppPath;
            GURUCORE.Framework.Core.GApplication.GetInstance().Start(sRoot);
            log.Info("GURUCORE Application instance  start at " + sRoot);
            
            Song song = Song.Instance;
            this.Application.Add("song", song);
            log.Info("New Song added to ApplicationState");
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