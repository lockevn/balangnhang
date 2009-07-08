using System;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using GURUCORE.Lib.Core.Security.Cryptography;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;

using Quantum.Tala.Service.Authentication;


namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Thread-safe singleton example without using locks
    /// </summary>
    public sealed class Song
    {
        #region Singleton Implementation

        private static readonly Song instance = new Song();

        // Explicit static constructor to tell C# compiler
        // not to mark type as beforefieldinit
        static Song()
        {
        }

        Song()
        {
        }



        /// <summary>
        /// The public Instance property to use
        /// </summary>
        public static Song Instance
        {
            get { return instance; }
        }

        #endregion



        private Dictionary<string, string> _DicValidAuthkey = new Dictionary<string, string>();
        /// <summary>
        /// map (validauthkey - username)
        /// </summary>
        public Dictionary<string, string> DicValidAuthkey
        {
            get { return _DicValidAuthkey; }
            //set { _DicValidAuthkey = value; }
        }

        private Dictionary<string, TalaUser> _DicOnlineUser = new Dictionary<string, TalaUser>();
        /// <summary>
        /// map (username - User object)
        /// </summary>
        public Dictionary<string, TalaUser> DicOnlineUser
        {
            get { return _DicOnlineUser; }
            //set { _DicOnlineUser = value; }
        }





        public string Text { get; set; }

        private Dictionary<string, Soi> _DicSoi = new Dictionary<string, Soi>();
        /// <summary>
        /// map (soiID - Soi object)
        /// </summary>
        public Dictionary<string, Soi> DicSoi
        {
            get { return _DicSoi; }
            //set { _DicSoi = value; }
        }





        /// <summary>
        /// Lục tìm trong Dictionary Soi để tìm với ID đã cho
        /// </summary>
        /// <param name="soiid"></param>
        /// <returns>null nếu không tìm thấy</returns>
        public Soi GetSoiByID(string soiid)
        {
            Soi soiRet;
            DicSoi.TryGetValue(soiid, out soiRet);
            return soiRet;
        }

        /// <summary>
        /// Lục tìm trong Dictionary ValidAuthkey
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns>trả về string.empty nếu không tìm thấy</returns>
        public string GetUsernameByAuthkey(string authkey)
        {
            string ret = string.Empty;
            if (DicValidAuthkey.TryGetValue(authkey, out ret))
            {
                return ret;
            }
            else
            {
                return string.Empty;
            }            
        }

        /// <summary>
        /// Lục tìm trong danh sách những user đang Online (Dictionary OnlineUser)
        /// </summary>
        /// <param name="username"></param>
        /// <returns>trả về null nếu không tìm thấy username đang online</returns>
        public TalaUser GetUserByUsername(string username)
        {
            TalaUser ret;
            DicOnlineUser.TryGetValue(username, out ret);
            return ret;
        }

        /// <summary>
        /// Dùng authkey Tìm username, dùng username tìm User
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns></returns>
        public TalaUser GetUserByAuthkey(string authkey)
        {
            return GetUserByUsername(GetUsernameByAuthkey(authkey));
        }





        /// <summary>
        /// Kiểm tra authentication để cho phép user có được login vào hệ thống hay không.
        /// </summary>
        /// <param name="username">username của user cần login</param>
        /// <param name="password">password của user cần login</param>
        /// <returns></returns>
        public TalaUser LoginVaoSongChoi(string username, string password)
        {
            if (username.IsNullOrEmpty() || password.IsNullOrEmpty())
            {
                return null;
            }
            
            TalaUser user = AuthenticationProvider.Authenticate("quantum", username, password) as TalaUser;

            /// nếu tìm thấy user ở một kho lưu nào đó (DB của Quantum hoặc AuthenStore của đơn vị khác)
            if (user != null && user.Username == username)
            {
                // if found user with username and password, authenticate OK
                if (Song.Instance.DicOnlineUser.ContainsKey(user.Username) == false)
                {
                    // lần đầu, tạo authkey mới, thêm vào các mảng cache
                    // generate new authkey
                    user.Authkey = user.Username + FunctionExtension.GetRandomGUID();

                    // TODO: chỗ này chỉ dùng test, bỏ dòng dưới đi
                    #if DEBUG
                    user.Authkey = user.Username;
                    #endif

                    Song.Instance.DicOnlineUser.Add(user.Username, user);
                    Song.Instance.DicValidAuthkey.Add(user.Authkey, user.Username);
                }
                else
                {
                    // lần login lại, lấy thông tin authenticated cũ ra, trả lại
                    user = Song.Instance.DicOnlineUser[user.Username];
                }
            }
            else
            {
                user = null;
            }

            return user;
        }
                

        /// <summary>
        /// Tạo sới mới, ấn creator vào sới nếu ok. Nếu creator đã có sới rồi, trả lại sới cũ
        /// </summary>
        /// <param name="sName"></param>
        /// <param name="ownerUsername"></param>
        /// <returns></returns>
        public Soi CreatSoiMoi(string sName, string ownerUsername)
        {
            Soi soiRet = null;

            TalaUser user = GetUserByUsername(ownerUsername);
            if (user != null)
            {
                if (user.CurrentSoi == null)
                {
                    // create new
                    soiRet = new Soi(Song.Instance.DicSoi.Count + 1, sName, ownerUsername);
                    Song.Instance.DicSoi.Add(soiRet.Id.ToString(), soiRet);
                    
                    // nhồi luôn người tạo vào sới
                    soiRet.AddPlayer(ownerUsername);
                }
                else
                {
                    // return current Soi
                    soiRet = user.CurrentSoi;
                }
            }           
            
            return soiRet;
        }


        /// <summary>
        /// Xoá một sới theo ID cho trước
        /// </summary>
        /// <param name="sSoiID"></param>
        /// <returns></returns>
        public bool DeleteSoi(string sSoiID)
        {   
            return Song.Instance.DicSoi.Remove(sSoiID);            
        }


        public bool ProcessSoiValidity()
        {
            bool bRet = false;

            // TODO: xử lý các trường hợp đúng sai trong logic game, mỗi khi có một lần gọi đến hàm này
            /// 

            return bRet;
        }
    }
}
