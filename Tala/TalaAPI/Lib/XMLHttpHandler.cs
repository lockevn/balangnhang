using System.Collections;
using System.Collections.Generic;
using System.Web;
using System.Xml.Linq;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Exception;
using System.Text;

namespace TalaAPI.Lib
{    
    //[WebService(Namespace = "http://api.tala.quantumme.com/")]
    //[WebServiceBinding(ConformsTo = WsiProfiles.BasicProfile1_1)]
    public class XMLHttpHandler : IHttpHandler
    {
        /// <summary>
        /// Mặc định là fail. Sau khi xử lý xong trong API, mọi thứ đều đúng, phải set lại thành OK
        /// </summary>
        protected internal string Stat = APICommandStatusState.FAIL;
        protected internal ArrayList Data = new ArrayList();
        protected internal ArrayList Cmd = new ArrayList();
        protected internal List<XElement> XElementData = new List<XElement>();

        /// <summary>
        /// Nhồi trực tiếp đoạn text này vào dưới root tag của response trả về
        /// </summary>
        protected internal string StringDirectUnderRoot = string.Empty;
        

        /// <summary>
        /// Set ContentType to xml, render Data and Cmd to xml
        /// </summary>
        /// <param name="context"></param>
        public virtual void ProcessRequest(HttpContext context)
        {
            /// change the response content type
            context.Response.ContentType = "text/xml";

            #region sRenderedData
                        
            StringBuilder sRenderedData = new StringBuilder();
            if(Data.Count > 0)
            {
                foreach (APIDataEntry data in Data)
                {
                    if (data != null)
                    {
                        sRenderedData.Append(data.ToXMLString());
                    }
                }
                sRenderedData.Insert(0, "<data>");
                sRenderedData.Append("</data>");
            }
            
            #endregion


            #region Rendered CommandStatus
                        
            string sRenderedCmd = string.Empty;
            if(Cmd.Count > 0)
            {
                foreach (APICommandStatus cs in Cmd)
                {
                    sRenderedCmd += cs.ToXMLString();
                    Stat = cs.Stat;
                }
                sRenderedCmd = "<cmd>" + sRenderedCmd + "</cmd>";
            }
            #endregion


            #region Rendered XMLData
            
            
            StringBuilder sRenderedXElementData = new StringBuilder();
            foreach (XElement x in XElementData)
            {
                sRenderedXElementData.Append(x.ToString());
            }
            
            #endregion


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
                if (soi != null && soi.CurrentVan != null)
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
                    + ((nCurrentTurnOfThisRequestContext < 0) ? string.Empty : "turn='{1}' ")
                    + ((nIsPlaying < 0) ? string.Empty : "isplaying='{2}' ")
                    + ((nIsVanFinished < 0) ? string.Empty : "isvanfinished='{3}' ")
                    + ">{4}{5}{6}{7}</q>"
                    , Stat, nCurrentTurnOfThisRequestContext, nIsPlaying, nIsVanFinished,
                    sRenderedData.ToString(), sRenderedCmd, sRenderedXElementData.ToString(), StringDirectUnderRoot)
                );
        }


        public bool IsReusable
        {
            get
            {
                return false;
            }
        }



        /// <summary>
        /// Hàm giúp send nhanh một COmmandStatus báo cho client biết khi gặp lỗi Exception, cần phun ra
        /// </summary>
        /// <param name="bex"></param>
        /// <param name="context"></param>
        public virtual void SendErrorAPICommand(BusinessException bex, HttpContext context)
        {
            APICommandStatus cs = new APICommandStatus(APICommandStatusState.FAIL, bex.Source, bex.ErrorMessage);
            XMLHttpHandler httphandler = new XMLHttpHandler();
            httphandler.Cmd.Add(cs);
            httphandler.ProcessRequest(context);
            context.Response.End();
        }


    }
}
