﻿using System;
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

        public TalaSecurity(HttpContext context)
        {
            _context = context;
            _authkey = context.Request["authkey"].ToStringSafetyNormalize();
            _user = Song.Instance.GetUserByUsername(Song.Instance.GetUsernameByAuthkey(_authkey));

            if (_user == null)
            {
                XMLHttpHandler httphandler = new XMLHttpHandler();
                httphandler.Cmd.Add(APICommandStatus.Get_WRONG_AUTHKEY_CommandStatus());
                httphandler.ProcessRequest(context);
                context.Response.End();
            }
        }

        public Soi CheckUserJoinedSoi()
        {
            Soi soi = this.CurrentAU.CurrentSoi;
            if (soi == null)
            {
                XMLHttpHandler httphandler = new XMLHttpHandler();
                httphandler.Cmd.Add(APICommandStatus.Get_NOT_JOINED_SOI_CommandStatus());
                httphandler.ProcessRequest(this._context);
                this._context.Response.End();
            }
            return soi;
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
