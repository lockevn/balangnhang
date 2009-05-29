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
using System.Xml.Linq;

using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;

namespace TalaAPI.Business
{
    public class TalaSecurity
    {
        HttpContext _context;
        string _authkey;
        public string CurrentAuthkey
        {
            get
            {                
                return _authkey;
            }
        }
        User _user;
        public User CurrentAU
        {
            get { return _user; }
        }

        /// <summary>
        /// Khởi tạo, tìm các dữ liệu liên quan của người dùng đang đăng nhập hiện tại (current AU). Nếu không có authkey, sẽ tự động kết thúc request và trả về error notlogin
        /// </summary>
        /// <param name="context"></param>
        public TalaSecurity(HttpContext context, bool NeedToEndRequest)
        {
            _context = context;
            _authkey = context.Request["authkey"].ToStringSafetyNormalize();
            _user = Song.Instance.GetUserByUsername(Song.Instance.GetUsernameByAuthkey(_authkey));

            if (_user == null)
            {
                if (NeedToEndRequest)
                {
                    XMLHttpHandler httphandler = new XMLHttpHandler();
                    httphandler.Cmd.Add(APICommandStatus.Get_WRONG_AUTHKEY_CommandStatus());
                    httphandler.ProcessRequest(context);
                    context.Response.End();
                }
            }
        }

        /// <summary>
        /// New TalaSecurity object, if can not find authkey, return fail, end request intermediatelly
        /// </summary>
        /// <param name="context"></param>
        public TalaSecurity(HttpContext context) : this(context, true)
        {
        }

        /// <summary>
        /// Trả về sới hiện tại của user, endRequest với lỗi not JOIN_SOI nếu chưa vào sới
        /// </summary>
        /// <param name="NeedToEndRequest">nếu true, endrequest luôn, nếu failse</param>
        /// <returns></returns>
        public Soi CheckUserJoinedSoi(bool NeedToEndRequest)
        {
            Soi soi = this.CurrentAU.CurrentSoi;
            if (soi == null)
            {
                if (NeedToEndRequest)
                {
                    XMLHttpHandler httphandler = new XMLHttpHandler();
                    httphandler.Cmd.Add(APICommandStatus.Get_NOT_JOINED_SOI_CommandStatus());
                    httphandler.ProcessRequest(this._context);
                    this._context.Response.End();
                }
            }
            return soi;
        }
        /// <summary>
        /// Trả về sới hiện tại của user, endRequest với lỗi not JOIN_SOI nếu chưa vào sới
        /// </summary>
        /// <returns></returns>
        public Soi CheckUserJoinedSoi()
        {
            return CheckUserJoinedSoi(true);
        }

        public Seat CheckUserJoinedSeat()
        {
            Soi soi = this.CurrentAU.CurrentSoi;            
            Seat seat = soi.GetSeatOfUserInSoi(this.CurrentAU.Username);
            if (seat == null)
            {
                XMLHttpHandler httphandler = new XMLHttpHandler();
                httphandler.Cmd.Add(APICommandStatus.Get_NOT_JOINED_SEAT_CommandStatus());
                httphandler.ProcessRequest(this._context);
                this._context.Response.End();
            }
            return seat;
        }

    }
}
