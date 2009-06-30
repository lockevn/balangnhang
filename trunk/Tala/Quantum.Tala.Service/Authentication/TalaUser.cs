using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Data.Common;


using Quantum.Tala.Lib;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;


namespace Quantum.Tala.Service.Authentication
{
    public class TalaUser : APIDataEntry, IUser
    {

        #region IUser Members

        public string Password
        {
            get
            {
                return "";
            }
            set
            {
                throw new NotImplementedException();
            }
        }

        
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

        int _Money;
        /// <summary>
        /// Read only. Thuộc tính này là chỉ đọc, đọc ra số tiền hiện có của User
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int Money
        {
            get { return _Money; }
            set { _Money = value; }
        }

        #endregion

        
        
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
        internal int AddMoney(int value)
        {
            _Money += value;

            // update xuống DB
            value = Cashier.AddMoneyOfUser(this.Username, value);

            return value;
        }

        /// <summary>
        /// trừ tiền của user, ghi persist xuống DB
        /// </summary>
        /// <param name="value"></param>
        /// <returns>trả về số tiền được cập nhật xuống DB</returns>
        internal int SubtractMoney(int value)
        {
            _Money -= value;

            // update xuống DB
            value = Cashier.SubtractMoneyOfUser(this.Username, value);

            return value;
        }




        

    }
}
