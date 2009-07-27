﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Data.Common;


using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.DTO;


namespace Quantum.Tala.Service.Business
{
    [ElementXMLExportAttribute("user", DataOutputXMLType.NestedTag)]
    public class TalaUser : APIDataEntry, IUser
    {

        #region IUser Members

        string _sUsername;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string Username
        {
            get
            {
                return _sUsername.ToStringSafetyNormalize();
            }
            set
            {
                _sUsername = value;
            }
        }

        string _sAuthkey;
        public string Authkey
        {
            get
            {
                return _sAuthkey;
            }
            set
            {
                _sAuthkey = value;
            }
        }


        public string System
        {
            get;
            set;
        }

        public string FullIdentity
        {
            get;
            set;
        }

        #endregion


        public string Password { get; set; }
        public int Money { get; set; }
        
        
        /// <summary>
        /// For displaying only
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string CurrentPlayingPos
        {
            get
            {
                if (_CurrentSoi != null)
                {
                    return _CurrentSoi.GetSeatByUsername(_sUsername).Pos.ToString();
                }
                else
                {
                    return null;
                }
            }
        }

        Soi _CurrentSoi;
        public Soi CurrentSoi
        {
            get { return _CurrentSoi; }
            set { _CurrentSoi = value; }
        }

        /// <summary>
        /// Code thể hiện trạng thái của User hiện tại ngay sau khi họ login. VD: trạng thái: chưa đăng ký, chưa trả tiền, chưa được chơi...
        /// </summary>
        public string StatusCode { get; set; }


        
        public user_statDTO UserStatDBEntry { get; set; }



        public TalaUser()
        { }
        public TalaUser(string p_sUsername, string p_sAuthkey)
        {
            _sUsername = p_sUsername;
            _sAuthkey = p_sAuthkey;
        }





        /// <summary>
        /// cộng tiền cho của user, ghi persist xuống DB
        /// </summary>
        /// <param name="value"></param>
        /// <returns>trả về số tiền được cập nhật xuống DB</returns>
        internal int AddMoney(int value, EnumPlayingResult why)
        {
            Money += value;
            
            // update xuống DB
            value = Cashier.AddGoldOfUser(this.Username, value, why, this._CurrentSoi.GetCurrentTournament());

            return value;
        }

        /// <summary>
        /// trừ tiền của user, ghi persist xuống DB
        /// </summary>
        /// <param name="value"></param>
        /// <returns>trả về số tiền được cập nhật xuống DB</returns>
        internal int SubtractMoney(int value, EnumPlayingResult why)
        {
            Money -= value;

            // update xuống DB
            value = Cashier.SubtractGoldOfUser(this.Username, value, why, this._CurrentSoi.GetCurrentTournament());

            return value;
        }





        
    }
}
