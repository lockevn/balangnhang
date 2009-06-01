using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Web;
using System.Web.Security;
using System.Web.UI;
using System.Web.UI.HtmlControls;
using System.Web.UI.WebControls;
using System.Web.UI.WebControls.WebParts;
using System.Web.Services;
using System.Web.Services.Protocols;
using System.Xml.Linq;
using System.Collections;
using TalaAPI.XMLRenderOutput;
using TalaAPI.Business;

namespace TalaAPI.Lib
{    
    //[WebService(Namespace = "http://api.tala.quantumme.com/")]
    //[WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
    public class XMLHttpHandler : IHttpHandler
    {
        protected internal string Stat = "fail";
        protected internal ArrayList Data = new ArrayList();
        protected internal ArrayList Cmd = new ArrayList();


        /// <summary>
        /// Set ContentType to xml, render Data and Cmd to xml
        /// </summary>
        /// <param name="context"></param>
        public virtual void ProcessRequest(HttpContext context)
        {            
            context.Response.ContentType = "text/xml";
            string sRenderedData = string.Empty;
            if(Data.Count > 0)
            {
                foreach (APIDataEntry data in Data)
                {
                    if (data != null)
                    {
                        sRenderedData += data.ToXMLString();
                    }
                }
                sRenderedData = "<data>" + sRenderedData + "</data>";
            }

            string sRenderedCmd = string.Empty;
            if(Cmd.Count > 0)
            {
                foreach (APICommandStatus cs in Cmd)
                {
                    sRenderedCmd += cs.ToString();
                    Stat = cs.Stat;
                }
                sRenderedCmd = "<cmd>" + sRenderedCmd + "</cmd>";
            }


            int nCurrentTurnOfThisRequestContext = -1;
            int nIsPlaying = -1;
            int nIsVanFinished = -1;
            /// Cố gắng lấy turn hiện tại nếu người chơi đang chơi dở, sới đang diễn ra
            TalaSecurity sec = new TalaSecurity(context, false);
            // người chơi đã login, đã ở trong sới
            if (sec.CurrentAU != null)
            {
                Soi soi = sec.CurrentAU.CurrentSoi;
                // người chơi hiện tại đã vào sới, sới đang chơi, có ván
                if (soi != null && soi.IsPlaying && soi.CurrentVan != null)
                {
                    // lấy currentTurn ra, ghi vào response
                    nCurrentTurnOfThisRequestContext = soi.CurrentVan.CurrentTurnSeatIndex;
                    nIsPlaying = soi.IsPlaying ? 1 : 0;
                    nIsVanFinished = soi.CurrentVan.IsFinished ? 1 : 0;
                }
            }

            // nếu turn >= 0 thì mới ghi ra, tiết kiệm tí response trả về
            context.Response.Write(
                    string.Format(
                    "<q stat='{0}' " 
                    + ((nCurrentTurnOfThisRequestContext < 0) ? string.Empty : "turn='{3}' ")
                    + ((nIsPlaying < 0) ? string.Empty : "isplaying='{4}' ")
                    + ((nIsVanFinished < 0) ? string.Empty : "isvanfinished='{5}' ")
                    + ">{1}{2}</q>"
                    , Stat, sRenderedData, sRenderedCmd, nCurrentTurnOfThisRequestContext, nIsPlaying, nIsVanFinished)
                );
        }


        public bool IsReusable
        {
            get
            {
                return false;
            }
        }
    }
}
