using System;
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

        string _UsernameInGame = string.Empty;
        /// <summary>
        /// Khi sử dụng với các sới pro (paid) thì phải set Property này. Với các tourfree, không cần mask, property này sẽ trả về username bình thường
        /// </summary>        
        [ElementXMLExportAttribute("username", DataOutputXMLType.NestedTag)]
        public string UsernameInGame
        { 
            get{
                return _UsernameInGame.IsNullOrEmpty() ? Username : _UsernameInGame;
            }
            set{
                _UsernameInGame = value;
            } 
        }

        public string Password { get; set; }
        public int Gold { get; set; }

        /// <summary>
        /// thông tin để TalaAPI có thể dựa vào đó, đăng nhập hộ, trừ tiền hộ cho User này ở bên VCoin
        /// </summary>
        public VTCBankCredential BankCredential { get; set; }

        /// <summary>
        /// IP của máy khách mà user này login vào. Ghi lại lúc login
        /// </summary>
        public string IP { get; set; }


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


        /// <summary>
        /// Các tour mà user này đang có vé vào chơi. Danh sách này chỉ ghi nhận các tour, còn về tính valid của tour thì sẽ detect sau, mỗi khi có action
        /// </summary>
        public List<tournamentDTO> AttendingTournament { get; set; }

        public TalaUser()
        {
            AttendingTournament = new List<tournamentDTO>();
        }
        public TalaUser(string p_sUsername, string p_sAuthkey) : this()
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
            Gold += value;
            
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
            Gold -= value;

            // update xuống DB
            value = Cashier.SubtractGoldOfUser(this.Username, value, why, this._CurrentSoi.GetCurrentTournament());

            return value;
        }





        
    }
}
